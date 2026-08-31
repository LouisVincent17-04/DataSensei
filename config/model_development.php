<?php

return [
    'max_dataset_rows' => (int) env('MODEL_DEVELOPMENT_MAX_ROWS', 1000),
    'max_dataset_columns' => (int) env('MODEL_DEVELOPMENT_MAX_COLUMNS', 50),
    'max_upload_kilobytes' => (int) env('MODEL_DEVELOPMENT_MAX_UPLOAD_KB', 5120),
    'max_features' => (int) env('MODEL_DEVELOPMENT_MAX_FEATURES', 12),
    'max_categories_per_feature' => (int) env('MODEL_DEVELOPMENT_MAX_CATEGORIES', 50),
    'timeout_seconds' => (int) env('MODEL_DEVELOPMENT_TIMEOUT', 25),
    'allowed_test_sizes' => [0.10, 0.20, 0.25, 0.30],
    'allowed_random_seeds' => [7, 21, 42, 100],
    'default_random_seed' => 42,
    'max_predictions_per_run' => (int) env('MODEL_DEVELOPMENT_MAX_PREDICTIONS', 30),
];
