<?php

namespace Tests\Feature\Regression\Concerns;

use App\Services\CodingChallengeTestRunner;
use App\Services\ReferenceSolutionVerifier;

/**
 * For tests about storing, versioning and publishing coding challenges: the
 * reference-solution check that runs on save (DataSensei Updates 5) is
 * stubbed to pass, so they do not need a Python sandbox. The check itself is
 * covered by Updates5ReferenceSolutionCheckTest.
 */
trait PassesReferenceSolutionCheck
{
    protected function passReferenceSolutionCheck(): void
    {
        $this->app->instance(ReferenceSolutionVerifier::class, new class(new CodingChallengeTestRunner(fn (): array => [])) extends ReferenceSolutionVerifier {
            public function failures(array $questions): array
            {
                return [];
            }
        });
    }
}
