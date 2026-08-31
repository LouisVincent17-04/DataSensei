<?php

namespace App\Services\HybridMl;

class EducationalExplanationService
{
    /**
     * @param array<string,mixed> $metrics
     * @param array<string,mixed> $qualitySummary
     * @param array<string,mixed> $trainingSummary
     * @return array<int,string>
     */
    public function explain(string $problemType, string $algorithm, array $metrics, array $qualitySummary, array $trainingSummary): array
    {
        $items = [];
        $rows = (int) ($trainingSummary['rows_used'] ?? $qualitySummary['rows'] ?? 0);
        $trainRows = (int) ($trainingSummary['train_rows'] ?? 0);
        $testRows = (int) ($trainingSummary['test_rows'] ?? 0);
        $imbalance = (float) data_get($qualitySummary, 'class_balance.imbalance_ratio', 1.0);

        if ($rows < 120) {
            $items[] = "Only {$rows} usable rows were available. A small dataset increases variance, so cross-validation and a simpler model are safer than aggressive tuning.";
        }
        if ($testRows > 0) {
            $items[] = "The evaluation used {$trainRows} training rows and {$testRows} held-out test rows. Test metrics estimate performance on unseen records rather than memorization of the training set.";
        }

        if ($problemType === 'classification') {
            $accuracy = (float) ($metrics['accuracy'] ?? 0);
            $f1 = (float) ($metrics['f1'] ?? 0);
            $rocAuc = $metrics['roc_auc'] ?? null;
            if ($imbalance < 0.50 && abs($accuracy - $f1) > 3) {
                $items[] = 'Accuracy and F1 differ noticeably because the target classes are imbalanced. The F1 score and precision-recall curve are more informative than accuracy alone.';
            }
            if ($rocAuc !== null && (float) $rocAuc >= 0.90) {
                $items[] = 'ROC AUC is high, meaning the model generally ranks positive examples ahead of negative examples across many probability thresholds.';
            }
            if ($algorithm === 'random_forest' && $f1 > 80) {
                $items[] = 'Random Forest likely benefited from nonlinear feature interactions. Its many trees reduce the instability of a single decision tree.';
            }
            if ($algorithm === 'logistic_regression' && $f1 >= 75) {
                $items[] = 'The linear Logistic Regression baseline performed competitively, suggesting that much of the class separation can be explained by additive feature effects.';
            }
        } elseif ($problemType === 'regression') {
            $r2 = (float) ($metrics['r2'] ?? 0);
            $rmse = (float) ($metrics['rmse'] ?? 0);
            if ($r2 < 0) {
                $items[] = 'R² is below zero, so this model generalized worse than simply predicting the test-set mean. Revisit the target, features, outliers, and model choice.';
            } elseif ($r2 >= 0.80) {
                $items[] = 'R² is high, so the selected features explain a large share of target variation in the held-out test data. Residual plots should still be checked for systematic errors.';
            } else {
                $items[] = "RMSE is {$this->format($rmse)} in the target's original unit. Use this value to judge whether the typical error is acceptable in the dataset's real context.";
            }
            if ($algorithm === 'random_forest_regressor' && $r2 > 0.60) {
                $items[] = 'The Random Forest Regressor can capture nonlinear relationships and feature interactions that a straight-line model may miss.';
            }
        } else {
            $silhouette = $metrics['silhouette'] ?? null;
            if ($silhouette !== null) {
                $silhouette = (float) $silhouette;
                $items[] = match (true) {
                    $silhouette >= 0.50 => 'The silhouette score indicates reasonably separated clusters.',
                    $silhouette >= 0.25 => 'The clusters overlap somewhat; use the visualization and domain meaning before assigning labels to them.',
                    default => 'The low silhouette score indicates weak cluster separation. Try different features, scaling, or a different cluster count.',
                };
            }
        }

        $missing = (float) ($qualitySummary['missing_percent'] ?? 0);
        if ($missing > 5) {
            $items[] = "The dataset contains {$this->format($missing)}% missing values. The reported model therefore depends partly on the selected imputation strategy.";
        }
        $duplicates = (int) ($qualitySummary['duplicate_rows'] ?? 0);
        if ($duplicates > 0) {
            $items[] = "The quality report found {$duplicates} duplicate rows. Removing duplicates prevents repeated records from receiving extra influence.";
        }

        return array_values(array_unique($items));
    }

    /** @return array<int,string> */
    public function predictionExplanation(string $problemType, array $prediction, array $topContributions = []): array
    {
        $items = [];
        if ($problemType === 'classification') {
            $confidence = $prediction['confidence'] ?? null;
            $items[] = $confidence !== null
                ? 'The selected class had the highest estimated probability at '.round((float) $confidence, 2).'%. Confidence is not the same as certainty.'
                : 'The model selected the class with the strongest learned score. This algorithm does not expose calibrated probabilities.';
        } elseif ($problemType === 'regression') {
            $items[] = 'The predicted value is an estimate based on patterns in the training data. It should not be treated as a guaranteed real-world outcome.';
        } else {
            $items[] = 'The record was assigned to the nearest learned cluster center after preprocessing and scaling.';
        }

        if ($topContributions !== []) {
            $features = array_slice(array_column($topContributions, 'feature'), 0, 3);
            $items[] = 'The strongest model-level influences include '.implode(', ', $features).'. These are global influences and do not prove causation.';
        }
        return $items;
    }

    private function format(float $value): string
    {
        return number_format($value, abs($value) < 10 ? 3 : 2);
    }
}
