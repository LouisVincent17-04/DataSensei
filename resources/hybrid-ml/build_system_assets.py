#!/usr/bin/env python3
from __future__ import annotations

import importlib.util
import json
import math
import shutil
from datetime import datetime, timezone
from pathlib import Path

import numpy as np
import pandas as pd
from sklearn.datasets import load_breast_cancer, load_diabetes, load_iris

ROOT = Path(__file__).resolve().parents[2]
STORAGE = ROOT / "storage" / "app" / "ml" / "system"
RUNNER_PATH = ROOT / "resources" / "hybrid-ml" / "trusted_runner.py"

spec = importlib.util.spec_from_file_location("datasensei_trusted_ml", RUNNER_PATH)
runner = importlib.util.module_from_spec(spec)
assert spec.loader is not None
spec.loader.exec_module(runner)

rng = np.random.default_rng(20260801)


def clean_columns(frame: pd.DataFrame) -> pd.DataFrame:
    frame = frame.copy()
    frame.columns = [str(c).strip().lower().replace(" ", "_").replace("(", "").replace(")", "") for c in frame.columns]
    return frame


def iris_dataset():
    data = load_iris(as_frame=True)
    frame = clean_columns(data.frame)
    frame = frame.rename(columns={"target": "species"})
    frame["species"] = frame["species"].map(dict(enumerate(data.target_names)))
    return frame, {
        "name": "Iris Flower Classification",
        "description": "Classify iris species from sepal and petal measurements.",
        "source_name": "UCI Iris dataset, packaged by scikit-learn",
        "source_url": "https://archive.ics.uci.edu/dataset/53/iris",
        "license_name": "CC BY 4.0",
        "target": "species",
        "problem_type": "classification",
    }


def titanic_dataset(n=600):
    pclass = rng.choice([1, 2, 3], n, p=[0.24, 0.22, 0.54])
    sex = rng.choice(["female", "male"], n, p=[0.37, 0.63])
    age = np.clip(rng.normal(30, 14, n), 1, 80)
    fare = np.maximum(4, rng.lognormal(mean=3.2 - 0.35 * (pclass - 1), sigma=0.7, size=n))
    sibsp = rng.poisson(0.5, n)
    parch = rng.poisson(0.4, n)
    embarked = rng.choice(["S", "C", "Q"], n, p=[0.70, 0.20, 0.10])
    logit = 1.9 * (sex == "female") - 0.75 * (pclass - 1) - 0.018 * (age - 28) + 0.18 * (fare > 40) - 0.15 * sibsp
    probability = 1 / (1 + np.exp(-logit))
    survived = rng.binomial(1, probability)
    frame = pd.DataFrame({
        "passenger_class": pclass,
        "sex": sex,
        "age": np.round(age, 1),
        "siblings_spouses": sibsp,
        "parents_children": parch,
        "fare": np.round(fare, 2),
        "embarked": embarked,
        "survived": survived,
    })
    missing_age = rng.choice(n, 24, replace=False)
    frame.loc[missing_age, "age"] = np.nan
    return frame, {
        "name": "Titanic Survival",
        "description": "Predict passenger survival from travel class, demographic, and family information.",
        "source_name": "DataSensei educational synthetic dataset inspired by Titanic variables",
        "source_url": None,
        "license_name": "DataSensei educational use",
        "target": "survived",
        "problem_type": "classification",
    }


def wine_quality_dataset(n=650):
    alcohol = np.clip(rng.normal(10.5, 1.2, n), 7.5, 15)
    acidity = np.clip(rng.normal(7.1, 1.1, n), 4, 12)
    volatile = np.clip(rng.normal(0.52, 0.17, n), 0.1, 1.4)
    sulphates = np.clip(rng.normal(0.65, 0.16, n), 0.25, 1.5)
    chlorides = np.clip(rng.normal(0.075, 0.024, n), 0.02, 0.2)
    density = np.clip(rng.normal(0.996, 0.002, n), 0.990, 1.004)
    sugar = np.clip(rng.lognormal(0.9, 0.55, n), 0.4, 15)
    score = 5.2 + 0.45 * (alcohol - 10.5) - 1.2 * (volatile - 0.5) + 0.8 * (sulphates - 0.6) - 2.5 * (chlorides - 0.07) + rng.normal(0, 0.55, n)
    quality = np.clip(np.rint(score), 3, 8).astype(int)
    label = pd.cut(quality, bins=[2, 4, 6, 9], labels=["low", "medium", "high"]).astype(str)
    frame = pd.DataFrame({
        "fixed_acidity": np.round(acidity, 3),
        "volatile_acidity": np.round(volatile, 3),
        "residual_sugar": np.round(sugar, 3),
        "chlorides": np.round(chlorides, 4),
        "density": np.round(density, 5),
        "sulphates": np.round(sulphates, 3),
        "alcohol": np.round(alcohol, 3),
        "quality_score": quality,
        "quality_label": label,
    })
    return frame, {
        "name": "Wine Quality",
        "description": "Classify wine quality levels from physicochemical measurements.",
        "source_name": "DataSensei educational synthetic wine-quality dataset",
        "source_url": None,
        "license_name": "DataSensei educational use",
        "target": "quality_label",
        "problem_type": "classification",
    }


def breast_cancer_dataset():
    data = load_breast_cancer(as_frame=True)
    frame = clean_columns(data.frame)
    frame = frame.rename(columns={"target": "diagnosis"})
    frame["diagnosis"] = frame["diagnosis"].map({0: "malignant", 1: "benign"})
    # Keep a focused subset for the educational UI and faster training.
    selected = [
        "mean_radius", "mean_texture", "mean_perimeter", "mean_area", "mean_smoothness",
        "mean_compactness", "mean_concavity", "mean_concave_points", "worst_radius",
        "worst_texture", "worst_perimeter", "worst_area", "diagnosis",
    ]
    frame = frame[selected]
    return frame, {
        "name": "Breast Cancer Wisconsin",
        "description": "Classify diagnostic outcomes from cell-nucleus measurements.",
        "source_name": "Wisconsin Diagnostic Breast Cancer dataset, packaged by scikit-learn",
        "source_url": "https://archive.ics.uci.edu/dataset/17/breast+cancer+wisconsin+diagnostic",
        "license_name": "UCI dataset terms",
        "target": "diagnosis",
        "problem_type": "classification",
    }


def churn_dataset(n=850):
    tenure = rng.integers(1, 73, n)
    contract = rng.choice(["month-to-month", "one-year", "two-year"], n, p=[0.56, 0.25, 0.19])
    internet = rng.choice(["fiber", "dsl", "none"], n, p=[0.48, 0.42, 0.10])
    support = rng.choice(["yes", "no"], n, p=[0.44, 0.56])
    monthly = np.round(np.clip(rng.normal(68, 25, n) + (internet == "fiber") * 14, 18, 145), 2)
    late = rng.poisson(0.8, n)
    satisfaction = np.clip(np.rint(rng.normal(3.4, 1.0, n)), 1, 5).astype(int)
    logit = -1.3 + 1.0 * (contract == "month-to-month") + 0.55 * (internet == "fiber") + 0.35 * late - 0.035 * tenure - 0.55 * (satisfaction - 3) - 0.45 * (support == "yes")
    churned = rng.binomial(1, 1 / (1 + np.exp(-logit)))
    frame = pd.DataFrame({
        "tenure_months": tenure,
        "contract_type": contract,
        "internet_service": internet,
        "technical_support": support,
        "monthly_charge": monthly,
        "late_payments": late,
        "satisfaction_score": satisfaction,
        "churned": churned,
    })
    return frame, {
        "name": "Customer Churn",
        "description": "Predict whether a subscription customer will leave the service.",
        "source_name": "DataSensei educational synthetic customer-churn dataset",
        "source_url": None,
        "license_name": "DataSensei educational use",
        "target": "churned",
        "problem_type": "classification",
    }


def student_performance_dataset(n=650):
    study = np.clip(rng.normal(10, 4, n), 1, 25)
    attendance = np.clip(rng.normal(86, 9, n), 45, 100)
    sleep = np.clip(rng.normal(7, 1.2, n), 3, 10)
    prior = np.clip(rng.normal(78, 10, n), 40, 100)
    assignments = np.clip(rng.normal(82, 9, n), 35, 100)
    internet = rng.choice(["stable", "limited"], n, p=[0.77, 0.23])
    final = 10 + 1.1 * study + 0.28 * attendance + 0.18 * prior + 0.22 * assignments + 1.2 * sleep + 3 * (internet == "stable") + rng.normal(0, 4.5, n)
    final = np.clip(final, 50, 100)
    frame = pd.DataFrame({
        "study_hours_weekly": np.round(study, 1),
        "attendance_rate": np.round(attendance, 1),
        "sleep_hours": np.round(sleep, 1),
        "prior_grade": np.round(prior, 1),
        "assignment_average": np.round(assignments, 1),
        "internet_access": internet,
        "final_score": np.round(final, 2),
    })
    return frame, {
        "name": "Student Performance",
        "description": "Predict final academic performance from study habits and prior results.",
        "source_name": "DataSensei educational synthetic student-performance dataset",
        "source_url": None,
        "license_name": "DataSensei educational use",
        "target": "final_score",
        "problem_type": "regression",
    }


def housing_dataset(n=750):
    area = np.clip(rng.normal(120, 45, n), 35, 320)
    bedrooms = np.clip(np.rint(area / 42 + rng.normal(0, 0.6, n)), 1, 7).astype(int)
    bathrooms = np.clip(np.rint(bedrooms * 0.55 + rng.normal(0.5, 0.4, n)), 1, 5).astype(int)
    age = rng.integers(0, 61, n)
    distance = np.clip(rng.gamma(2.5, 2.2, n), 0.2, 30)
    location = rng.choice(["urban", "suburban", "rural"], n, p=[0.42, 0.40, 0.18])
    parking = rng.integers(0, 4, n)
    price = 55000 + area * 1750 + bedrooms * 14000 + bathrooms * 22000 - age * 1300 - distance * 5200 + parking * 9000 + np.select([location == "urban", location == "suburban"], [110000, 55000], default=0) + rng.normal(0, 30000, n)
    frame = pd.DataFrame({
        "floor_area_sqm": np.round(area, 1),
        "bedrooms": bedrooms,
        "bathrooms": bathrooms,
        "property_age_years": age,
        "distance_to_center_km": np.round(distance, 2),
        "location_type": location,
        "parking_spaces": parking,
        "price": np.round(np.maximum(price, 50000), 2),
    })
    return frame, {
        "name": "Housing Prices",
        "description": "Estimate property price from size, location, age, and room information.",
        "source_name": "DataSensei educational synthetic housing dataset",
        "source_url": None,
        "license_name": "DataSensei educational use",
        "target": "price",
        "problem_type": "regression",
    }


def sales_dataset(n=720):
    dates = pd.date_range("2023-01-01", periods=n, freq="D")
    category = rng.choice(["electronics", "home", "office", "personal"], n)
    promotion = rng.choice(["yes", "no"], n, p=[0.28, 0.72])
    price = np.round(np.clip(rng.normal(42, 18, n) + (category == "electronics") * 35, 5, 180), 2)
    traffic = np.clip(rng.normal(260, 80, n) + (dates.dayofweek >= 5) * 55 + (promotion == "yes") * 90, 50, 700)
    season = np.sin(2 * np.pi * dates.dayofyear / 365.25)
    sales = 25 + traffic * 0.22 - price * 0.12 + (promotion == "yes") * 32 + season * 18 + (category == "electronics") * 15 + rng.normal(0, 12, n)
    frame = pd.DataFrame({
        "day_of_week": dates.day_name(),
        "month": dates.month,
        "product_category": category,
        "promotion": promotion,
        "unit_price": price,
        "website_visits": np.rint(traffic).astype(int),
        "sales": np.round(np.maximum(sales, 0), 2),
    })
    return frame, {
        "name": "Sales Forecast",
        "description": "Estimate daily sales using pricing, promotions, traffic, category, and calendar features.",
        "source_name": "DataSensei educational synthetic sales dataset",
        "source_url": None,
        "license_name": "DataSensei educational use",
        "target": "sales",
        "problem_type": "regression",
    }


def diabetes_dataset():
    data = load_diabetes(as_frame=True, scaled=False)
    frame = clean_columns(data.frame)
    frame = frame.rename(columns={"target": "progression"})
    return frame, {
        "name": "Diabetes Progression",
        "description": "Estimate one-year disease progression from baseline clinical measurements.",
        "source_name": "Diabetes dataset described by Efron et al., packaged by scikit-learn",
        "source_url": "https://scikit-learn.org/stable/datasets/toy_dataset.html#diabetes-dataset",
        "license_name": "scikit-learn packaged dataset terms",
        "target": "progression",
        "problem_type": "regression",
    }


def heart_dataset(n=700):
    age = rng.integers(29, 79, n)
    sex = rng.choice(["female", "male"], n, p=[0.44, 0.56])
    chest = rng.choice(["typical", "atypical", "non_anginal", "asymptomatic"], n, p=[0.16, 0.22, 0.28, 0.34])
    resting_bp = np.clip(rng.normal(132, 18, n), 88, 210)
    cholesterol = np.clip(rng.normal(240, 48, n), 120, 410)
    max_hr = np.clip(205 - age + rng.normal(0, 14, n), 70, 205)
    exercise_angina = rng.choice(["yes", "no"], n, p=[0.33, 0.67])
    oldpeak = np.clip(rng.gamma(1.5, 0.9, n), 0, 6)
    logit = -5.6 + 0.055 * age + 0.9 * (sex == "male") + 1.3 * (chest == "asymptomatic") + 0.018 * (resting_bp - 120) + 0.009 * (cholesterol - 200) - 0.025 * (max_hr - 140) + 1.1 * (exercise_angina == "yes") + 0.45 * oldpeak
    disease = rng.binomial(1, 1 / (1 + np.exp(-logit)))
    frame = pd.DataFrame({
        "age": age,
        "sex": sex,
        "chest_pain_type": chest,
        "resting_blood_pressure": np.round(resting_bp, 1),
        "cholesterol": np.round(cholesterol, 1),
        "maximum_heart_rate": np.round(max_hr, 1),
        "exercise_induced_angina": exercise_angina,
        "st_depression": np.round(oldpeak, 2),
        "has_disease": disease,
    })
    return frame, {
        "name": "Heart Disease",
        "description": "Predict heart-disease risk from demographic and clinical measurements.",
        "source_name": "DataSensei educational synthetic heart-disease dataset",
        "source_url": None,
        "license_name": "DataSensei educational use",
        "target": "has_disease",
        "problem_type": "classification",
    }


DATASETS = {
    "iris": iris_dataset,
    "titanic": titanic_dataset,
    "wine-quality": wine_quality_dataset,
    "breast-cancer-wisconsin": breast_cancer_dataset,
    "customer-churn": churn_dataset,
    "student-performance": student_performance_dataset,
    "housing-prices": housing_dataset,
    "sales-forecast": sales_dataset,
    "diabetes": diabetes_dataset,
    "heart-disease": heart_dataset,
}

ALGORITHMS = {
    "iris": ["logistic_regression", "decision_tree", "random_forest", "knn"],
    "titanic": ["random_forest", "logistic_regression", "decision_tree"],
    "wine-quality": ["svm", "random_forest", "logistic_regression"],
    "breast-cancer-wisconsin": ["random_forest", "logistic_regression", "svm"],
    "customer-churn": ["random_forest", "logistic_regression", "decision_tree"],
    "heart-disease": ["random_forest", "logistic_regression", "svm"],
    "student-performance": ["linear_regression", "random_forest_regressor", "decision_tree_regressor"],
    "housing-prices": ["linear_regression", "random_forest_regressor", "decision_tree_regressor"],
    "sales-forecast": ["linear_regression", "random_forest_regressor", "decision_tree_regressor"],
    "diabetes": ["linear_regression", "random_forest_regressor", "decision_tree_regressor"],
}

RECOMMENDATIONS = {
    "iris": {
        "objective": "Predict the iris flower species from its sepal and petal measurements.",
        "target": "species",
        "problem_type": "classification",
        "algorithm_key": "knn",
        "features": ["sepal_length_cm", "sepal_width_cm", "petal_length_cm", "petal_width_cm"],
        "reason": "K-Nearest Neighbors is easy to understand for this small measurement-based dataset because it classifies a flower by comparing it with similar flowers.",
    },
    "titanic": {
        "objective": "Predict whether a passenger survived using travel and demographic information.",
        "target": "survived",
        "problem_type": "classification",
        "algorithm_key": "random_forest",
        "features": ["passenger_class", "sex", "age", "fare", "siblings_spouses", "embarked"],
        "reason": "Random Forest works well with the mix of numeric and categorical passenger information and can capture interactions such as class, age, and sex without requiring a simple straight-line relationship.",
    },
    "wine-quality": {
        "objective": "Predict the wine quality level from physicochemical measurements.",
        "target": "quality_label",
        "problem_type": "classification",
        "algorithm_key": "random_forest",
        "features": ["fixed_acidity", "volatile_acidity", "residual_sugar", "chlorides", "density", "sulphates", "alcohol"],
        "reason": "Random Forest can combine several measurements and capture nonlinear relationships while still giving feature-importance values that are useful for learning.",
    },
    "breast-cancer-wisconsin": {
        "objective": "Classify the diagnostic outcome from cell-nucleus measurements.",
        "target": "diagnosis",
        "problem_type": "classification",
        "algorithm_key": "logistic_regression",
        "features": ["mean_radius", "mean_texture", "mean_concavity", "mean_concave_points", "worst_radius", "worst_perimeter", "worst_area"],
        "reason": "Logistic Regression provides a strong and interpretable classification baseline for these numeric measurements, making it easier to explain how the features influence the predicted class.",
    },
    "customer-churn": {
        "objective": "Predict whether a subscription customer will leave the service.",
        "target": "churned",
        "problem_type": "classification",
        "algorithm_key": "logistic_regression",
        "features": ["tenure_months", "contract_type", "internet_service", "technical_support", "monthly_charge", "late_payments", "satisfaction_score"],
        "reason": "Logistic Regression is a clear starting point for a yes-or-no churn outcome and produces probabilities that beginners can connect to customer risk.",
    },
    "student-performance": {
        "objective": "Predict a student's final score from study habits and prior academic results.",
        "target": "final_score",
        "problem_type": "regression",
        "algorithm_key": "linear_regression",
        "features": ["study_hours_weekly", "attendance_rate", "sleep_hours", "prior_grade", "assignment_average", "internet_access"],
        "reason": "Linear Regression is a simple, transparent model for a numeric score and helps beginners see how each selected feature is associated with the predicted final score.",
    },
    "housing-prices": {
        "objective": "Estimate property price from size, rooms, location, age, and parking information.",
        "target": "price",
        "problem_type": "regression",
        "algorithm_key": "random_forest_regressor",
        "features": ["floor_area_sqm", "bedrooms", "bathrooms", "property_age_years", "distance_to_center_km", "location_type", "parking_spaces"],
        "reason": "Random Forest Regressor can handle different feature types and interactions between property characteristics without assuming that every relationship with price is perfectly linear.",
    },
    "sales-forecast": {
        "objective": "Estimate daily sales from calendar, product, promotion, pricing, and website traffic information.",
        "target": "sales",
        "problem_type": "regression",
        "algorithm_key": "random_forest_regressor",
        "features": ["day_of_week", "month", "product_category", "promotion", "unit_price", "website_visits"],
        "reason": "Random Forest Regressor can capture nonlinear sales patterns and interactions between promotions, traffic, product category, price, and calendar features.",
    },
    "diabetes": {
        "objective": "Estimate one-year diabetes disease progression from baseline clinical measurements.",
        "target": "progression",
        "problem_type": "regression",
        "algorithm_key": "linear_regression",
        "features": ["age", "bmi", "bp", "s1", "s5", "s6"],
        "reason": "Linear Regression is a useful educational baseline for this numeric target because its coefficients are easy to inspect and compare across the selected clinical measurements.",
    },
    "heart-disease": {
        "objective": "Predict whether a record indicates heart disease from demographic and clinical measurements.",
        "target": "has_disease",
        "problem_type": "classification",
        "algorithm_key": "random_forest",
        "features": ["age", "sex", "chest_pain_type", "resting_blood_pressure", "cholesterol", "maximum_heart_rate", "exercise_induced_angina", "st_depression"],
        "reason": "Random Forest is suitable for the mix of categorical and numeric risk factors and can capture interactions between measurements while providing feature importance for interpretation.",
    },
}

DEFAULTS = {
    "logistic_regression": {"c": 1.0, "max_iter": 1000},
    "decision_tree": {"max_depth": 6, "min_samples_split": 2},
    "random_forest": {"n_estimators": 80, "max_depth": 10, "min_samples_split": 2},
    "knn": {"n_neighbors": 5, "weights": "uniform"},
    "naive_bayes": {"var_smoothing": 1e-9},
    "svm": {"c": 1.0, "kernel": "rbf", "gamma": "scale"},
    "linear_regression": {},
    "random_forest_regressor": {"n_estimators": 80, "max_depth": 12, "min_samples_split": 2},
    "decision_tree_regressor": {"max_depth": 8, "min_samples_split": 2},
}


def quality(frame: pd.DataFrame, target: str, problem_type: str):
    missing = int(frame.isna().sum().sum())
    total_cells = max(1, frame.shape[0] * frame.shape[1])
    duplicates = int(frame.duplicated().sum())
    numeric = frame.select_dtypes(include=[np.number])
    outliers = 0
    for column in numeric.columns:
        series = numeric[column].dropna()
        if len(series) < 8:
            continue
        q1, q3 = series.quantile([0.25, 0.75])
        iqr = q3 - q1
        if iqr > 0:
            outliers += int(((series < q1 - 1.5 * iqr) | (series > q3 + 1.5 * iqr)).sum())
    class_balance = {"applicable": False, "label": "Not applicable", "counts": {}}
    if problem_type == "classification":
        counts = frame[target].value_counts().to_dict()
        ratio = min(counts.values()) / max(counts.values())
        class_balance = {
            "applicable": True,
            "counts": {str(k): int(v) for k, v in counts.items()},
            "imbalance_ratio": round(float(ratio), 4),
            "label": "Excellent" if ratio >= .75 else "Good" if ratio >= .5 else "Moderately imbalanced" if ratio >= .25 else "Severely imbalanced",
        }
    missing_pct = missing / total_cells * 100
    duplicate_pct = duplicates / max(1, len(frame)) * 100
    score = 100 - min(30, missing_pct * .75) - min(15, duplicate_pct * .6) - min(15, outliers / max(1, numeric.size) * 100 * .45)
    return {
        "quality_score": round(max(0, min(100, score)), 2),
        "summary": {
            "rows": int(frame.shape[0]), "columns": int(frame.shape[1]),
            "missing_values": missing, "missing_percent": round(missing_pct, 2),
            "duplicate_rows": duplicates, "duplicate_percent": round(duplicate_pct, 2),
            "empty_columns": [c for c in frame.columns if frame[c].isna().all()],
            "constant_columns": [c for c in frame.columns if frame[c].nunique(dropna=True) <= 1],
            "high_cardinality_columns": [c for c in frame.select_dtypes(exclude=[np.number]).columns if frame[c].nunique() > 50],
            "outliers": outliers,
            "outlier_percent": round(outliers / max(1, numeric.size) * 100, 2),
            "target_column": target, "problem_type": problem_type,
            "class_balance": class_balance,
        },
        "recommendations": [
            "Use cross-validation before tuning the benchmark model.",
            "Review feature importance and error plots before interpreting performance.",
        ],
    }


def main():
    if STORAGE.exists():
        shutil.rmtree(STORAGE)
    STORAGE.mkdir(parents=True, exist_ok=True)
    manifest = {
        "schema_version": 1,
        "generated_at": datetime.now(timezone.utc).isoformat(),
        "python_version": sys.version.split()[0] if 'sys' in globals() else None,
        "sklearn_version": runner.sklearn.__version__,
        "datasets": [],
    }

    for slug, factory in DATASETS.items():
        frame, meta = factory()
        target = meta["target"]
        problem_type = meta["problem_type"]
        features = [column for column in frame.columns if column != target]
        dataset_dir = STORAGE / "datasets" / slug / "v1"
        dataset_dir.mkdir(parents=True, exist_ok=True)
        dataset_path = dataset_dir / "dataset.csv"
        frame.to_csv(dataset_path, index=False)
        quality_report = quality(frame, target, problem_type)

        dataset_entry = {
            "slug": slug,
            **meta,
            "storage_path": f"ml/system/datasets/{slug}/v1/dataset.csv",
            "row_count": int(frame.shape[0]),
            "column_count": int(frame.shape[1]),
            "features": features,
            "recommended_setup": RECOMMENDATIONS[slug],
            "version_label": "v1",
            "checksum_sha256": __import__('hashlib').sha256(dataset_path.read_bytes()).hexdigest(),
            "metadata": {
                "preview": json.loads(frame.head(12).to_json(orient="records")),
                "columns": [{"name": c, "dtype": str(frame[c].dtype), "unique_count": int(frame[c].nunique(dropna=True))} for c in frame.columns],
                "quality": quality_report,
            },
            "models": [],
        }

        for algorithm in ALGORITHMS[slug]:
            model_dir = STORAGE / "models" / slug / algorithm / "v1"
            model_dir.mkdir(parents=True, exist_ok=True)
            config = {
                "problem_type": problem_type,
                "algorithm_key": algorithm,
                "features": features,
                "target_column": target,
                "test_size": 0.20,
                "random_state": 42,
                "cross_validation": 3,
                "generate_extended_charts": False,
                "parameters": DEFAULTS[algorithm],
                "preprocessing": {
                    "remove_duplicates": True,
                    "numeric_imputation": "median",
                    "categorical_imputation": "most_frequent",
                    "apply_scaling": algorithm in {"logistic_regression", "knn", "svm"},
                    "one_hot_encode": True,
                },
            }
            config_path = model_dir / "config.json"
            config_path.write_text(json.dumps(config), encoding="utf-8")
            runner.train(dataset_path, config_path, model_dir)
            result = json.loads((model_dir / "result.json").read_text(encoding="utf-8"))
            for disposable in ["progress.json", "result.json", "config.json"]:
                (model_dir / disposable).unlink(missing_ok=True)
            dataset_entry["models"].append({
                "algorithm_key": algorithm,
                "artifact_path": f"ml/system/models/{slug}/{algorithm}/v1/model.joblib",
                "metadata_path": f"ml/system/models/{slug}/{algorithm}/v1/metadata.json",
                "metrics": result["metrics"],
                "training_summary": result["training_summary"],
                "feature_importance": result["feature_importance"],
                "prediction_schema": result["prediction_schema"],
                "visualizations": {k: f"ml/system/models/{slug}/{algorithm}/v1/{v}" for k, v in result["visualizations"].items()},
                "training_time_ms": result["training_time_ms"],
                "python_version": result["python_version"],
                "sklearn_version": result["sklearn_version"],
                "parameters": result["parameters"],
                "preprocessing": result["preprocessing"],
            })

        metric = "f1" if problem_type == "classification" else "rmse"
        reverse = problem_type == "classification"
        dataset_entry["models"].sort(key=lambda m: (m["metrics"].get(metric) is not None, m["metrics"].get(metric, -1 if reverse else math.inf)), reverse=reverse)
        if problem_type == "regression":
            dataset_entry["models"].sort(key=lambda m: m["metrics"].get("rmse", math.inf))
        dataset_entry["primary_algorithm"] = dataset_entry["models"][0]["algorithm_key"]
        for rank, model in enumerate(dataset_entry["models"], 1):
            model["benchmark_rank"] = rank
            model["is_primary"] = rank == 1
        manifest["datasets"].append(dataset_entry)
        print(f"Built {slug}: {len(dataset_entry['models'])} models")

    (STORAGE / "manifest.json").write_text(json.dumps(manifest, ensure_ascii=False, indent=2), encoding="utf-8")
    print(f"Manifest: {STORAGE / 'manifest.json'}")


if __name__ == "__main__":
    import sys
    main()
