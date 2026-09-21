"""Tests for the automatic model search in resources/hybrid-ml/trusted_runner.py.

Run with:  python -m unittest tests/Python/test_trusted_runner_model_search.py
Needs numpy, pandas, scikit-learn, matplotlib and joblib (the ML runner image has them).
"""

from __future__ import annotations

import importlib.util
import json
import sys
import tempfile
import unittest
from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]
RUNNER = ROOT / "resources" / "hybrid-ml" / "trusted_runner.py"
DATASETS = ROOT / "storage" / "app" / "ml" / "system" / "datasets"

try:
    import sklearn  # noqa: F401
    import pandas as pd
    HAVE_ML = True
except Exception:  # pragma: no cover - depends on the machine
    HAVE_ML = False


def load_runner():
    spec = importlib.util.spec_from_file_location("datasensei_trusted_runner", RUNNER)
    module = importlib.util.module_from_spec(spec)
    assert spec.loader is not None
    sys.modules[spec.name] = module
    spec.loader.exec_module(module)
    return module


def base_config(problem_type: str, target: str, features: list[str], algorithm: str, **extra):
    config = {
        "problem_type": problem_type,
        "algorithm_key": algorithm,
        "features": features,
        "target_column": target,
        "test_size": 0.20,
        "random_state": 42,
        "cross_validation": 0,
        "parameters": {},
        "preprocessing": {"scale_mode": "auto", "apply_scaling": False, "numeric_imputation": "median", "remove_duplicates": True},
        "generate_extended_charts": False,
        "search_budget_seconds": 60,
    }
    config.update(extra)
    return config


@unittest.skipUnless(HAVE_ML, "scikit-learn and pandas are required")
class ModelSearchTest(unittest.TestCase):
    @classmethod
    def setUpClass(cls):
        cls.runner = load_runner()
        cls.cancer = DATASETS / "breast-cancer-wisconsin" / "v1" / "dataset.csv"
        cls.housing = DATASETS / "housing-prices" / "v1" / "dataset.csv"
        cls.cancer_features = [c for c in pd.read_csv(cls.cancer, nrows=1).columns if c != "diagnosis"]
        cls.housing_features = [c for c in pd.read_csv(cls.housing, nrows=1).columns if c != "price"]

    def train(self, dataset: Path, config: dict) -> tuple[dict, Path]:
        work = Path(tempfile.mkdtemp(prefix="ds-ml-test-"))
        config_path = work / "config.json"
        config_path.write_text(json.dumps(config), encoding="utf-8")
        self.runner.train(dataset, config_path, work / "out")
        return json.loads((work / "out" / "result.json").read_text(encoding="utf-8")), work / "out"

    def test_automatic_mode_compares_algorithms_and_reports_the_winner(self):
        result, _ = self.train(self.cancer, base_config("classification", "diagnosis", self.cancer_features, "auto_classification"))
        search = result["metrics"]["model_search"]

        self.assertEqual(result["algorithm_key"], "auto_classification")
        self.assertEqual(search["mode"], "automatic")
        self.assertEqual(search["scoring"], "accuracy")
        self.assertGreaterEqual(len(search["candidates"]), 4)
        self.assertEqual(sum(1 for c in search["candidates"] if c.get("selected")), 1)
        self.assertEqual(result["selected_algorithm"], search["selected_algorithm"])
        self.assertIn(result["selected_algorithm"], self.runner.AUTO_CANDIDATES["classification"])
        self.assertEqual(result["training_summary"]["selected_label"], search["selected_label"])

        best = max(c["score"] for c in search["candidates"])
        chosen = next(c for c in search["candidates"] if c.get("selected"))
        self.assertGreaterEqual(chosen["score"], best - self.runner.TIE_MARGIN * 100 - 1e-6)

        self.assertGreaterEqual(result["metrics"]["accuracy"], 92.0)
        self.assertGreater(result["metrics"]["accuracy"], result["metrics"]["baseline_accuracy"] + 20)
        self.assertEqual(result["metrics"]["baseline_label"], "benign")

    def test_the_search_never_sees_the_held_out_test_rows(self):
        seen_lengths = []
        original = self.runner.cross_val_score

        def spy(estimator, X, y, **kwargs):
            seen_lengths.append(len(X))
            return original(estimator, X, y, **kwargs)

        self.runner.cross_val_score = spy
        try:
            result, _ = self.train(self.cancer, base_config("classification", "diagnosis", self.cancer_features, "auto_classification"))
        finally:
            self.runner.cross_val_score = original

        train_rows = result["training_summary"]["train_rows"]
        self.assertTrue(seen_lengths)
        self.assertTrue(all(length <= train_rows for length in seen_lengths), (seen_lengths, train_rows))

    def test_simpler_algorithm_wins_a_tie(self):
        # Two candidates inside the tie margin: the one listed first (simpler) must be chosen.
        report_scores = {"logistic_regression": 0.9000, "random_forest": 0.9030}
        original_candidates = dict(self.runner.AUTO_CANDIDATES)
        original_cv = self.runner.cross_val_score
        original_grid = self.runner.parameter_grid
        self.runner.AUTO_CANDIDATES = {"classification": ["logistic_regression", "random_forest"], "regression": original_candidates["regression"]}
        self.runner.parameter_grid = lambda key: [{}]

        def fake(estimator, X, y, **kwargs):
            name = type(estimator.named_steps["model"]).__name__
            key = "logistic_regression" if name == "LogisticRegression" else "random_forest"
            return self.runner.np.array([report_scores[key]] * 3)

        self.runner.cross_val_score = fake
        try:
            result, _ = self.train(self.cancer, base_config("classification", "diagnosis", self.cancer_features, "auto_classification"))
            self.assertEqual(result["selected_algorithm"], "logistic_regression")
            report_scores["random_forest"] = 0.9300
            result, _ = self.train(self.cancer, base_config("classification", "diagnosis", self.cancer_features, "auto_classification"))
            self.assertEqual(result["selected_algorithm"], "random_forest")
        finally:
            self.runner.AUTO_CANDIDATES = original_candidates
            self.runner.cross_val_score = original_cv
            self.runner.parameter_grid = original_grid

    def test_a_named_algorithm_is_tuned_only_when_asked(self):
        tuned, _ = self.train(self.cancer, base_config("classification", "diagnosis", self.cancer_features, "decision_tree", tune=True, parameters={"max_depth": 6, "min_samples_split": 2}))
        self.assertEqual(tuned["selected_algorithm"], "decision_tree")
        self.assertEqual(tuned["metrics"]["model_search"]["mode"], "tuned")
        self.assertEqual(len(tuned["metrics"]["model_search"]["candidates"]), 1)

        exact, _ = self.train(self.cancer, base_config("classification", "diagnosis", self.cancer_features, "decision_tree", parameters={"max_depth": 4, "min_samples_split": 2}))
        self.assertNotIn("model_search", exact["metrics"])
        self.assertEqual(exact["parameters"]["max_depth"], 4)

    def test_automatic_regression_beats_guessing_the_average(self):
        result, _ = self.train(self.housing, base_config("regression", "price", self.housing_features, "auto_regression"))

        self.assertEqual(result["metrics"]["model_search"]["scoring"], "r2")
        self.assertIn(result["selected_algorithm"], self.runner.AUTO_CANDIDATES["regression"])
        self.assertGreaterEqual(result["metrics"]["r2"], 0.85)
        self.assertLess(result["metrics"]["mae"], result["metrics"]["baseline_mae"] * 0.5)

    def test_automatic_choice_must_match_the_problem_type(self):
        with self.assertRaises(ValueError):
            self.train(self.housing, base_config("regression", "price", self.housing_features, "auto_classification"))

    def test_predictions_return_the_chance_of_each_answer_and_schema_hints(self):
        result, out = self.train(self.cancer, base_config("classification", "diagnosis", self.cancer_features, "auto_classification"))
        schema = result["prediction_schema"]
        self.assertIn("low", schema["mean_radius"])
        self.assertIn("high", schema["mean_radius"])
        self.assertFalse(schema["mean_radius"]["whole"])

        row = pd.read_csv(self.cancer).iloc[0]
        payload = out.parent / "input.json"
        payload.write_text(json.dumps({"input_values": {name: float(row[name]) for name in self.cancer_features}}), encoding="utf-8")
        self.runner.predict(out / "model.joblib", payload, out.parent / "prediction")
        prediction = json.loads((out.parent / "prediction" / "prediction.json").read_text(encoding="utf-8"))

        self.assertEqual(prediction["predicted_value"], "malignant")
        self.assertAlmostEqual(sum(prediction["probabilities"].values()), 100.0, places=3)
        self.assertGreater(prediction["confidence"], 90)

    def test_small_whole_number_columns_become_choice_lists(self):
        titanic = DATASETS / "titanic" / "v1" / "dataset.csv"
        features = [c for c in pd.read_csv(titanic, nrows=1).columns if c != "survived"]
        result, _ = self.train(titanic, base_config("classification", "survived", features, "logistic_regression"))

        self.assertEqual(result["prediction_schema"]["passenger_class"]["choices"], [1, 2, 3])
        self.assertIsNone(result["prediction_schema"]["fare"]["choices"])


if __name__ == "__main__":
    unittest.main()
