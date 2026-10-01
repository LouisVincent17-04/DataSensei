<?php

namespace Tests\Unit;

use App\Support\Glossary;
use Tests\TestCase;

/**
 * DataSensei Updates 10, tasks 2 and 3: statistical and technical words are
 * explained by short, plain tooltips that open on hover, keyboard focus,
 * and tap.
 */
class GlossaryTest extends TestCase
{
    public function test_the_terms_a_beginner_meets_are_defined_briefly_and_plainly(): void
    {
        $terms = Glossary::terms();
        foreach ([
            'univariate', 'outlier', 'mean', 'median', 'standard_deviation', 'iqr', 'variance', 'correlation',
            'distribution', 'skewness', 'quartile', 'percentile', 'missing_value', 'numeric_column', 'categorical_column',
        ] as $required) {
            $this->assertArrayHasKey($required, $terms);
        }

        foreach ($terms as $key => $term) {
            $words = str_word_count($term['definition']);
            $this->assertGreaterThanOrEqual(4, $words, "{$key} is too short to explain anything.");
            $this->assertLessThanOrEqual(34, $words, "{$key} should stay a one-breath definition.");
            $this->assertDoesNotMatchRegularExpression('/[=∑√]/u', $term['definition'], "{$key}: formulas belong in the formula field.");
        }

        // The exact wording the update asks for.
        $this->assertSame('Looking at one column at a time.', Glossary::definition('univariate'));
        $this->assertSame('A value that is very different from most other values.', Glossary::definition('outlier'));
        $this->assertSame('The average value: add every value, then divide by how many there are.', Glossary::definition('mean'));
        $this->assertSame('The middle value after the numbers are arranged in order.', Glossary::definition('median'));
        $this->assertSame('Shows how spread out the values are from the average.', Glossary::definition('standard_deviation'));
        $this->assertSame('A method the system uses to find values that are unusually far from most of the data.', Glossary::definition('iqr'));
    }

    public function test_standard_deviation_also_shows_its_formula_and_purpose(): void
    {
        $html = (string) Glossary::help('standard_deviation');

        $this->assertStringContainsString('σ = √(Σ(x − μ)² / N)', $html);
        $this->assertStringContainsString('Purpose: Helps show whether the values are close together or spread far apart.', $html);
    }

    public function test_a_term_renders_as_an_accessible_escaped_tooltip_with_unique_ids(): void
    {
        $first = (string) Glossary::help('mean', 'the <avg> value');
        $second = (string) Glossary::help('Median');

        $this->assertStringContainsString('<button type="button" class="ds-term-help"', $first);
        $this->assertStringContainsString('role="tooltip"', $first);
        $this->assertStringContainsString('the &lt;avg&gt; value', $first);
        $this->assertStringContainsString('add every value', $first);

        preg_match('/aria-describedby="([^"]+)"/', $first, $a);
        preg_match('/aria-describedby="([^"]+)"/', $second, $b);
        $this->assertNotSame($a[1], $b[1], 'Each tooltip needs its own id.');
        $this->assertStringContainsString('id="'.$a[1].'"', $first);

        $this->assertSame('not &amp; known', (string) Glossary::help('nope', 'not & known'));
        $this->assertSame('', (string) Glossary::mark('nope'));
    }

    public function test_the_shared_tooltip_works_by_hover_keyboard_and_tap_on_every_page(): void
    {
        $partial = (string) file_get_contents(resource_path('views/partials/glossary-tips.blade.php'));
        $this->assertStringContainsString('.ds-term:hover .ds-term-tip', $partial);
        $this->assertStringContainsString('.ds-term:focus-within .ds-term-tip', $partial, 'Tooltips must open from the keyboard.');
        $this->assertStringContainsString("event.key !== 'Escape'", $partial);
        $this->assertStringContainsString("closest('.ds-term-help')", $partial, 'Tap-to-toggle for touch screens.');

        // Loaded once through the shared page head, so any page can use a term.
        $head = (string) file_get_contents(resource_path('views/partials/page-head.blade.php'));
        $this->assertStringContainsString("@include('partials.glossary-tips')", $head);

        // A tapped "?" keeps focus, and :focus-within would keep the box open,
        // so tapping elsewhere also moves focus off it.
        $this->assertMatchesRegularExpression("/if \\(!help\\) \\{[^}]*closeAll\\(null\\);[^}]*activeElement\\.blur\\(\\)/s", $partial);
    }

    public function test_assessment_and_anti_cheat_words_are_explained(): void
    {
        foreach (['time_limit', 'passing_score', 'provisional_score', 'held_for_review', 'focus_loss', 'dual_monitor', 'question_bank', 'performance_segment'] as $key) {
            $this->assertNotSame('', Glossary::definition($key), "{$key} needs a definition");
        }
        $this->assertStringContainsString('whichever comes first', Glossary::definition('time_limit'), 'The combined time limit and due date rule is explained.');
    }

    public function test_thinking_levels_use_the_table_of_specifications_wording(): void
    {
        foreach (\App\Services\TableOfSpecificationService::COGNITIVE_LEVELS as $slug => $level) {
            $term = Glossary::find('thinking_'.$slug);
            $this->assertNotNull($term, $slug);
            $this->assertSame($level['label'], $term['label']);
            $this->assertSame($level['explanation'], $term['definition']);
        }

        // The TOS pages show the definitions as tooltips on the level names,
        // not as a block of text under the form.
        $show = (string) file_get_contents(resource_path('views/instructor/tos/show.blade.php'));
        $this->assertStringNotContainsString('What do these cognitive levels mean?', $show);
        $this->assertStringContainsString("Glossary::help('thinking_'.\$slug", $show);
        $review = (string) file_get_contents(resource_path('views/instructor/tos/review.blade.php'));
        $this->assertStringNotContainsString("\$definition['explanation']", $review);
    }
}
