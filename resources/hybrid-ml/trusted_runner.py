#!/usr/bin/env python3
"""Trusted DataSensei Hybrid ML runner.

This script is mounted read-only and invoked only by Laravel's queue worker.
It never evaluates user code. Inputs are a canonical CSV and validated JSON.
"""

from __future__ import annotations

import json
import math
import os
import platform
import sys
import time
import traceback
import warnings
from pathlib import Path
from typing import Any

os.environ.setdefault("MPLBACKEND", "Agg")
os.environ.setdefault("MPLCONFIGDIR", "/tmp/matplotlib")
os.environ.setdefault("OMP_NUM_THREADS", "1")
os.environ.setdefault("OPENBLAS_NUM_THREADS", "1")
os.environ.setdefault("MKL_NUM_THREADS", "1")
os.environ.setdefault("NUMEXPR_NUM_THREADS", "1")

import joblib
import matplotlib
matplotlib.use("Agg", force=True)
import matplotlib.pyplot as plt
import numpy as np
import pandas as pd
import sklearn
from sklearn.base import clone
from sklearn.cluster import KMeans
from sklearn.compose import ColumnTransformer
from sklearn.ensemble import GradientBoostingClassifier, GradientBoostingRegressor, RandomForestClassifier, RandomForestRegressor
from sklearn.impute import SimpleImputer
from sklearn.inspection import permutation_importance
from sklearn.linear_model import LinearRegression, LogisticRegression
from sklearn.metrics import (
    accuracy_score,
    average_precision_score,
    confusion_matrix,
    f1_score,
    mean_absolute_error,
    mean_squared_error,
    precision_recall_curve,
    precision_score,
    r2_score,
    recall_score,
    roc_auc_score,
    roc_curve,
    silhouette_score,
)
from sklearn.model_selection import KFold, StratifiedKFold, cross_val_score, learning_curve, train_test_split, validation_curve
from sklearn.naive_bayes import GaussianNB
from sklearn.neighbors import KNeighborsClassifier
from sklearn.pipeline import Pipeline
from sklearn.preprocessing import OneHotEncoder, StandardScaler, label_binarize
from sklearn.svm import SVC
from sklearn.tree import DecisionTreeClassifier, DecisionTreeRegressor

warnings.filterwarnings("ignore", category=UserWarning)
warnings.filterwarnings("ignore", category=RuntimeWarning)


def sanitize(value: Any) -> Any:
    if isinstance(value, dict):
        return {str(key): sanitize(item) for key, item in value.items()}
    if isinstance(value, (list, tuple, np.ndarray, pd.Series, pd.Index)):
        return [sanitize(item) for item in list(value)]
    if isinstance(value, (np.integer,)):
        return int(value)
    if isinstance(value, (np.floating, float)):
        number = float(value)
        if math.isnan(number) or math.isinf(number):
            return None
        return round(number, 8)
    if isinstance(value, (np.bool_, bool)):
        return bool(value)
    if value is pd.NA:
        return None
    return value


def write_json(path: Path, payload: dict[str, Any]) -> None:
    path.write_text(json.dumps(sanitize(payload), ensure_ascii=False, separators=(",", ":")), encoding="utf-8")


def update_progress(output_dir: Path, progress: int, stage: str) -> None:
    write_json(output_dir / "progress.json", {"progress": int(progress), "stage": stage, "updated_at": time.time()})


def load_config(path: Path) -> dict[str, Any]:
    with path.open("r", encoding="utf-8") as handle:
        payload = json.load(handle)
    if not isinstance(payload, dict):
        raise ValueError("The training configuration is invalid.")
    return payload


def load_frame(path: Path) -> pd.DataFrame:
    frame = pd.read_csv(path, low_memory=False)
    if frame.empty:
        raise ValueError("The dataset contains no data rows.")
    frame.columns = [str(column).strip() for column in frame.columns]
    frame = frame.replace([np.inf, -np.inf], np.nan)
    return frame


def split_types(frame: pd.DataFrame, features: list[str]) -> tuple[list[str], list[str]]:
    numeric = [column for column in features if pd.api.types.is_numeric_dtype(frame[column])]
    categorical = [column for column in features if column not in numeric]
    return numeric, categorical


def make_preprocessor(frame: pd.DataFrame, features: list[str], config: dict[str, Any], numeric_only: bool = False):
    numeric, categorical = split_types(frame, features)
    if numeric_only and categorical:
        raise ValueError("K-Means accepts numeric features only.")
    preprocessing = config.get("preprocessing", {})
    numeric_steps: list[tuple[str, Any]] = [
        ("imputer", SimpleImputer(strategy=preprocessing.get("numeric_imputation", "median")))
    ]
    if bool(preprocessing.get("apply_scaling", False)):
        numeric_steps.append(("scaler", StandardScaler()))

    transformers: list[tuple[str, Any, list[str]]] = []
    if numeric:
        transformers.append(("numeric", Pipeline(numeric_steps), numeric))
    if categorical:
        transformers.append((
            "categorical",
            Pipeline([
                ("imputer", SimpleImputer(strategy="most_frequent")),
                ("encoder", OneHotEncoder(handle_unknown="ignore", sparse_output=False)),
            ]),
            categorical,
        ))
    if not transformers:
        raise ValueError("No usable feature columns remain after preprocessing.")
    return ColumnTransformer(transformers=transformers, remainder="drop"), numeric, categorical


def optional_depth(value: Any, default: int) -> int | None:
    """A depth of 0 (or less) means "no limit" so a search can try unrestricted trees."""
    depth = int(value if value is not None else default)
    return depth if depth > 0 else None


def classifier(config: dict[str, Any], train_rows: int):
    key = config["algorithm_key"]
    params = config.get("parameters", {})
    seed = int(config.get("random_state", 42))
    if key == "logistic_regression":
        return LogisticRegression(
            C=float(params.get("c", 1.0)),
            max_iter=int(params.get("max_iter", 1000)),
            random_state=seed,
            class_weight="balanced" if bool(config.get("class_weight_balanced", False)) else None,
        )
    if key == "decision_tree":
        return DecisionTreeClassifier(
            max_depth=int(params.get("max_depth", 6)),
            min_samples_split=int(params.get("min_samples_split", 2)),
            random_state=seed,
            class_weight="balanced" if bool(config.get("class_weight_balanced", False)) else None,
        )
    if key == "random_forest":
        return RandomForestClassifier(
            n_estimators=int(params.get("n_estimators", 150)),
            max_depth=optional_depth(params.get("max_depth"), 10),
            min_samples_split=int(params.get("min_samples_split", 2)),
            random_state=seed,
            class_weight="balanced" if bool(config.get("class_weight_balanced", False)) else None,
            n_jobs=1,
        )
    if key == "gradient_boosting":
        return GradientBoostingClassifier(
            n_estimators=int(params.get("n_estimators", 100)),
            learning_rate=float(params.get("learning_rate", 0.1)),
            max_depth=int(params.get("max_depth", 3)),
            random_state=seed,
        )
    if key == "knn":
        neighbors = max(1, min(int(params.get("n_neighbors", 5)), max(1, train_rows - 1)))
        return KNeighborsClassifier(n_neighbors=neighbors, weights=str(params.get("weights", "uniform")))
    if key == "naive_bayes":
        return GaussianNB(var_smoothing=float(params.get("var_smoothing", 1.0e-9)))
    if key == "svm":
        return SVC(
            C=float(params.get("c", 1.0)),
            kernel=str(params.get("kernel", "rbf")),
            gamma=str(params.get("gamma", "scale")),
            probability=True,
            random_state=seed,
            class_weight="balanced" if bool(config.get("class_weight_balanced", False)) else None,
        )
    raise ValueError("Unsupported classification algorithm.")


def regressor(config: dict[str, Any]):
    key = config["algorithm_key"]
    params = config.get("parameters", {})
    seed = int(config.get("random_state", 42))
    if key == "linear_regression":
        return LinearRegression()
    if key == "random_forest_regressor":
        return RandomForestRegressor(
            n_estimators=int(params.get("n_estimators", 150)),
            max_depth=optional_depth(params.get("max_depth"), 12),
            min_samples_split=int(params.get("min_samples_split", 2)),
            random_state=seed,
            n_jobs=1,
        )
    if key == "gradient_boosting_regressor":
        return GradientBoostingRegressor(
            n_estimators=int(params.get("n_estimators", 100)),
            learning_rate=float(params.get("learning_rate", 0.1)),
            max_depth=int(params.get("max_depth", 3)),
            random_state=seed,
        )
    if key == "decision_tree_regressor":
        return DecisionTreeRegressor(
            max_depth=int(params.get("max_depth", 8)),
            min_samples_split=int(params.get("min_samples_split", 2)),
            random_state=seed,
        )
    raise ValueError("Unsupported regression algorithm.")


def transformed_feature_names(pipeline: Pipeline) -> list[str]:
    try:
        names = pipeline.named_steps["preprocessor"].get_feature_names_out()
        return [str(name).replace("numeric__", "").replace("categorical__", "") for name in names]
    except Exception:
        return []


def feature_importance(pipeline: Pipeline, X_test: pd.DataFrame, y_test: pd.Series | None, problem_type: str) -> list[dict[str, Any]]:
    model = pipeline.named_steps["model"]
    transformed_names = transformed_feature_names(pipeline)
    values = None
    names = transformed_names
    kind = "importance"

    if hasattr(model, "feature_importances_"):
        values = np.asarray(model.feature_importances_, dtype=float)
        kind = "native_importance"
    elif hasattr(model, "coef_"):
        coefficients = np.asarray(model.coef_, dtype=float)
        values = np.mean(np.abs(coefficients), axis=0) if coefficients.ndim > 1 else np.abs(coefficients)
        kind = "coefficient_magnitude"

    if values is None or len(values) != len(names):
        if y_test is None or len(X_test) < 5:
            return []
        scoring = "f1_weighted" if problem_type == "classification" else "neg_root_mean_squared_error"
        try:
            permutation = permutation_importance(
                pipeline,
                X_test.iloc[: min(800, len(X_test))],
                y_test.iloc[: min(800, len(y_test))],
                scoring=scoring,
                n_repeats=4,
                random_state=42,
                n_jobs=1,
            )
            names = [str(column) for column in X_test.columns]
            values = np.asarray(permutation.importances_mean, dtype=float)
            kind = "permutation_importance"
        except Exception:
            return []

    pairs = sorted(zip(names, values.tolist()), key=lambda pair: abs(float(pair[1])), reverse=True)[:20]
    return [{"feature": name, "value": float(value), "kind": kind} for name, value in pairs]


def save_current_figure(path: Path) -> None:
    plt.tight_layout()
    plt.savefig(path, dpi=140, bbox_inches="tight")
    plt.close()


def plot_feature_importance(items: list[dict[str, Any]], path: Path) -> bool:
    if not items:
        return False
    top = items[:15][::-1]
    plt.figure(figsize=(8, max(4, len(top) * 0.34)))
    plt.barh([item["feature"] for item in top], [abs(float(item["value"])) for item in top])
    plt.xlabel("Influence")
    plt.title("Feature Importance")
    save_current_figure(path)
    return True


def plot_missing_values(frame: pd.DataFrame, path: Path) -> bool:
    missing = frame.isna().mean().sort_values(ascending=False)
    missing = missing[missing > 0]
    if missing.empty:
        return False
    plt.figure(figsize=(8, max(4, len(missing) * 0.30)))
    plt.barh(missing.index[::-1], (missing.values[::-1] * 100))
    plt.xlabel("Missing values (%)")
    plt.title("Missing Values by Column")
    save_current_figure(path)
    return True


def plot_correlation(frame: pd.DataFrame, path: Path) -> bool:
    numeric = frame.select_dtypes(include=[np.number]).iloc[:, :20]
    if numeric.shape[1] < 2:
        return False
    correlation = numeric.corr(numeric_only=True)
    plt.figure(figsize=(min(12, 3 + correlation.shape[1] * 0.55), min(10, 3 + correlation.shape[1] * 0.5)))
    image = plt.imshow(correlation.values, vmin=-1, vmax=1, cmap="coolwarm")
    plt.colorbar(image, fraction=0.046, pad=0.04)
    plt.xticks(range(len(correlation.columns)), correlation.columns, rotation=70, ha="right", fontsize=8)
    plt.yticks(range(len(correlation.columns)), correlation.columns, fontsize=8)
    plt.title("Correlation Heatmap")
    save_current_figure(path)
    return True


def plot_missing_heatmap(frame: pd.DataFrame, path: Path) -> bool:
    missing = frame.isna()
    if not bool(missing.to_numpy().any()):
        return False
    sample = missing.iloc[:200, :30].T
    plt.figure(figsize=(10, max(4, sample.shape[0] * 0.28)))
    plt.imshow(sample.values, aspect="auto", interpolation="nearest", cmap="binary")
    plt.yticks(range(len(sample.index)), sample.index, fontsize=8)
    plt.xlabel("Sampled rows")
    plt.title("Missing Value Heatmap")
    save_current_figure(path)
    return True


def plot_distributions(frame: pd.DataFrame, path: Path) -> bool:
    numeric = frame.select_dtypes(include=[np.number]).iloc[:, :6]
    if numeric.empty:
        return False
    columns = list(numeric.columns)
    rows = int(math.ceil(len(columns) / 2))
    figure, axes = plt.subplots(rows, 2, figsize=(10, max(4, rows * 3.2)))
    axes_array = np.atleast_1d(axes).reshape(-1)
    for index, column in enumerate(columns):
        values = pd.to_numeric(numeric[column], errors="coerce").dropna()
        axes_array[index].hist(values, bins=min(30, max(8, int(math.sqrt(max(1, len(values)))))))
        axes_array[index].set_title(str(column))
        axes_array[index].set_ylabel("Frequency")
    for index in range(len(columns), len(axes_array)):
        axes_array[index].axis("off")
    figure.suptitle("Numeric Feature Distributions")
    save_current_figure(path)
    return True


def validation_parameter(config: dict[str, Any]):
    key = config.get("algorithm_key")
    if key in {"gradient_boosting", "gradient_boosting_regressor"}:
        return "model__learning_rate", [0.01, 0.03, 0.1, 0.3, 0.7], "Learning rate"
    if key in {"decision_tree", "decision_tree_regressor", "random_forest", "random_forest_regressor"}:
        return "model__max_depth", [2, 4, 6, 10, 15, 25], "Maximum depth"
    if key == "knn":
        return "model__n_neighbors", [1, 3, 5, 9, 15], "Neighbors"
    if key in {"logistic_regression", "svm"}:
        return "model__C", [0.01, 0.1, 1.0, 10.0, 100.0], "C"
    if key == "naive_bayes":
        return "model__var_smoothing", [1e-12, 1e-10, 1e-9, 1e-8, 1e-6], "Variance smoothing"
    return None


def plot_validation(pipeline: Pipeline, X: pd.DataFrame, y: pd.Series, problem_type: str, path: Path, seed: int, config: dict[str, Any]) -> bool:
    parameter = validation_parameter(config)
    if parameter is None:
        return False
    parameter_name, parameter_range, label = parameter
    try:
        if len(X) > 3000:
            sampled = X.sample(n=3000, random_state=seed)
            y_sampled = y.loc[sampled.index]
        else:
            sampled = X
            y_sampled = y
        if problem_type == "classification":
            minimum_class = int(y_sampled.value_counts().min())
            folds = min(3, minimum_class)
            if folds < 2:
                return False
            cv = StratifiedKFold(n_splits=folds, shuffle=True, random_state=seed)
            scoring = "f1_weighted"
        else:
            folds = min(3, max(2, len(y_sampled) // 20))
            cv = KFold(n_splits=folds, shuffle=True, random_state=seed)
            scoring = "neg_root_mean_squared_error"
        train_scores, validation_scores = validation_curve(
            clone(pipeline), sampled, y_sampled,
            param_name=parameter_name, param_range=parameter_range,
            cv=cv, scoring=scoring, n_jobs=1,
        )
        plt.figure(figsize=(7, 4.5))
        plt.plot(parameter_range, np.mean(train_scores, axis=1), marker="o", label="Training")
        plt.plot(parameter_range, np.mean(validation_scores, axis=1), marker="o", label="Validation")
        if all(float(value) > 0 for value in parameter_range) and max(parameter_range) / min(parameter_range) >= 1000:
            plt.xscale("log")
        plt.xlabel(label)
        plt.ylabel("F1 score" if problem_type == "classification" else "Negative RMSE")
        plt.title("Validation Curve")
        plt.legend()
        save_current_figure(path)
        return True
    except Exception:
        plt.close("all")
        return False


def plot_learning(pipeline: Pipeline, X: pd.DataFrame, y: pd.Series, problem_type: str, path: Path, seed: int) -> bool:
    try:
        if problem_type == "classification":
            min_class = int(y.value_counts().min())
            folds = max(2, min(3, min_class))
            cv = StratifiedKFold(n_splits=folds, shuffle=True, random_state=seed)
            scoring = "f1_weighted"
        else:
            folds = min(3, max(2, len(y) // 20))
            cv = KFold(n_splits=folds, shuffle=True, random_state=seed)
            scoring = "neg_root_mean_squared_error"
        train_sizes, train_scores, validation_scores = learning_curve(
            clone(pipeline), X, y, cv=cv, scoring=scoring,
            train_sizes=np.array([0.55, 0.70, 0.85, 1.0]), n_jobs=1,
        )
        plt.figure(figsize=(7, 4.5))
        plt.plot(train_sizes, np.mean(train_scores, axis=1), marker="o", label="Training")
        plt.plot(train_sizes, np.mean(validation_scores, axis=1), marker="o", label="Validation")
        plt.xlabel("Training rows")
        plt.ylabel("F1 score" if problem_type == "classification" else "Negative RMSE")
        plt.title("Learning Curve")
        plt.legend()
        save_current_figure(path)
        return True
    except Exception:
        plt.close("all")
        return False


def prediction_schema(frame: pd.DataFrame, features: list[str]) -> dict[str, Any]:
    schema: dict[str, Any] = {}
    for feature in features:
        series = frame[feature]
        if pd.api.types.is_numeric_dtype(series):
            values = pd.to_numeric(series, errors="coerce").dropna()
            whole = bool(not values.empty and np.all(np.equal(np.mod(values, 1), 0)))
            distinct = sorted(set(values.tolist()))
            schema[feature] = {
                "type": "number",
                "minimum": float(values.min()) if not values.empty else None,
                "maximum": float(values.max()) if not values.empty else None,
                "median": float(values.median()) if not values.empty else None,
                "low": float(values.quantile(0.25)) if not values.empty else None,
                "high": float(values.quantile(0.75)) if not values.empty else None,
                "whole": whole,
                # A handful of whole-number codes (0/1, 1/2/3) reads better as a short list.
                "choices": [int(item) for item in distinct] if whole and 0 < len(distinct) <= 10 else None,
            }
        else:
            values = [str(value) for value in series.dropna().astype(str).value_counts().head(50).index.tolist()]
            schema[feature] = {"type": "category", "options": values}
    return schema


def cross_validation_metrics(pipeline: Pipeline, X: pd.DataFrame, y: pd.Series, problem_type: str, requested: int, seed: int) -> dict[str, Any]:
    if requested <= 1:
        return {"folds": 0, "mean": None, "std": None, "scoring": None}
    if problem_type == "classification":
        min_class = int(y.value_counts().min())
        folds = min(requested, min_class)
        if folds < 2:
            return {"folds": 0, "mean": None, "std": None, "scoring": "f1_weighted"}
        cv = StratifiedKFold(n_splits=folds, shuffle=True, random_state=seed)
        scoring = "f1_weighted"
    else:
        folds = min(requested, max(2, len(y) // 10))
        if folds < 2:
            return {"folds": 0, "mean": None, "std": None, "scoring": "neg_root_mean_squared_error"}
        cv = KFold(n_splits=folds, shuffle=True, random_state=seed)
        scoring = "neg_root_mean_squared_error"
    scores = cross_val_score(clone(pipeline), X, y, cv=cv, scoring=scoring, n_jobs=1)
    return {"folds": folds, "mean": float(scores.mean()), "std": float(scores.std()), "scoring": scoring}


# --------------------------------------------------------------------------
# Automatic model search
#
# Every candidate is scored with cross-validation on the TRAINING rows only.
# The held-out test rows are never looked at while choosing, so the reported
# test score stays an honest estimate.
# --------------------------------------------------------------------------

AUTO_KEYS = {"auto_classification": "classification", "auto_regression": "regression"}

# Ordered from simplest to most complex. When two candidates score about the
# same, the earlier (simpler, easier to explain) one wins.
AUTO_CANDIDATES = {
    "classification": ["logistic_regression", "decision_tree", "knn", "random_forest", "gradient_boosting", "svm"],
    "regression": ["linear_regression", "decision_tree_regressor", "random_forest_regressor", "gradient_boosting_regressor"],
}

ALGORITHM_LABELS = {
    "logistic_regression": "Logistic Regression",
    "decision_tree": "Decision Tree",
    "random_forest": "Random Forest",
    "gradient_boosting": "Gradient Boosting",
    "knn": "K-Nearest Neighbors",
    "naive_bayes": "Naive Bayes",
    "svm": "Support Vector Machine",
    "linear_regression": "Linear Regression",
    "decision_tree_regressor": "Decision Tree Regressor",
    "random_forest_regressor": "Random Forest Regressor",
    "gradient_boosting_regressor": "Gradient Boosting Regressor",
}

SCALE_SENSITIVE = {"logistic_regression", "knn", "svm", "linear_regression"}

# Candidates within this many score points of the best count as a tie.
TIE_MARGIN = 0.005


def parameter_grid(key: str) -> list[dict[str, Any]]:
    """A deliberately small set of settings to try for one algorithm."""
    grids: dict[str, list[dict[str, Any]]] = {
        "logistic_regression": [{"c": 0.1}, {"c": 1.0}, {"c": 10.0}, {"c": 100.0}],
        "decision_tree": [{"max_depth": 3}, {"max_depth": 5}, {"max_depth": 8}, {"max_depth": 12}],
        "random_forest": [
            {"n_estimators": 200, "max_depth": 6},
            {"n_estimators": 200, "max_depth": 12},
            {"n_estimators": 200, "max_depth": 0},
        ],
        "gradient_boosting": [
            {"n_estimators": 100, "learning_rate": 0.1, "max_depth": 3},
            {"n_estimators": 200, "learning_rate": 0.05, "max_depth": 3},
            {"n_estimators": 150, "learning_rate": 0.1, "max_depth": 2},
        ],
        "knn": [
            {"n_neighbors": 3, "weights": "distance"},
            {"n_neighbors": 5, "weights": "distance"},
            {"n_neighbors": 9, "weights": "distance"},
            {"n_neighbors": 15, "weights": "distance"},
        ],
        "naive_bayes": [{}],
        "svm": [
            {"c": 0.5, "kernel": "rbf", "gamma": "scale"},
            {"c": 2.0, "kernel": "rbf", "gamma": "scale"},
            {"c": 10.0, "kernel": "rbf", "gamma": "scale"},
        ],
        "linear_regression": [{}],
        "decision_tree_regressor": [{"max_depth": 3}, {"max_depth": 5}, {"max_depth": 8}, {"max_depth": 12}],
        "random_forest_regressor": [
            {"n_estimators": 200, "max_depth": 8},
            {"n_estimators": 200, "max_depth": 14},
            {"n_estimators": 200, "max_depth": 0},
        ],
        "gradient_boosting_regressor": [
            {"n_estimators": 100, "learning_rate": 0.1, "max_depth": 3},
            {"n_estimators": 200, "learning_rate": 0.05, "max_depth": 3},
            {"n_estimators": 150, "learning_rate": 0.1, "max_depth": 2},
        ],
    }
    return grids.get(key, [{}])


def should_balance_classes(config: dict[str, Any], y: pd.Series) -> bool:
    """Rare classes are only weighted up when the configuration asks for it.

    Weighting trades overall accuracy for fewer missed rare cases, so it is an
    explicit choice rather than something that silently lowers the headline score.
    """
    return config.get("class_weight_balanced", False) is True


def build_pipeline(frame: pd.DataFrame, features: list[str], config: dict[str, Any], key: str, params: dict[str, Any], problem_type: str, train_rows: int) -> Pipeline:
    candidate = dict(config)
    candidate["algorithm_key"] = key
    candidate["parameters"] = params
    preprocessing = dict(config.get("preprocessing", {}))
    if str(preprocessing.get("scale_mode", "auto")) == "auto":
        preprocessing["apply_scaling"] = key in SCALE_SENSITIVE
    candidate["preprocessing"] = preprocessing
    preprocessor, _, _ = make_preprocessor(frame, features, candidate)
    model = classifier(candidate, train_rows) if problem_type == "classification" else regressor(candidate)
    return Pipeline([("preprocessor", preprocessor), ("model", model)])


def search_best_model(
    frame: pd.DataFrame,
    features: list[str],
    config: dict[str, Any],
    X_train: pd.DataFrame,
    y_train: pd.Series,
    problem_type: str,
    output_dir: Path,
) -> tuple[str, dict[str, Any], dict[str, Any]]:
    """Return (algorithm key, parameters, search report)."""
    requested = str(config["algorithm_key"])
    automatic = requested in AUTO_KEYS
    keys = list(AUTO_CANDIDATES[problem_type]) if automatic else [requested]
    seed = int(config.get("random_state", 42))
    budget = float(config.get("search_budget_seconds", 90))

    X_search, y_search = X_train, y_train
    if len(X_search) > 4000:
        X_search = X_train.sample(n=4000, random_state=seed)
        y_search = y_train.loc[X_search.index]

    if problem_type == "classification":
        smallest = int(y_search.value_counts().min())
        folds = min(5 if len(X_search) < 2000 else 3, smallest)
        if folds < 2:
            folds = 0
        else:
            cv = StratifiedKFold(n_splits=folds, shuffle=True, random_state=seed)
        scoring = "accuracy"
    else:
        folds = min(5 if len(X_search) < 2000 else 3, max(2, len(y_search) // 10))
        cv = KFold(n_splits=folds, shuffle=True, random_state=seed)
        scoring = "r2"

    if automatic and len(X_search) > 3000 and "svm" in keys:
        keys.remove("svm")  # too slow for the classroom time limit

    user_params = dict(config.get("parameters", {}))
    report: dict[str, Any] = {
        "mode": "automatic" if automatic else "tuned",
        "scoring": scoring,
        "folds": folds,
        "candidates": [],
        "stopped_early": False,
    }
    if folds < 2:
        # Too few rows per class to compare fairly; fall back to a safe default.
        fallback = keys[0]
        return fallback, (parameter_grid(fallback)[len(parameter_grid(fallback)) // 2] if automatic else user_params), report

    started = time.perf_counter()
    results: list[tuple[int, str, dict[str, Any], float]] = []
    for index, key in enumerate(keys):
        if results and (time.perf_counter() - started) > budget:
            report["stopped_early"] = True
            break
        update_progress(
            output_dir,
            20 + int(14 * index / max(1, len(keys))),
            f"Trying {ALGORITHM_LABELS.get(key, key)} ({index + 1} of {len(keys)})" if automatic else f"Fine-tuning {ALGORITHM_LABELS.get(key, key)}",
        )
        grid = parameter_grid(key)
        if not automatic and user_params:
            grid = [user_params] + [item for item in grid if item != user_params]
        best_score = -math.inf
        best_params: dict[str, Any] = grid[0]
        for params in grid:
            merged = {**user_params, **params} if not automatic else params
            try:
                pipeline = build_pipeline(frame, features, config, key, merged, problem_type, len(X_search))
                scores = cross_val_score(pipeline, X_search, y_search, cv=cv, scoring=scoring, n_jobs=1)
                score = float(np.mean(scores))
            except Exception:
                continue
            if math.isfinite(score) and score > best_score + 1e-9:
                best_score, best_params = score, merged
            if (time.perf_counter() - started) > budget and math.isfinite(best_score):
                report["stopped_early"] = True
                break
        if math.isfinite(best_score):
            results.append((index, key, best_params, best_score))
            report["candidates"].append({
                "algorithm_key": key,
                "label": ALGORITHM_LABELS.get(key, key),
                "score": best_score * 100 if scoring == "accuracy" else best_score,
                "parameters": best_params,
            })

    if not results:
        fallback = keys[0]
        return fallback, user_params, report

    top = max(score for _, _, _, score in results)
    # Simplest candidate that is practically as good as the best one.
    _, chosen_key, chosen_params, chosen_score = next(item for item in results if item[3] >= top - TIE_MARGIN)
    for entry in report["candidates"]:
        entry["selected"] = entry["algorithm_key"] == chosen_key
    report["selected_algorithm"] = chosen_key
    report["selected_label"] = ALGORITHM_LABELS.get(chosen_key, chosen_key)
    report["selected_score"] = chosen_score * 100 if scoring == "accuracy" else chosen_score
    report["search_seconds"] = round(time.perf_counter() - started, 2)
    return chosen_key, chosen_params, report


def train_supervised(frame: pd.DataFrame, config: dict[str, Any], output_dir: Path):
    problem_type = config["problem_type"]
    features = list(config["features"])
    target = str(config["target_column"])
    missing = [column for column in features + [target] if column not in frame.columns]
    if missing:
        raise ValueError("Missing required columns: " + ", ".join(missing))

    working = frame[features + [target]].copy()
    original_rows = len(working)
    if bool(config.get("preprocessing", {}).get("remove_duplicates", True)):
        working = working.drop_duplicates()
    duplicates_removed = original_rows - len(working)
    before_target_drop = len(working)
    working = working.dropna(subset=[target])
    dropped_target_rows = before_target_drop - len(working)
    if len(working) < 20:
        raise ValueError("At least 20 usable rows are required after cleaning.")

    X = working[features].copy()
    y = working[target].copy()
    if problem_type == "classification":
        y = y.astype(str)
        if y.nunique() < 2 or y.nunique() > 100:
            raise ValueError("Classification requires between 2 and 100 target classes.")
    else:
        y = pd.to_numeric(y, errors="coerce")
        valid = y.notna()
        X = X.loc[valid]
        y = y.loc[valid].astype(float)
        if len(y) < 20:
            raise ValueError("Regression requires at least 20 rows with a numeric target.")

    seed = int(config.get("random_state", 42))
    test_size = float(config.get("test_size", 0.20))
    stratify = None
    if problem_type == "classification":
        counts = y.value_counts()
        estimated_test = max(1, int(math.ceil(len(y) * test_size)))
        if int(counts.min()) >= 2 and estimated_test >= int(y.nunique()):
            stratify = y

    X_train, X_test, y_train, y_test = train_test_split(
        X, y, test_size=test_size, random_state=seed, stratify=stratify
    )
    config = dict(config)
    if problem_type == "classification":
        config["class_weight_balanced"] = should_balance_classes(config, y_train)

    search_report: dict[str, Any] | None = None
    requested_algorithm = str(config["algorithm_key"])
    if requested_algorithm in AUTO_KEYS or bool(config.get("tune", False)):
        if requested_algorithm in AUTO_KEYS and AUTO_KEYS[requested_algorithm] != problem_type:
            raise ValueError("The automatic model choice does not match the problem type.")
        chosen_key, chosen_params, search_report = search_best_model(
            working, features, config, X_train, y_train, problem_type, output_dir
        )
        config["algorithm_key"] = chosen_key
        config["parameters"] = chosen_params
        preprocessing = dict(config.get("preprocessing", {}))
        if str(preprocessing.get("scale_mode", "auto")) == "auto":
            preprocessing["apply_scaling"] = chosen_key in SCALE_SENSITIVE
        config["preprocessing"] = preprocessing

    preprocessor, numeric, categorical = make_preprocessor(working, features, config)
    model = classifier(config, len(X_train)) if problem_type == "classification" else regressor(config)
    pipeline = Pipeline([("preprocessor", preprocessor), ("model", model)])

    update_progress(output_dir, 35, "Training " + ALGORITHM_LABELS.get(str(config["algorithm_key"]), "the selected algorithm"))
    started = time.perf_counter()
    pipeline.fit(X_train, y_train)
    training_ms = int((time.perf_counter() - started) * 1000)
    predicted = pipeline.predict(X_test)

    update_progress(output_dir, 58, "Calculating evaluation metrics")
    charts: dict[str, str] = {}
    metrics: dict[str, Any]
    if problem_type == "classification":
        labels = [str(label) for label in pipeline.named_steps["model"].classes_]
        matrix = confusion_matrix(y_test.astype(str), predicted.astype(str), labels=labels)
        metrics = {
            "accuracy": accuracy_score(y_test, predicted) * 100,
            "precision": precision_score(y_test, predicted, average="weighted", zero_division=0) * 100,
            "recall": recall_score(y_test, predicted, average="weighted", zero_division=0) * 100,
            "f1": f1_score(y_test, predicted, average="weighted", zero_division=0) * 100,
            "roc_auc": None,
            "average_precision": None,
            "confusion_matrix": {"labels": labels, "values": matrix.tolist()},
        }
        # What "always answer the most common class" would score on the same test rows.
        majority_label = str(y_train.value_counts().idxmax())
        metrics["baseline_accuracy"] = float((y_test.astype(str) == majority_label).mean()) * 100
        metrics["baseline_label"] = majority_label

        probabilities = pipeline.predict_proba(X_test) if hasattr(pipeline, "predict_proba") else None
        if probabilities is not None:
            try:
                if len(labels) == 2:
                    positive = probabilities[:, 1]
                    binary = (y_test.astype(str) == labels[1]).astype(int)
                    metrics["roc_auc"] = roc_auc_score(binary, positive) * 100
                    metrics["average_precision"] = average_precision_score(binary, positive) * 100
                    fpr, tpr, _ = roc_curve(binary, positive)
                    precision_curve, recall_curve, _ = precision_recall_curve(binary, positive)

                    plt.figure(figsize=(6, 5))
                    plt.plot(fpr, tpr, label=f"AUC {metrics['roc_auc'] / 100:.3f}")
                    plt.plot([0, 1], [0, 1], linestyle="--")
                    plt.xlabel("False Positive Rate")
                    plt.ylabel("True Positive Rate")
                    plt.title("ROC Curve")
                    plt.legend()
                    save_current_figure(output_dir / "roc_curve.png")
                    charts["roc_curve"] = "roc_curve.png"

                    plt.figure(figsize=(6, 5))
                    plt.plot(recall_curve, precision_curve, label=f"AP {metrics['average_precision'] / 100:.3f}")
                    plt.xlabel("Recall")
                    plt.ylabel("Precision")
                    plt.title("Precision-Recall Curve")
                    plt.legend()
                    save_current_figure(output_dir / "precision_recall_curve.png")
                    charts["precision_recall_curve"] = "precision_recall_curve.png"
                else:
                    binary = label_binarize(y_test.astype(str), classes=labels)
                    metrics["roc_auc"] = roc_auc_score(binary, probabilities, multi_class="ovr", average="weighted") * 100
            except Exception:
                pass

        plt.figure(figsize=(max(6, len(labels) * 0.8), max(5, len(labels) * 0.65)))
        plt.imshow(matrix, cmap="Blues")
        plt.colorbar()
        plt.xticks(range(len(labels)), labels, rotation=45, ha="right")
        plt.yticks(range(len(labels)), labels)
        plt.xlabel("Predicted")
        plt.ylabel("Actual")
        plt.title("Confusion Matrix")
        for i in range(matrix.shape[0]):
            for j in range(matrix.shape[1]):
                plt.text(j, i, str(matrix[i, j]), ha="center", va="center")
        save_current_figure(output_dir / "confusion_matrix.png")
        charts["confusion_matrix"] = "confusion_matrix.png"

        counts = y.value_counts().sort_index()
        plt.figure(figsize=(7, 4.5))
        plt.bar([str(index) for index in counts.index], counts.values)
        plt.xticks(rotation=35, ha="right")
        plt.ylabel("Rows")
        plt.title("Class Distribution")
        save_current_figure(output_dir / "class_distribution.png")
        charts["class_distribution"] = "class_distribution.png"
    else:
        mse = mean_squared_error(y_test, predicted)
        metrics = {
            "mae": mean_absolute_error(y_test, predicted),
            "mse": mse,
            "rmse": math.sqrt(mse),
            "r2": r2_score(y_test, predicted),
        }
        # What "always answer the average" would miss by on the same test rows.
        metrics["baseline_mae"] = float(mean_absolute_error(y_test, np.full(len(y_test), float(y_train.mean()))))
        metrics["target_mean"] = float(y_train.mean())
        plt.figure(figsize=(6, 5))
        plt.scatter(y_test, predicted, alpha=0.65)
        minimum = min(float(np.min(y_test)), float(np.min(predicted)))
        maximum = max(float(np.max(y_test)), float(np.max(predicted)))
        plt.plot([minimum, maximum], [minimum, maximum], linestyle="--")
        plt.xlabel("Actual")
        plt.ylabel("Predicted")
        plt.title("Actual vs Predicted")
        save_current_figure(output_dir / "actual_vs_predicted.png")
        charts["actual_vs_predicted"] = "actual_vs_predicted.png"

        residuals = np.asarray(y_test, dtype=float) - np.asarray(predicted, dtype=float)
        plt.figure(figsize=(6, 5))
        plt.scatter(predicted, residuals, alpha=0.65)
        plt.axhline(0, linestyle="--")
        plt.xlabel("Predicted")
        plt.ylabel("Residual")
        plt.title("Residual Plot")
        save_current_figure(output_dir / "residual_plot.png")
        charts["residual_plot"] = "residual_plot.png"

    update_progress(output_dir, 72, "Generating explainability and learning charts")
    importance = feature_importance(pipeline, X_test, y_test, problem_type)
    if plot_feature_importance(importance, output_dir / "feature_importance.png"):
        charts["feature_importance"] = "feature_importance.png"
    if bool(config.get("generate_extended_charts", True)):
        if plot_learning(pipeline, X, y, problem_type, output_dir / "learning_curve.png", seed):
            charts["learning_curve"] = "learning_curve.png"
        if plot_correlation(working, output_dir / "correlation_heatmap.png"):
            charts["correlation_heatmap"] = "correlation_heatmap.png"
        if plot_missing_values(working, output_dir / "missing_values.png"):
            charts["missing_values"] = "missing_values.png"
        if plot_missing_heatmap(working, output_dir / "missing_value_heatmap.png"):
            charts["missing_value_heatmap"] = "missing_value_heatmap.png"
        if plot_distributions(working, output_dir / "distribution_plots.png"):
            charts["distribution_plots"] = "distribution_plots.png"
        if plot_validation(pipeline, X, y, problem_type, output_dir / "validation_curve.png", seed, config):
            charts["validation_curve"] = "validation_curve.png"

    cv = cross_validation_metrics(pipeline, X, y, problem_type, int(config.get("cross_validation", 0)), seed)
    metrics["cross_validation"] = cv
    if search_report is not None:
        metrics["model_search"] = search_report
    summary = {
        "rows_total": int(len(frame)),
        "rows_used": int(len(working)),
        "train_rows": int(len(X_train)),
        "test_rows": int(len(X_test)),
        "duplicates_removed": int(duplicates_removed),
        "dropped_target_rows": int(dropped_target_rows),
        "feature_count": len(features),
        "numeric_features": numeric,
        "categorical_features": categorical,
        "transformed_feature_count": len(transformed_feature_names(pipeline)),
        "scaling_applied": bool(config.get("preprocessing", {}).get("apply_scaling", False)),
        "stratified_split": stratify is not None,
        "class_weight_balanced": bool(config.get("class_weight_balanced", False)) if problem_type == "classification" else False,
        "requested_algorithm": requested_algorithm,
        "selected_algorithm": str(config["algorithm_key"]),
        "selected_label": ALGORITHM_LABELS.get(str(config["algorithm_key"]), str(config["algorithm_key"])),
        "selected_parameters": dict(config.get("parameters", {})),
        "class_labels": [str(label) for label in pipeline.named_steps["model"].classes_] if problem_type == "classification" else [],
    }
    return pipeline, metrics, charts, summary, importance, prediction_schema(working, features)


def train_clustering(frame: pd.DataFrame, config: dict[str, Any], output_dir: Path):
    features = list(config["features"])
    missing = [column for column in features if column not in frame.columns]
    if missing:
        raise ValueError("Missing required columns: " + ", ".join(missing))
    working = frame[features].copy()
    original_rows = len(working)
    if bool(config.get("preprocessing", {}).get("remove_duplicates", True)):
        working = working.drop_duplicates()
    duplicates_removed = original_rows - len(working)
    if len(working) < 20:
        raise ValueError("K-Means requires at least 20 usable rows.")
    preprocessor, numeric, categorical = make_preprocessor(working, features, config, numeric_only=True)
    params = config.get("parameters", {})
    clusters = int(params.get("n_clusters", 3))
    if clusters >= len(working):
        raise ValueError("The number of clusters must be smaller than the number of rows.")
    model = KMeans(
        n_clusters=clusters,
        n_init=int(params.get("n_init", 10)),
        random_state=int(config.get("random_state", 42)),
    )
    pipeline = Pipeline([("preprocessor", preprocessor), ("model", model)])
    update_progress(output_dir, 35, "Training K-Means clusters")
    started = time.perf_counter()
    labels = pipeline.fit_predict(working)
    training_ms = int((time.perf_counter() - started) * 1000)
    transformed = pipeline.named_steps["preprocessor"].transform(working)
    silhouette = silhouette_score(transformed, labels) if len(set(labels.tolist())) > 1 else None
    counts = pd.Series(labels).value_counts().sort_index()
    charts: dict[str, str] = {}

    plt.figure(figsize=(7, 5))
    plt.scatter(working[features[0]], working[features[1]], c=labels, alpha=0.7)
    plt.xlabel(features[0])
    plt.ylabel(features[1])
    plt.title("Cluster Scatter Plot")
    save_current_figure(output_dir / "cluster_scatter.png")
    charts["cluster_scatter"] = "cluster_scatter.png"

    plt.figure(figsize=(7, 4.5))
    plt.bar([f"Cluster {int(index) + 1}" for index in counts.index], counts.values)
    plt.ylabel("Rows")
    plt.title("Cluster Sizes")
    save_current_figure(output_dir / "cluster_sizes.png")
    charts["cluster_sizes"] = "cluster_sizes.png"
    if bool(config.get("generate_extended_charts", True)):
        if plot_correlation(working, output_dir / "correlation_heatmap.png"):
            charts["correlation_heatmap"] = "correlation_heatmap.png"
        if plot_missing_values(working, output_dir / "missing_values.png"):
            charts["missing_values"] = "missing_values.png"
        if plot_missing_heatmap(working, output_dir / "missing_value_heatmap.png"):
            charts["missing_value_heatmap"] = "missing_value_heatmap.png"
        if plot_distributions(working, output_dir / "distribution_plots.png"):
            charts["distribution_plots"] = "distribution_plots.png"

    metrics = {
        "inertia": float(model.inertia_),
        "silhouette": float(silhouette) if silhouette is not None else None,
        "cluster_count": clusters,
        "cluster_sizes": {f"Cluster {int(index) + 1}": int(value) for index, value in counts.items()},
    }
    summary = {
        "rows_total": int(len(frame)),
        "rows_used": int(len(working)),
        "train_rows": int(len(working)),
        "test_rows": 0,
        "duplicates_removed": int(duplicates_removed),
        "dropped_target_rows": 0,
        "feature_count": len(features),
        "numeric_features": numeric,
        "categorical_features": categorical,
        "transformed_feature_count": int(transformed.shape[1]),
        "scaling_applied": bool(config.get("preprocessing", {}).get("apply_scaling", False)),
        "training_time_ms": training_ms,
    }
    return pipeline, metrics, charts, summary, [], prediction_schema(working, features)


def train(dataset_path: Path, config_path: Path, output_dir: Path) -> None:
    output_dir.mkdir(parents=True, exist_ok=True)
    update_progress(output_dir, 3, "Reading the dataset")
    config = load_config(config_path)
    frame = load_frame(dataset_path)
    update_progress(output_dir, 15, "Validating columns and preprocessing choices")
    started = time.perf_counter()

    if config.get("problem_type") in {"classification", "regression"}:
        pipeline, metrics, charts, summary, importance, schema = train_supervised(frame, config, output_dir)
    elif config.get("problem_type") == "clustering":
        pipeline, metrics, charts, summary, importance, schema = train_clustering(frame, config, output_dir)
    else:
        raise ValueError("Unsupported machine-learning problem type.")

    total_ms = int((time.perf_counter() - started) * 1000)
    summary["training_time_ms"] = int(summary.get("training_time_ms", total_ms))
    update_progress(output_dir, 90, "Saving the model artifact and metadata")
    artifact = {
        "pipeline": pipeline,
        "problem_type": config["problem_type"],
        "algorithm_key": config["algorithm_key"],
        "selected_algorithm": summary.get("selected_algorithm", config["algorithm_key"]),
        "features": list(config["features"]),
        "target_column": config.get("target_column"),
        "prediction_schema": schema,
        "feature_importance": importance,
        "created_at": time.time(),
        "sklearn_version": sklearn.__version__,
    }
    joblib.dump(artifact, output_dir / "model.joblib", compress=3)
    result = {
        "ok": True,
        "metrics": metrics,
        "visualizations": charts,
        "training_summary": summary,
        "feature_importance": importance,
        "prediction_schema": schema,
        "problem_type": config["problem_type"],
        "algorithm_key": config["algorithm_key"],
        "features": list(config["features"]),
        "target_column": config.get("target_column"),
        "selected_algorithm": summary.get("selected_algorithm", config["algorithm_key"]),
        "selected_label": summary.get("selected_label"),
        "parameters": summary.get("selected_parameters", config.get("parameters", {})),
        "preprocessing": {**config.get("preprocessing", {}), "apply_scaling": bool(summary.get("scaling_applied", False))},
        "python_version": platform.python_version(),
        "sklearn_version": sklearn.__version__,
        "training_time_ms": total_ms,
    }
    write_json(output_dir / "metadata.json", result)
    write_json(output_dir / "result.json", result)
    update_progress(output_dir, 100, "Training completed")


def predict(model_path: Path, input_path: Path, output_dir: Path) -> None:
    output_dir.mkdir(parents=True, exist_ok=True)
    artifact = joblib.load(model_path)
    payload = load_config(input_path)
    features = list(artifact["features"])
    values = payload.get("input_values", {})
    if not isinstance(values, dict):
        raise ValueError("Prediction input must be an object keyed by feature name.")
    frame = pd.DataFrame([{feature: values.get(feature) for feature in features}])
    prediction = artifact["pipeline"].predict(frame)[0]
    problem_type = artifact["problem_type"]
    result: dict[str, Any] = {
        "ok": True,
        "predicted_value": str(prediction) if problem_type != "regression" else float(prediction),
        "probabilities": None,
        "confidence": None,
        "top_features": artifact.get("feature_importance", [])[:5],
    }
    if problem_type == "classification" and hasattr(artifact["pipeline"], "predict_proba"):
        probabilities = artifact["pipeline"].predict_proba(frame)[0]
        classes = artifact["pipeline"].named_steps["model"].classes_
        mapping = {str(label): float(probability) * 100 for label, probability in zip(classes, probabilities)}
        result["probabilities"] = mapping
        result["confidence"] = max(mapping.values()) if mapping else None
    if problem_type == "clustering":
        result["predicted_value"] = f"Cluster {int(prediction) + 1}"
        result["cluster_index"] = int(prediction)
    write_json(output_dir / "prediction.json", result)


def main() -> int:
    if len(sys.argv) != 5:
        print("Usage: trusted_runner.py <train|predict> <data/model path> <config/input path> <output dir>", file=sys.stderr)
        return 2
    mode = sys.argv[1]
    first = Path(sys.argv[2])
    second = Path(sys.argv[3])
    output_dir = Path(sys.argv[4])
    try:
        if mode == "train":
            train(first, second, output_dir)
        elif mode == "predict":
            predict(first, second, output_dir)
        else:
            raise ValueError("Unsupported runner mode.")
        return 0
    except Exception as exc:
        output_dir.mkdir(parents=True, exist_ok=True)
        message = str(exc).strip() or "The trusted machine-learning runner failed."
        write_json(output_dir / "error.json", {
            "ok": False,
            "message": message[:1200],
            "type": exc.__class__.__name__,
        })
        print(message[:1200], file=sys.stderr)
        if os.environ.get("ML_DEBUG") == "1":
            traceback.print_exc()
        return 1


if __name__ == "__main__":
    raise SystemExit(main())
