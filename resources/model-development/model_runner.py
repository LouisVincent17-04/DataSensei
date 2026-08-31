#!/usr/bin/env python3

import json
import math
import time
import warnings
from pathlib import Path

import numpy as np
import pandas as pd
from sklearn.cluster import KMeans
from sklearn.compose import ColumnTransformer
from sklearn.ensemble import RandomForestClassifier
from sklearn.impute import SimpleImputer
from sklearn.linear_model import Lasso, LinearRegression, LogisticRegression, Ridge
from sklearn.metrics import (
    accuracy_score,
    confusion_matrix,
    f1_score,
    mean_absolute_error,
    mean_squared_error,
    precision_score,
    r2_score,
    recall_score,
    silhouette_score,
)
from sklearn.model_selection import train_test_split
from sklearn.naive_bayes import GaussianNB
from sklearn.neighbors import KNeighborsClassifier
from sklearn.pipeline import Pipeline
from sklearn.preprocessing import OneHotEncoder, StandardScaler
from sklearn.tree import DecisionTreeClassifier

warnings.filterwarnings("ignore")

BASE = Path(__file__).resolve().parent
DATASET_FILE = BASE / "dataset.json"
CONFIG_FILE = BASE / "config.json"


def load_json(path):
    with path.open("r", encoding="utf-8") as handle:
        return json.load(handle)


def emit(payload):
    print("__MODEL_RESULT__:" + json.dumps(sanitize(payload), separators=(",", ":"), ensure_ascii=False))


def sanitize(value):
    if isinstance(value, dict):
        return {str(key): sanitize(item) for key, item in value.items()}
    if isinstance(value, (list, tuple, np.ndarray, pd.Series)):
        return [sanitize(item) for item in list(value)]
    if isinstance(value, (np.integer,)):
        return int(value)
    if isinstance(value, (np.floating, float)):
        number = float(value)
        if math.isnan(number) or math.isinf(number):
            return None
        return round(number, 6)
    if isinstance(value, (np.bool_, bool)):
        return bool(value)
    if pd.isna(value):
        return None
    return value


def clean_frame(records):
    frame = pd.DataFrame(records)
    frame = frame.replace(r"^\s*$", np.nan, regex=True)
    return frame


def split_feature_types(frame, features):
    numeric = [column for column in features if pd.api.types.is_numeric_dtype(frame[column])]
    categorical = [column for column in features if column not in numeric]
    return numeric, categorical


def build_preprocessor(frame, features, config, force_numeric=False):
    numeric, categorical = split_feature_types(frame, features)
    if force_numeric and categorical:
        raise ValueError("K-Means accepts numeric feature columns only.")

    numeric_strategy = config.get("preprocessing", {}).get("numeric_imputation", "median")
    apply_scaling = bool(config.get("preprocessing", {}).get("apply_scaling", False))

    numeric_steps = [("imputer", SimpleImputer(strategy=numeric_strategy))]
    if apply_scaling:
        numeric_steps.append(("scaler", StandardScaler()))

    transformers = []
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
        raise ValueError("No usable feature columns remain after validation.")

    return ColumnTransformer(transformers=transformers, remainder="drop"), numeric, categorical


def classifier_for(config, training_rows):
    algorithm = config["algorithm"]
    params = config.get("parameters", {})
    seed = int(config.get("random_seed", 42))

    if algorithm == "logistic_regression":
        return LogisticRegression(max_iter=600, random_state=seed)
    if algorithm == "decision_tree":
        return DecisionTreeClassifier(max_depth=int(params.get("max_depth", 5)), random_state=seed)
    if algorithm == "random_forest":
        return RandomForestClassifier(
            n_estimators=int(params.get("n_estimators", 60)),
            max_depth=int(params.get("max_depth", 6)),
            random_state=seed,
            n_jobs=1,
        )
    if algorithm == "knn":
        requested = int(params.get("n_neighbors", 5))
        return KNeighborsClassifier(n_neighbors=max(1, min(requested, training_rows)))
    if algorithm == "naive_bayes":
        return GaussianNB()
    raise ValueError("Unsupported classification algorithm.")


def regressor_for(config):
    algorithm = config["algorithm"]
    params = config.get("parameters", {})

    if algorithm == "linear_regression":
        return LinearRegression()
    if algorithm == "ridge_regression":
        return Ridge(alpha=float(params.get("alpha", 1.0)))
    if algorithm == "lasso_regression":
        return Lasso(alpha=float(params.get("alpha", 1.0)), max_iter=5000)
    raise ValueError("Unsupported regression algorithm.")


def feature_names(pipeline):
    try:
        names = pipeline.named_steps["preprocessor"].get_feature_names_out()
        return [str(name).replace("numeric__", "").replace("categorical__", "") for name in names]
    except Exception:
        return []


def importance_payload(pipeline):
    model = pipeline.named_steps.get("model")
    names = feature_names(pipeline)
    values = None

    if hasattr(model, "feature_importances_"):
        values = np.asarray(model.feature_importances_, dtype=float)
    elif hasattr(model, "coef_"):
        coefficients = np.asarray(model.coef_, dtype=float)
        values = np.mean(np.abs(coefficients), axis=0) if coefficients.ndim > 1 else np.abs(coefficients)

    if values is None or len(values) != len(names):
        return {"labels": [], "values": []}

    pairs = sorted(zip(names, values.tolist()), key=lambda pair: abs(pair[1]), reverse=True)[:12]
    return {
        "labels": [pair[0] for pair in pairs],
        "values": [round(float(pair[1]), 6) for pair in pairs],
    }


def prepare_supervised(frame, config):
    features = config["features"]
    target = config["target_column"]
    required = features + [target]
    missing = [column for column in required if column not in frame.columns]
    if missing:
        raise ValueError("The dataset is missing required columns: " + ", ".join(missing))

    working = frame[required].copy()
    original_rows = len(working)
    if bool(config.get("preprocessing", {}).get("remove_duplicates", True)):
        working = working.drop_duplicates()
    duplicate_rows_removed = original_rows - len(working)
    working = working.dropna(subset=[target])
    dropped_target_rows = original_rows - duplicate_rows_removed - len(working)

    if len(working) < 20:
        raise ValueError("At least 20 usable records are required after cleaning the target column.")

    return working, duplicate_rows_removed, dropped_target_rows


def train_classification(frame, config):
    working, duplicates_removed, dropped_target_rows = prepare_supervised(frame, config)
    features = config["features"]
    target = config["target_column"]
    X = working[features].copy()
    y = working[target].astype(str)
    class_counts = y.value_counts()

    if y.nunique() < 2 or y.nunique() > 20:
        raise ValueError("Classification requires between 2 and 20 target classes.")

    test_size = float(config.get("test_size", 0.20))
    seed = int(config.get("random_seed", 42))
    estimated_test_rows = max(1, int(math.ceil(len(y) * test_size)))
    stratify = y if class_counts.min() >= 2 and estimated_test_rows >= y.nunique() else None

    X_train, X_test, y_train, y_test = train_test_split(
        X,
        y,
        test_size=test_size,
        random_state=seed,
        stratify=stratify,
    )

    preprocessor, numeric, categorical = build_preprocessor(working, features, config)
    model = classifier_for(config, len(X_train))
    pipeline = Pipeline([("preprocessor", preprocessor), ("model", model)])

    started = time.perf_counter()
    pipeline.fit(X_train, y_train)
    training_ms = int((time.perf_counter() - started) * 1000)
    predicted = pipeline.predict(X_test)
    labels = sorted([str(value) for value in y.unique().tolist()])
    matrix = confusion_matrix(y_test, predicted, labels=labels)

    metrics = {
        "accuracy": accuracy_score(y_test, predicted) * 100,
        "precision": precision_score(y_test, predicted, average="weighted", zero_division=0) * 100,
        "recall": recall_score(y_test, predicted, average="weighted", zero_division=0) * 100,
        "f1": f1_score(y_test, predicted, average="weighted", zero_division=0) * 100,
    }

    charts = {
        "confusion_matrix": {"labels": labels, "values": matrix.tolist()},
        "feature_importance": importance_payload(pipeline),
        "test_predictions": [
            {"actual": str(actual), "predicted": str(prediction), "correct": str(actual) == str(prediction)}
            for actual, prediction in list(zip(y_test.tolist(), predicted.tolist()))[:60]
        ],
    }

    summary = {
        "rows_total": len(frame),
        "rows_used": len(working),
        "train_rows": len(X_train),
        "test_rows": len(X_test),
        "duplicates_removed": duplicates_removed,
        "dropped_target_rows": dropped_target_rows,
        "feature_count": len(features),
        "transformed_feature_count": len(feature_names(pipeline)),
        "numeric_features": numeric,
        "categorical_features": categorical,
        "class_count": len(labels),
        "class_labels": labels,
        "stratified_split": stratify is not None,
        "scaling_applied": bool(config.get("preprocessing", {}).get("apply_scaling", False)),
        "training_time_ms": training_ms,
    }

    return pipeline, metrics, charts, summary


def train_regression(frame, config):
    working, duplicates_removed, dropped_target_rows = prepare_supervised(frame, config)
    features = config["features"]
    target = config["target_column"]
    working[target] = pd.to_numeric(working[target], errors="coerce")
    before_numeric_drop = len(working)
    working = working.dropna(subset=[target])
    dropped_target_rows += before_numeric_drop - len(working)

    if len(working) < 20:
        raise ValueError("Regression requires at least 20 records with a numeric target.")

    X = working[features].copy()
    y = working[target].astype(float)
    test_size = float(config.get("test_size", 0.20))
    seed = int(config.get("random_seed", 42))
    X_train, X_test, y_train, y_test = train_test_split(
        X,
        y,
        test_size=test_size,
        random_state=seed,
    )

    preprocessor, numeric, categorical = build_preprocessor(working, features, config)
    pipeline = Pipeline([("preprocessor", preprocessor), ("model", regressor_for(config))])

    started = time.perf_counter()
    pipeline.fit(X_train, y_train)
    training_ms = int((time.perf_counter() - started) * 1000)
    predicted = pipeline.predict(X_test)
    mse = mean_squared_error(y_test, predicted)
    residuals = y_test.to_numpy(dtype=float) - np.asarray(predicted, dtype=float)

    metrics = {
        "mae": mean_absolute_error(y_test, predicted),
        "mse": mse,
        "rmse": math.sqrt(mse),
        "r2": r2_score(y_test, predicted),
    }

    pairs = list(zip(y_test.tolist(), np.asarray(predicted).tolist(), residuals.tolist()))[:60]
    charts = {
        "actual_vs_predicted": [
            {"actual": float(actual), "predicted": float(prediction)} for actual, prediction, _ in pairs
        ],
        "residuals": [
            {"predicted": float(prediction), "residual": float(residual)} for _, prediction, residual in pairs
        ],
        "feature_importance": importance_payload(pipeline),
    }

    summary = {
        "rows_total": len(frame),
        "rows_used": len(working),
        "train_rows": len(X_train),
        "test_rows": len(X_test),
        "duplicates_removed": duplicates_removed,
        "dropped_target_rows": dropped_target_rows,
        "feature_count": len(features),
        "transformed_feature_count": len(feature_names(pipeline)),
        "numeric_features": numeric,
        "categorical_features": categorical,
        "scaling_applied": bool(config.get("preprocessing", {}).get("apply_scaling", False)),
        "training_time_ms": training_ms,
    }

    return pipeline, metrics, charts, summary


def train_clustering(frame, config):
    features = config["features"]
    missing = [column for column in features if column not in frame.columns]
    if missing:
        raise ValueError("The dataset is missing required columns: " + ", ".join(missing))

    working = frame[features].copy()
    original_rows = len(working)
    if bool(config.get("preprocessing", {}).get("remove_duplicates", True)):
        working = working.drop_duplicates()
    duplicates_removed = original_rows - len(working)

    if len(working) < 20:
        raise ValueError("K-Means requires at least 20 usable records.")

    preprocessor, numeric, categorical = build_preprocessor(working, features, config, force_numeric=True)
    clusters = int(config.get("parameters", {}).get("n_clusters", 3))
    if clusters >= len(working):
        raise ValueError("The number of clusters must be smaller than the number of records.")

    model = KMeans(n_clusters=clusters, random_state=int(config.get("random_seed", 42)), n_init=10)
    pipeline = Pipeline([("preprocessor", preprocessor), ("model", model)])

    started = time.perf_counter()
    labels = pipeline.fit_predict(working)
    training_ms = int((time.perf_counter() - started) * 1000)
    transformed = pipeline.named_steps["preprocessor"].transform(working)
    silhouette = silhouette_score(transformed, labels) if len(set(labels.tolist())) > 1 else None
    counts = pd.Series(labels).value_counts().sort_index()
    first_feature = features[0]
    second_feature = features[1]

    charts = {
        "cluster_scatter": [
            {
                "x": float(row[first_feature]),
                "y": float(row[second_feature]),
                "cluster": int(label),
            }
            for (_, row), label in list(zip(working.iterrows(), labels.tolist()))[:120]
        ],
        "cluster_sizes": {
            "labels": [f"Cluster {int(index) + 1}" for index in counts.index.tolist()],
            "values": [int(value) for value in counts.tolist()],
        },
        "cluster_centers": pipeline.named_steps["model"].cluster_centers_.tolist(),
        "axis": {"x": first_feature, "y": second_feature},
    }

    metrics = {
        "inertia": float(pipeline.named_steps["model"].inertia_),
        "silhouette": silhouette,
        "cluster_count": clusters,
    }

    summary = {
        "rows_total": len(frame),
        "rows_used": len(working),
        "train_rows": len(working),
        "test_rows": 0,
        "duplicates_removed": duplicates_removed,
        "dropped_target_rows": 0,
        "feature_count": len(features),
        "transformed_feature_count": transformed.shape[1],
        "numeric_features": numeric,
        "categorical_features": categorical,
        "scaling_applied": bool(config.get("preprocessing", {}).get("apply_scaling", False)),
        "training_time_ms": training_ms,
    }

    return pipeline, metrics, charts, summary


def prediction_for(pipeline, config):
    prediction_input = config.get("prediction_input")
    if not isinstance(prediction_input, dict):
        return None

    frame = pd.DataFrame([{feature: prediction_input.get(feature) for feature in config["features"]}])
    frame = frame.replace(r"^\s*$", np.nan, regex=True)
    predicted = pipeline.predict(frame)[0]
    task = config["task_type"]

    if task == "classification":
        probabilities = None
        confidence = None
        if hasattr(pipeline, "predict_proba"):
            values = pipeline.predict_proba(frame)[0]
            classes = pipeline.named_steps["model"].classes_
            probabilities = {str(label): float(probability) * 100 for label, probability in zip(classes, values)}
            confidence = max(probabilities.values()) if probabilities else None
        return {
            "predicted_value": str(predicted),
            "probabilities": probabilities,
            "confidence": confidence,
            "explanation": "The model assigned the class with the strongest learned evidence from the selected features.",
        }

    if task == "regression":
        return {
            "predicted_value": float(predicted),
            "probabilities": None,
            "confidence": None,
            "explanation": "The value is an estimate produced from the relationships learned during training; it is not a guaranteed outcome.",
        }

    return {
        "predicted_value": f"Cluster {int(predicted) + 1}",
        "cluster_index": int(predicted),
        "probabilities": None,
        "confidence": None,
        "explanation": "The record was assigned to the cluster with the nearest learned center after preprocessing and scaling.",
    }


def interpretations(task, metrics):
    if task == "classification":
        return {
            "accuracy": "Accuracy is the percentage of test records classified correctly. It is easiest to interpret when class sizes are reasonably balanced.",
            "precision": "Weighted precision measures how often predicted classes were correct while accounting for the number of records in each class.",
            "recall": "Weighted recall measures how many actual class members the model successfully identified.",
            "f1": "The weighted F1 score balances precision and recall. Higher values indicate a stronger overall classification balance.",
            "result": f"The model correctly classified approximately {metrics['accuracy']:.1f}% of the held-out test records.",
        }
    if task == "regression":
        r2 = metrics.get("r2")
        return {
            "mae": "MAE is the average absolute prediction error in the same unit as the target. Lower is better.",
            "mse": "MSE squares errors before averaging, so large mistakes receive a stronger penalty. Lower is better.",
            "rmse": "RMSE is the square root of MSE and returns the error to the target's original unit. Lower is better.",
            "r2": "R² describes how much target variation is explained by the model. Values closer to 1 are stronger; negative values indicate performance worse than predicting the test-set mean.",
            "result": f"The model produced an R² score of {r2:.3f}. Interpret it together with MAE and RMSE rather than alone.",
        }
    return {
        "inertia": "Inertia is the total squared distance from records to their cluster centers. Lower values indicate tighter clusters, but it should be compared across different cluster counts on the same data.",
        "silhouette": "The silhouette score ranges from -1 to 1. Higher values indicate clearer separation between clusters.",
        "result": "K-Means grouped records by similarity without using a target label. Cluster numbers are labels, not rankings.",
    }


def main():
    try:
        records = load_json(DATASET_FILE)
        config = load_json(CONFIG_FILE)
        frame = clean_frame(records)
        task = config.get("task_type")

        if task == "classification":
            pipeline, metrics, charts, summary = train_classification(frame, config)
        elif task == "regression":
            pipeline, metrics, charts, summary = train_regression(frame, config)
        elif task == "clustering":
            pipeline, metrics, charts, summary = train_clustering(frame, config)
        else:
            raise ValueError("Unsupported machine-learning task.")

        prediction = prediction_for(pipeline, config) if config.get("operation") == "predict" else None
        emit({
            "ok": True,
            "metrics": metrics,
            "charts": charts,
            "training_summary": summary,
            "interpretations": interpretations(task, metrics),
            "prediction": prediction,
        })
    except Exception as exc:
        emit({"ok": False, "message": str(exc)[:700]})


if __name__ == "__main__":
    main()
