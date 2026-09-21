<?php

namespace App\Support;

/**
 * Turns raw model output ("malignant", 0.9731) into sentences a beginner can
 * read ("Likely breast cancer. The model is very sure: 97 out of 100.").
 *
 * Built-in datasets have hand-written wording. Uploaded datasets fall back to
 * rules that only use the column name and the answer values, so every model
 * gets a readable result.
 */
final class ModelDevelopmentOutcome
{
    /** Answer values that usually mean "yes, the thing happened". */
    private const POSITIVE_TOKENS = [
        '1', '1.0', 'yes', 'y', 'true', 't', 'positive', 'pos', 'malignant', 'churned', 'churn',
        'default', 'fraud', 'spam', 'disease', 'sick', 'fail', 'failed', 'died', 'dead', 'abnormal',
    ];

    private const NEGATIVE_TOKENS = [
        '0', '0.0', 'no', 'n', 'false', 'f', 'negative', 'neg', 'benign', 'stayed', 'normal',
        'healthy', 'pass', 'passed', 'survived', 'alive', 'ham', 'legit',
    ];

    /**
     * Hand-written wording for the built-in datasets, keyed by dataset slug.
     *
     * @return array<string, array<string,mixed>>
     */
    public static function presets(): array
    {
        return [
            'breast-cancer-wisconsin' => [
                'question' => 'Is this tumour breast cancer?',
                'story' => 'Doctors measured cell samples from breast lumps. Your model learns to tell cancerous lumps from harmless ones.',
                'subject' => 'this tumour',
                'positive' => 'malignant',
                'labels' => [
                    'malignant' => ['headline' => 'Likely breast cancer', 'detail' => 'These measurements look like a malignant (cancerous) tumour.', 'short' => 'Cancer (malignant)'],
                    'benign' => ['headline' => 'Likely not breast cancer', 'detail' => 'These measurements look like a benign (harmless) lump.', 'short' => 'Not cancer (benign)'],
                ],
                'missed' => 'cancers the model called harmless',
                'false_alarm' => 'harmless lumps the model called cancer',
                'caution' => 'This is a classroom model, not a medical test. Only a doctor can diagnose cancer.',
                'fields' => [
                    'mean_radius' => ['Average cell size (radius)', 'Average distance from the centre of a cell nucleus to its edge. Cancer cells tend to be larger.'],
                    'mean_texture' => ['Average texture', 'How much the grey shading varies inside the cells. Higher means a rougher look.'],
                    'mean_perimeter' => ['Average outline length', 'Average length around the edge of a cell nucleus.'],
                    'mean_area' => ['Average cell area', 'Average area of a cell nucleus. Larger areas are more common in cancer.'],
                    'mean_smoothness' => ['Average smoothness', 'How even the cell outline is. Lower values mean smoother edges.'],
                    'mean_compactness' => ['Average compactness', 'How tightly packed the cell shape is compared with a circle.'],
                    'mean_concavity' => ['Average dents in the outline', 'How deep the inward dents in the cell outline are. Deeper dents are a warning sign.'],
                    'mean_concave_points' => ['Average number of dents', 'How many inward dents the cell outline has.'],
                    'worst_radius' => ['Largest cell size (radius)', 'The size of the biggest cells in the sample.'],
                    'worst_texture' => ['Roughest texture', 'The most uneven shading found in the sample.'],
                    'worst_perimeter' => ['Longest outline', 'Outline length of the biggest cells in the sample.'],
                    'worst_area' => ['Largest cell area', 'Area of the biggest cells in the sample.'],
                ],
            ],
            'heart-disease' => [
                'question' => 'Does this person have heart disease?',
                'story' => 'Each row is one patient check-up. Your model learns which measurements go with heart disease.',
                'subject' => 'this person',
                'positive' => '1',
                'labels' => [
                    '1' => ['headline' => 'Likely has heart disease', 'detail' => 'These check-up values look like patients who had heart disease.', 'short' => 'Heart disease'],
                    '0' => ['headline' => 'Likely no heart disease', 'detail' => 'These check-up values look like patients with a healthy heart.', 'short' => 'No heart disease'],
                ],
                'missed' => 'patients with heart disease the model called healthy',
                'false_alarm' => 'healthy patients the model flagged',
                'caution' => 'This is a classroom model, not a medical test.',
                'fields' => [
                    'age' => ['Age', 'Age in years.'],
                    'sex' => ['Sex', 'Sex recorded for the patient.'],
                    'chest_pain_type' => ['Type of chest pain', 'The kind of chest pain reported. "asymptomatic" means no pain was felt.'],
                    'resting_blood_pressure' => ['Resting blood pressure', 'Blood pressure while resting, in mm Hg. Around 120 is typical.'],
                    'cholesterol' => ['Cholesterol', 'Cholesterol in the blood, in mg/dl. Under 200 is considered healthy.'],
                    'maximum_heart_rate' => ['Highest heart rate reached', 'Fastest heartbeat reached during an exercise test.'],
                    'exercise_induced_angina' => ['Chest pain during exercise', 'Whether exercise brought on chest pain.'],
                    'st_depression' => ['ECG dip during exercise', 'How far part of the heart tracing dropped during exercise. Bigger dips are a warning sign.'],
                ],
            ],
            'titanic' => [
                'question' => 'Would this passenger have survived the Titanic?',
                'story' => 'Each row is one passenger. Your model learns who was more likely to survive.',
                'subject' => 'this passenger',
                'positive' => '1',
                'labels' => [
                    '1' => ['headline' => 'Likely survived', 'detail' => 'Passengers like this one usually survived.', 'short' => 'Survived'],
                    '0' => ['headline' => 'Likely did not survive', 'detail' => 'Passengers like this one usually did not survive.', 'short' => 'Did not survive'],
                ],
                'missed' => 'survivors the model expected to die',
                'false_alarm' => 'passengers the model wrongly expected to survive',
                'fields' => [
                    'passenger_class' => ['Ticket class', '1 is first class (most expensive) and 3 is third class.'],
                    'sex' => ['Sex', 'Sex of the passenger.'],
                    'age' => ['Age', 'Age in years.'],
                    'siblings_spouses' => ['Siblings or spouse aboard', 'How many brothers, sisters, or a husband or wife travelled with them.'],
                    'parents_children' => ['Parents or children aboard', 'How many parents or children travelled with them.'],
                    'fare' => ['Ticket price', 'How much the ticket cost.'],
                    'embarked' => ['Boarding port', 'S is Southampton, C is Cherbourg, Q is Queenstown.'],
                ],
            ],
            'customer-churn' => [
                'question' => 'Will this customer cancel their subscription?',
                'story' => 'Each row is one subscriber. Your model learns the signs that a customer is about to leave.',
                'subject' => 'this customer',
                'positive' => '1',
                'labels' => [
                    '1' => ['headline' => 'Likely to cancel', 'detail' => 'Customers like this one usually left the service.', 'short' => 'Will cancel'],
                    '0' => ['headline' => 'Likely to stay', 'detail' => 'Customers like this one usually stayed.', 'short' => 'Will stay'],
                ],
                'missed' => 'leaving customers the model expected to stay',
                'false_alarm' => 'loyal customers the model flagged',
                'fields' => [
                    'tenure_months' => ['Months as a customer', 'How long they have been subscribed.'],
                    'contract_type' => ['Contract', 'How long their contract lasts.'],
                    'internet_service' => ['Internet type', 'The kind of connection they pay for.'],
                    'technical_support' => ['Has tech support', 'Whether their plan includes technical support.'],
                    'monthly_charge' => ['Monthly bill', 'What they pay each month.'],
                    'late_payments' => ['Late payments', 'How many bills they paid late.'],
                    'satisfaction_score' => ['Satisfaction (1 to 5)', 'Their survey rating. 5 is very happy.'],
                ],
            ],
            'iris' => [
                'question' => 'Which iris species is this flower?',
                'story' => 'Each row is one flower with four petal and sepal measurements. Your model learns to name the species.',
                'subject' => 'this flower',
                'labels' => [
                    'setosa' => ['headline' => 'Likely Iris setosa', 'detail' => 'Small petals like these are typical of setosa.', 'short' => 'Setosa'],
                    'versicolor' => ['headline' => 'Likely Iris versicolor', 'detail' => 'Medium petals like these are typical of versicolor.', 'short' => 'Versicolor'],
                    'virginica' => ['headline' => 'Likely Iris virginica', 'detail' => 'Large petals like these are typical of virginica.', 'short' => 'Virginica'],
                ],
                'fields' => [
                    'sepal_length_cm' => ['Sepal length (cm)', 'Sepals are the green leaf-like parts under the petals.'],
                    'sepal_width_cm' => ['Sepal width (cm)', 'Width of the green leaf-like part under the petals.'],
                    'petal_length_cm' => ['Petal length (cm)', 'Length of a coloured petal.'],
                    'petal_width_cm' => ['Petal width (cm)', 'Width of a coloured petal.'],
                ],
            ],
            'wine-quality' => [
                'question' => 'Is this wine low, medium or high quality?',
                'story' => 'Each row is one wine with its lab measurements. Your model learns which chemistry goes with better wine.',
                'subject' => 'this wine',
                'labels' => [
                    'low' => ['headline' => 'Likely low quality', 'detail' => 'Wines with this chemistry were usually rated low.', 'short' => 'Low quality'],
                    'medium' => ['headline' => 'Likely medium quality', 'detail' => 'Wines with this chemistry were usually rated medium.', 'short' => 'Medium quality'],
                    'high' => ['headline' => 'Likely high quality', 'detail' => 'Wines with this chemistry were usually rated high.', 'short' => 'High quality'],
                ],
                // quality_label is made from quality_score, so the score gives the answer away.
                'leak_columns' => ['quality_score'],
                'fields' => [
                    'fixed_acidity' => ['Fixed acidity', 'Acids that do not evaporate. They give wine its sharpness.'],
                    'volatile_acidity' => ['Vinegar-like acidity', 'Too much makes wine taste like vinegar.'],
                    'residual_sugar' => ['Leftover sugar', 'Sugar left after fermentation. Higher means sweeter.'],
                    'chlorides' => ['Salt', 'The amount of salt in the wine.'],
                    'density' => ['Density', 'How heavy the wine is compared with water.'],
                    'sulphates' => ['Sulphates', 'A preservative that keeps wine fresh.'],
                    'alcohol' => ['Alcohol (%)', 'Alcohol by volume.'],
                ],
            ],
            'student-performance' => [
                'question' => 'What final score will this student get?',
                'story' => 'Each row is one student. Your model learns how habits and earlier grades relate to the final score.',
                'subject' => 'this student',
                'answer_name' => 'final score',
                'unit_suffix' => ' points',
                'fields' => [
                    'study_hours_weekly' => ['Study hours per week', 'Hours spent studying in a normal week.'],
                    'attendance_rate' => ['Attendance (%)', 'Share of classes attended.'],
                    'sleep_hours' => ['Sleep per night (hours)', 'Average hours of sleep.'],
                    'prior_grade' => ['Previous grade', 'Grade from the previous term, out of 100.'],
                    'assignment_average' => ['Assignment average', 'Average assignment mark, out of 100.'],
                    'internet_access' => ['Internet at home', 'How reliable their internet connection is.'],
                ],
            ],
            'housing-prices' => [
                'question' => 'How much is this home worth?',
                'story' => 'Each row is one home that was sold. Your model learns how size and location affect the price.',
                'subject' => 'this home',
                'answer_name' => 'price',
                'fields' => [
                    'floor_area_sqm' => ['Floor area (m²)', 'Living space in square metres.'],
                    'bedrooms' => ['Bedrooms', 'Number of bedrooms.'],
                    'bathrooms' => ['Bathrooms', 'Number of bathrooms.'],
                    'property_age_years' => ['Age of the home (years)', 'Years since it was built.'],
                    'distance_to_center_km' => ['Distance to city centre (km)', 'Closer homes usually cost more.'],
                    'location_type' => ['Area type', 'Whether it is in the city, the suburbs or the countryside.'],
                    'parking_spaces' => ['Parking spaces', 'Number of parking spaces.'],
                ],
            ],
            'sales-forecast' => [
                'question' => 'How much will we sell on this day?',
                'story' => 'Each row is one day of sales for one product group. Your model learns what drives sales up or down.',
                'subject' => 'this day',
                'answer_name' => 'sales',
                'fields' => [
                    'day_of_week' => ['Day of the week', 'Weekends often sell differently from weekdays.'],
                    'month' => ['Month (1 to 12)', '1 is January and 12 is December.'],
                    'product_category' => ['Product group', 'The kind of product being sold.'],
                    'promotion' => ['Promotion running', 'Whether a discount or advert was running that day.'],
                    'unit_price' => ['Price per item', 'What one item costs.'],
                    'website_visits' => ['Website visits', 'How many people visited the shop online that day.'],
                ],
            ],
            'diabetes' => [
                'question' => 'How much will this patient\'s diabetes progress in a year?',
                'story' => 'Each row is one patient. The answer is a score for how much the illness advanced after one year. Higher means worse.',
                'subject' => 'this patient',
                'answer_name' => 'progression score',
                'caution' => 'This is a classroom model, not a medical test.',
                'fields' => [
                    'age' => ['Age', 'Age in years.'],
                    'sex' => ['Sex (1 or 2)', 'Recorded as a code in the original study.'],
                    'bmi' => ['Body mass index', 'Weight compared with height. 18.5 to 25 is the healthy range.'],
                    'bp' => ['Average blood pressure', 'Average blood pressure reading.'],
                    's1' => ['Total cholesterol', 'Blood test result.'],
                    's2' => ['LDL ("bad") cholesterol', 'Blood test result.'],
                    's3' => ['HDL ("good") cholesterol', 'Blood test result.'],
                    's4' => ['Cholesterol ratio', 'Total cholesterol divided by HDL.'],
                    's5' => ['Blood fats (triglycerides)', 'Blood test result, on a log scale.'],
                    's6' => ['Blood sugar', 'Blood sugar level.'],
                ],
            ],
        ];
    }

    /**
     * Wording for one model.
     *
     * @param array<int,string> $classLabels
     * @return array<string,mixed>
     */
    public static function wording(?string $datasetSlug, string $problemType, ?string $target, array $classLabels = []): array
    {
        $preset = self::presets()[(string) $datasetSlug] ?? [];
        $targetName = self::humanize((string) $target);
        $classLabels = array_values(array_map('strval', $classLabels));

        $wording = [
            'question' => (string) ($preset['question'] ?? self::defaultQuestion($problemType, $targetName)),
            'story' => (string) ($preset['story'] ?? ''),
            'subject' => (string) ($preset['subject'] ?? 'this example'),
            'answer_name' => (string) ($preset['answer_name'] ?? ($targetName !== '' ? strtolower($targetName) : 'answer')),
            'unit_prefix' => (string) ($preset['unit_prefix'] ?? ''),
            'unit_suffix' => (string) ($preset['unit_suffix'] ?? ''),
            'caution' => $preset['caution'] ?? null,
            'fields' => (array) ($preset['fields'] ?? []),
            'leak_columns' => array_values((array) ($preset['leak_columns'] ?? [])),
            'positive' => null,
            'labels' => [],
            'missed' => (string) ($preset['missed'] ?? 'real "yes" cases the model missed'),
            'false_alarm' => (string) ($preset['false_alarm'] ?? 'false alarms (the model said "yes" by mistake)'),
        ];

        if ($problemType !== 'classification') {
            return $wording;
        }

        // A preset only applies when the student kept the dataset's own answer column.
        $presetLabels = (array) ($preset['labels'] ?? []);
        $presetFits = $presetLabels !== [] && $classLabels !== []
            && array_diff($classLabels, array_map('strval', array_keys($presetLabels))) === [];

        if ($presetFits) {
            foreach ($classLabels as $label) {
                $wording['labels'][$label] = $presetLabels[$label];
            }
            $positive = isset($preset['positive']) ? (string) $preset['positive'] : null;
            $wording['positive'] = $positive !== null && in_array($positive, $classLabels, true) && count($classLabels) === 2
                ? $positive
                : null;
        } else {
            if ($presetLabels !== []) {
                $wording['question'] = self::defaultQuestion($problemType, $targetName);
                $wording['caution'] = null;
            }
            $wording['positive'] = self::guessPositive($classLabels);
            if ($wording['positive'] !== null && $targetName !== '' && $presetLabels === []) {
                $wording['question'] = $targetName.': yes or no?';
            }
            foreach ($classLabels as $label) {
                $wording['labels'][$label] = self::genericLabel($label, $targetName, $wording['positive'], $classLabels);
            }
            $wording['missed'] = 'real "yes" cases the model missed';
            $wording['false_alarm'] = 'false alarms (the model said "yes" by mistake)';
        }

        foreach ($wording['labels'] as $label => $copy) {
            $wording['labels'][$label]['tone'] = $wording['positive'] === null
                ? 'neutral'
                : ((string) $label === $wording['positive'] ? 'alert' : 'calm');
        }

        return $wording;
    }

    /**
     * A reader-friendly version of one prediction.
     *
     * @param array<string,mixed> $wording
     * @param array<string,mixed> $result   Output of the trusted runner.
     * @param array<string,mixed> $metrics  Stored metrics of the model version.
     * @return array<string,mixed>
     */
    public static function describePrediction(array $wording, string $problemType, array $result, array $metrics = []): array
    {
        $raw = $result['predicted_value'] ?? null;
        $description = [
            'question' => (string) ($wording['question'] ?? ''),
            'headline' => is_scalar($raw) ? (string) $raw : 'Prediction ready',
            'detail' => '',
            'tone' => 'neutral',
            'raw' => is_scalar($raw) ? (string) $raw : '',
            'confidence' => null,
            'confidence_label' => null,
            'confidence_text' => null,
            'chances' => [],
            'caution' => $wording['caution'] ?? null,
        ];

        if ($problemType === 'classification') {
            $label = (string) $raw;
            $copy = (array) ($wording['labels'][$label] ?? self::genericLabel($label, '', null, []));
            $description['headline'] = (string) ($copy['headline'] ?? 'Likely '.$label);
            $description['detail'] = (string) ($copy['detail'] ?? '');
            $description['tone'] = (string) ($copy['tone'] ?? 'neutral');

            $confidence = is_numeric($result['confidence'] ?? null) ? (float) $result['confidence'] : null;
            if ($confidence !== null) {
                $description['confidence'] = $confidence;
                $description['confidence_label'] = self::confidenceLabel($confidence);
                $description['confidence_text'] = $confidence >= 99.5
                    ? 'More than 99 out of 100'
                    : 'About '.(int) round($confidence).' out of 100';
                if ($confidence < 60) {
                    $description['headline'] = 'Hard to say, leaning towards: '.lcfirst(self::stripLikely($description['headline']));
                }
            }

            $chances = [];
            foreach ((array) ($result['probabilities'] ?? []) as $class => $percent) {
                if (! is_numeric($percent)) {
                    continue;
                }
                $chances[] = [
                    'label' => (string) ($wording['labels'][(string) $class]['short'] ?? $class),
                    'percent' => round((float) $percent, 1),
                    'chosen' => (string) $class === $label,
                ];
            }
            usort($chances, static fn (array $a, array $b): int => $b['percent'] <=> $a['percent']);
            $description['chances'] = array_slice($chances, 0, 6);

            return $description;
        }

        if ($problemType === 'regression') {
            $value = is_numeric($raw) ? (float) $raw : null;
            $formatted = $value === null ? '—' : self::formatNumber($value);
            $description['headline'] = 'About '.($wording['unit_prefix'] ?? '').$formatted.($wording['unit_suffix'] ?? '');
            $description['detail'] = 'This is the model\'s estimate of the '.($wording['answer_name'] ?? 'answer').' for '.($wording['subject'] ?? 'this example').'.';
            if ($value !== null && is_numeric($metrics['mae'] ?? null) && (float) $metrics['mae'] > 0) {
                $mae = (float) $metrics['mae'];
                $description['confidence_text'] = 'Usually within '.self::formatNumber($mae).' of the real value, so expect roughly '
                    .self::formatNumber($value - $mae).' to '.self::formatNumber($value + $mae).'.';
            }

            return $description;
        }

        $description['headline'] = 'Belongs to '.(string) ($raw ?: 'a group');
        $description['detail'] = 'Its values are closest to the centre of this group. Group numbers are just names, so look at the rows in the group to see what they share.';

        return $description;
    }

    /**
     * The results page opens with these sentences instead of a wall of metrics.
     *
     * @param array<string,mixed> $wording
     * @param array<string,mixed> $metrics
     * @return array{headline:string,lines:array<int,string>,mistakes:?array<string,int|string>,suspicious:bool}
     */
    public static function summarizeResults(array $wording, string $problemType, array $metrics): array
    {
        $lines = [];
        $mistakes = null;
        $suspicious = false;

        if ($problemType === 'classification') {
            $accuracy = is_numeric($metrics['accuracy'] ?? null) ? (float) $metrics['accuracy'] : null;
            $headline = $accuracy === null
                ? 'Your model is trained'
                : 'Right about '.(int) round($accuracy).' times out of 100';

            $matrix = (array) ($metrics['confusion_matrix'] ?? []);
            $labels = array_values(array_map('strval', (array) ($matrix['labels'] ?? [])));
            $values = array_values((array) ($matrix['values'] ?? []));
            $total = 0;
            $correct = 0;
            foreach ($values as $i => $row) {
                foreach (array_values((array) $row) as $j => $count) {
                    $total += (int) $count;
                    if ($i === $j) {
                        $correct += (int) $count;
                    }
                }
            }
            if ($total > 0) {
                $lines[] = 'On '.number_format($total).' hidden test rows it got '.number_format($correct).' right and '.number_format($total - $correct).' wrong.';
            }

            $baseline = is_numeric($metrics['baseline_accuracy'] ?? null) ? (float) $metrics['baseline_accuracy'] : null;
            if ($accuracy !== null && $baseline !== null) {
                $common = (string) ($wording['labels'][(string) ($metrics['baseline_label'] ?? '')]['short'] ?? ($metrics['baseline_label'] ?? 'the most common answer'));
                $gain = $accuracy - $baseline;
                $lines[] = $gain >= 3
                    ? 'Always answering "'.$common.'" would be right '.(int) round($baseline).' times out of 100, so the model really learned something.'
                    : 'Careful: always answering "'.$common.'" would already be right '.(int) round($baseline).' times out of 100, so this model adds little.';
            }

            $positive = $wording['positive'] ?? null;
            if ($positive !== null && count($labels) === 2 && in_array($positive, $labels, true)) {
                $p = array_search($positive, $labels, true);
                $n = $p === 0 ? 1 : 0;
                $mistakes = [
                    'missed' => (int) ($values[$p][$n] ?? 0),
                    'false_alarms' => (int) ($values[$n][$p] ?? 0),
                    'missed_text' => (string) $wording['missed'],
                    'false_alarm_text' => (string) $wording['false_alarm'],
                ];
            }

            $suspicious = $accuracy !== null && $accuracy >= 99.5 && $total >= 30;

            return ['headline' => $headline, 'lines' => $lines, 'mistakes' => $mistakes, 'suspicious' => $suspicious];
        }

        if ($problemType === 'regression') {
            $mae = is_numeric($metrics['mae'] ?? null) ? (float) $metrics['mae'] : null;
            $headline = $mae === null
                ? 'Your model is trained'
                : 'Usually within '.self::formatNumber($mae).' of the real '.($wording['answer_name'] ?? 'answer');
            $baseline = is_numeric($metrics['baseline_mae'] ?? null) ? (float) $metrics['baseline_mae'] : null;
            if ($mae !== null && $baseline !== null && $baseline > 0) {
                $lines[] = $mae < $baseline * 0.9
                    ? 'Always guessing the average would miss by about '.self::formatNumber($baseline).', so the model cut the typical miss by '.(int) round((1 - $mae / $baseline) * 100).'%.'
                    : 'Careful: always guessing the average would miss by about '.self::formatNumber($baseline).', so this model adds little.';
            }
            $r2 = is_numeric($metrics['r2'] ?? null) ? (float) $metrics['r2'] : null;
            if ($r2 !== null) {
                $lines[] = 'It explains about '.max(0, (int) round($r2 * 100)).'% of why the '.($wording['answer_name'] ?? 'answer').' goes up and down.';
                $suspicious = $r2 >= 0.999;
            }

            return ['headline' => $headline, 'lines' => $lines, 'mistakes' => null, 'suspicious' => $suspicious];
        }

        $count = (int) ($metrics['cluster_count'] ?? 0);
        $silhouette = is_numeric($metrics['silhouette'] ?? null) ? (float) $metrics['silhouette'] : null;
        $lines[] = match (true) {
            $silhouette === null => 'The groups were created, but their separation could not be scored.',
            $silhouette >= 0.5 => 'The groups are clearly separated from each other.',
            $silhouette >= 0.25 => 'The groups overlap a little. Check the chart before naming them.',
            default => 'The groups overlap a lot. Try fewer groups or different columns.',
        };

        return ['headline' => 'Found '.$count.' groups of similar rows', 'lines' => $lines, 'mistakes' => null, 'suspicious' => false];
    }

    /**
     * Label and one-line help for a prediction input.
     *
     * @param array<string,mixed> $wording
     * @return array{label:string,help:string}
     */
    public static function field(array $wording, string $column): array
    {
        $entry = $wording['fields'][$column] ?? null;
        if (is_array($entry) && isset($entry[0])) {
            return ['label' => (string) $entry[0], 'help' => (string) ($entry[1] ?? '')];
        }

        return ['label' => self::humanize($column), 'help' => ''];
    }

    /** "mean_concave_points" becomes "Mean concave points". */
    public static function humanize(string $column): string
    {
        $text = trim((string) preg_replace('/[_\-\.]+/', ' ', $column));
        $text = (string) preg_replace('/(?<=[a-z])(?=[A-Z])/', ' ', $text);
        $text = (string) preg_replace('/\s+/', ' ', $text);

        return $text === '' ? '' : ucfirst(strtolower($text));
    }

    public static function confidenceLabel(float $percent): string
    {
        return match (true) {
            $percent >= 90 => 'Very sure',
            $percent >= 75 => 'Fairly sure',
            $percent >= 60 => 'Leaning this way',
            default => 'Not sure, it is a close call',
        };
    }

    public static function formatNumber(float $value): string
    {
        $absolute = abs($value);
        $decimals = match (true) {
            $absolute >= 1000 => 0,
            $absolute >= 100 => 1,
            $absolute >= 1 => 2,
            default => 3,
        };
        $text = number_format($value, $decimals);

        return str_contains($text, '.') ? rtrim(rtrim($text, '0'), '.') : $text;
    }

    /** @param array<int,string> $labels */
    private static function guessPositive(array $labels): ?string
    {
        if (count($labels) !== 2) {
            return null;
        }
        foreach ($labels as $label) {
            if (in_array(strtolower(trim($label)), self::POSITIVE_TOKENS, true)) {
                return $label;
            }
        }

        return null;
    }

    /**
     * @param array<int,string> $labels
     * @return array{headline:string,detail:string,short:string}
     */
    private static function genericLabel(string $label, string $targetName, ?string $positive, array $labels): array
    {
        $token = strtolower(trim($label));
        $name = $targetName !== '' ? $targetName : 'Answer';
        $isYes = $positive !== null && $label === $positive;
        $isNo = $positive !== null && $label !== $positive
            && (in_array($token, self::NEGATIVE_TOKENS, true) || in_array(strtolower($positive), ['1', '1.0', 'yes', 'y', 'true', 't'], true));

        if ($isYes && in_array($token, ['1', '1.0', 'yes', 'y', 'true', 't'], true)) {
            return ['headline' => 'Likely yes: '.lcfirst($name), 'detail' => 'The model expects "'.$name.'" to be '.$label.' (yes).', 'short' => 'Yes'];
        }
        if ($isNo && in_array($token, ['0', '0.0', 'no', 'n', 'false', 'f'], true)) {
            return ['headline' => 'Likely no: not '.lcfirst($name), 'detail' => 'The model expects "'.$name.'" to be '.$label.' (no).', 'short' => 'No'];
        }

        return [
            'headline' => 'Likely '.$label,
            'detail' => 'The model expects "'.$name.'" to be "'.$label.'".',
            'short' => ucfirst($label),
        ];
    }

    private static function defaultQuestion(string $problemType, string $targetName): string
    {
        return match ($problemType) {
            'classification' => $targetName !== '' ? 'What is the '.lcfirst($targetName).'?' : 'Which group does it belong to?',
            'regression' => $targetName !== '' ? 'How much is the '.lcfirst($targetName).'?' : 'What number do we expect?',
            default => 'Which rows are similar to each other?',
        };
    }

    private static function stripLikely(string $headline): string
    {
        return (string) preg_replace('/^Likely\s+/i', '', $headline);
    }
}
