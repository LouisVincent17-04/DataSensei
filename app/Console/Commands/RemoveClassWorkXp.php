<?php

namespace App\Console\Commands;

use App\Services\ClassWorkXpRemover;
use Illuminate\Console\Command;

/**
 * Takes back the XP students earned on class work (instructor-built
 * challenges and the University Student level) before DataSensei Updates 5.
 * Run it once after updating; running it again removes nothing more.
 */
class RemoveClassWorkXp extends Command
{
    protected $signature = 'xp:remove-class-work
        {--dry-run : Only show what would be removed}';

    protected $description = 'Remove the XP students earned on class work (instructor challenges and the University Student level)';

    public function handle(ClassWorkXpRemover $remover): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $summary = $remover->run($dryRun);

        if ($summary['users'] === 0) {
            $this->info('No class-work XP to remove.');

            return self::SUCCESS;
        }

        foreach ($summary['by_user'] as $row) {
            $this->line('  '.$row['email'].': -'.number_format($row['xp']).' XP');
        }

        $this->newLine();
        $verb = $dryRun ? 'Would remove' : 'Removed';
        $this->info("{$verb} ".number_format($summary['xp'])." XP from {$summary['users']} student(s) ({$summary['attempts']} challenge attempt(s), {$summary['submissions']} coding submission(s)).");

        if ($dryRun) {
            $this->line('Nothing was changed. Run it again without --dry-run to remove it.');
        }

        return self::SUCCESS;
    }
}
