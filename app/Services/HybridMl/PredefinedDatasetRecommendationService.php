<?php

namespace App\Services\HybridMl;

use App\Models\MlDataset;

class PredefinedDatasetRecommendationService
{
    public function __construct(private readonly AlgorithmCatalogService $algorithms) {}

    /**
     * @param array<string, mixed> $schemaProfile
     * @return array<string, mixed>|null
     */
    public function forDataset(MlDataset $dataset, array $schemaProfile = []): ?array
    {
        $setup = (array) data_get($dataset->metadata, 'recommended_setup', []);
        if ($setup === []) {
            return null;
        }

        $problemType = (string) ($setup['problem_type'] ?? '');
        $algorithmKey = (string) ($setup['algorithm_key'] ?? '');
        $definition = $this->algorithms->definitions()[$algorithmKey] ?? null;
        if (! is_array($definition) || ($definition['problem_type'] ?? null) !== $problemType) {
            return null;
        }

        $headers = array_values(array_map('strval', (array) ($schemaProfile['headers'] ?? [])));
        $target = trim((string) ($setup['target'] ?? $dataset->target_column ?? ''));
        if ($headers !== [] && $target !== '' && ! in_array($target, $headers, true)) {
            return null;
        }

        $features = array_values(array_filter(
            array_map('strval', (array) ($setup['features'] ?? [])),
            static fn (string $feature): bool => $feature !== $target
                && ($headers === [] || in_array($feature, $headers, true))
        ));

        if ($features === []) {
            return null;
        }

        return [
            'objective' => trim((string) ($setup['objective'] ?? $dataset->description ?? 'Build a model from this dataset.')),
            'target' => $target,
            'problem_type' => $problemType,
            'algorithm_key' => $algorithmKey,
            'model_label' => (string) ($definition['label'] ?? str($algorithmKey)->replace('_', ' ')->title()),
            'features' => $features,
            'reason' => trim((string) ($setup['reason'] ?? 'This model is a suitable educational starting point for the dataset.')),
        ];
    }

    /**
     * @param array<string, mixed> $recommendation
     * @param array<string, mixed> $configuration
     * @return array{is_recommended_model: bool, is_full_recommended_setup: bool}
     */
    public function compareToConfiguration(array $recommendation, array $configuration): array
    {
        $recommendedFeatures = array_values(array_map('strval', (array) ($recommendation['features'] ?? [])));
        $selectedFeatures = array_values(array_map('strval', (array) ($configuration['features'] ?? [])));
        sort($recommendedFeatures);
        sort($selectedFeatures);

        $isRecommendedModel = ($configuration['problem_type'] ?? null) === ($recommendation['problem_type'] ?? null)
            && ($configuration['algorithm_key'] ?? null) === ($recommendation['algorithm_key'] ?? null);

        return [
            'is_recommended_model' => $isRecommendedModel,
            'is_full_recommended_setup' => $isRecommendedModel
                && ($configuration['target_column'] ?? null) === ($recommendation['target'] ?? null)
                && $selectedFeatures === $recommendedFeatures,
        ];
    }
}
