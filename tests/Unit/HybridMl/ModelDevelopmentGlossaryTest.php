<?php

namespace Tests\Unit\HybridMl;

use App\Support\ModelDevelopmentGlossary;
use App\Support\ModelDevelopmentGuide;
use Tests\TestCase;

class ModelDevelopmentGlossaryTest extends TestCase
{
    public function test_definitions_are_short_and_free_of_jargon_symbols(): void
    {
        $terms = ModelDevelopmentGlossary::terms();
        $this->assertGreaterThanOrEqual(45, count($terms));

        foreach ($terms as $key => $term) {
            $this->assertNotSame('', trim($term['label']), "{$key} needs a label.");
            $words = str_word_count($term['definition']);
            $this->assertGreaterThanOrEqual(6, $words, "{$key} is too short to explain anything.");
            $this->assertLessThanOrEqual(32, $words, "{$key} should stay a one-breath definition.");
            $this->assertDoesNotMatchRegularExpression('/[=∑√]|\\bTP\\b|\\bFN\\b/u', $term['definition'], "{$key} should not use formulas.");
        }
    }

    public function test_every_metric_shown_to_students_has_a_definition(): void
    {
        foreach (array_keys(ModelDevelopmentGuide::metricGlossary()) as $metric) {
            if (in_array($metric, ['mse', 'cluster_count', 'training_time_ms'], true)) {
                continue;
            }
            $this->assertNotNull(ModelDevelopmentGlossary::find($metric), "{$metric} needs a tooltip definition.");
        }

        foreach (array_keys((array) config('hybrid_ml.algorithms')) as $algorithm) {
            $this->assertNotNull(
                ModelDevelopmentGlossary::find(ModelDevelopmentGlossary::algorithmKey((string) $algorithm)),
                "{$algorithm} needs a tooltip definition."
            );
        }
    }

    public function test_a_term_renders_as_an_accessible_escaped_tooltip(): void
    {
        $first = (string) ModelDevelopmentGlossary::term('accuracy', 'how <often> it is right');
        $second = (string) ModelDevelopmentGlossary::term('Recall');

        $this->assertStringContainsString('<button type="button" class="ml-term-word"', $first);
        $this->assertStringContainsString('role="tooltip"', $first);
        $this->assertStringContainsString('how &lt;often&gt; it is right', $first);
        $this->assertStringContainsString('Out of every 100 answers', $first);

        preg_match('/aria-describedby="([^"]+)"/', $first, $a);
        preg_match('/aria-describedby="([^"]+)"/', $second, $b);
        $this->assertNotSame($a[1], $b[1], 'Each tooltip needs its own id.');
        $this->assertStringContainsString('id="'.$a[1].'"', $first);

        $this->assertSame('not &amp; known', (string) ModelDevelopmentGlossary::term('nope', 'not & known'));
    }

    public function test_the_views_attach_definitions_to_the_words_students_meet(): void
    {
        $wizard = (string) file_get_contents(resource_path('views/student/model-development/wizard.blade.php'));
        foreach (["\$term('target'", "\$term('feature'", "\$term('algorithm'", "\$term('cross_validation'", "\$term('test_rows'"] as $needle) {
            $this->assertStringContainsString($needle, $wizard);
        }

        $model = (string) file_get_contents(resource_path('views/student/model-development/model.blade.php'));
        foreach (["\$term('false_negative'", "\$term('false_positive'", "\$term('confidence'", "\$term('confusion_matrix'", "\$term('feature_importance'"] as $needle) {
            $this->assertStringContainsString($needle, $model);
        }

        $styles = (string) file_get_contents(resource_path('views/student/model-development/partials/styles.blade.php'));
        $this->assertStringContainsString('.ml-term:focus-within .ml-term-tip', $styles, 'Tooltips must open from the keyboard.');
        $this->assertStringContainsString("event.key !== 'Escape'", $styles);
    }
}
