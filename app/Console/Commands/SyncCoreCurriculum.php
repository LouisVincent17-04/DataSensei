<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\CertificateService;
use App\Support\CoreCurriculum;
use Illuminate\Console\Command;

/**
 * Marks the 24 Core Modules and their built-in challenges, keeps the three
 * core certificates and their requirement versions up to date, and (with
 * --award) issues the certificates learners have already earned from work
 * saved before (DataSensei Updates 12). Safe to run again at any time: no
 * key is changed once given and no certificate is issued twice.
 *
 * Run it after re-seeding the curriculum or the challenge seeders.
 */
class SyncCoreCurriculum extends Command
{
    protected $signature = 'datasensei:core-sync
        {--award : Also issue the core certificates every learner already qualifies for}
        {--email= : With --award, only this learner (e-mail address)}';

    protected $description = 'Mark the 24 Core Modules and their challenges, and check the core certificates';

    public function handle(CertificateService $certificates): int
    {
        $marked = CoreCurriculum::sync();
        $this->info("Core modules marked: {$marked['modules']}; core challenge versions marked: {$marked['challenges']}.");

        $definitions = $certificates->ensureDefinitions();
        if ($definitions->isEmpty()) {
            $this->warn('The certificate tables are missing. Run: php artisan migrate');

            return self::SUCCESS;
        }

        foreach ($definitions as $key => $definition) {
            $set = $certificates->currentRequirementSet($definition);
            $this->line("  {$definition->name}: requirement version {$set->version}, {$set->item_count} items");
        }

        if (! $this->option('award')) {
            return self::SUCCESS;
        }

        $issued = 0;
        $checked = 0;
        User::query()
            ->where('role', User::ROLE_USER)
            ->when($this->option('email'), fn ($q, $email) => $q->where('email', $email))
            ->orderBy('id')
            ->chunkById(200, function ($users) use ($certificates, &$issued, &$checked): void {
                foreach ($users as $user) {
                    $checked++;
                    $new = $certificates->syncUser($user);
                    foreach ($new as $certificate) {
                        $this->line("  {$user->email}: {$certificate->certificate_number}");
                    }
                    $issued += count($new);
                }
            });

        $this->info("Learners checked: {$checked}; certificates issued: {$issued}.");

        return self::SUCCESS;
    }
}
