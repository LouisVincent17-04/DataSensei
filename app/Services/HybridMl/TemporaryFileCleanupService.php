<?php

namespace App\Services\HybridMl;

use DateTimeInterface;
use FilesystemIterator;
use Illuminate\Support\Facades\File;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class TemporaryFileCleanupService
{
    public function __construct(
        private readonly ?string $mlTemporaryRoot = null,
        private readonly ?string $systemTemporaryRoot = null,
    ) {
    }

    /** @return array{directories:int,files:int} */
    public function prune(DateTimeInterface $cutoff): array
    {
        $deleted = ['directories' => 0, 'files' => 0];
        $cutoffTimestamp = $cutoff->getTimestamp();
        $mlRoot = $this->mlTemporaryRoot ?: storage_path('app/ml/tmp');
        $systemRoot = $this->systemTemporaryRoot ?: sys_get_temp_dir();

        if (is_dir($mlRoot)) {
            foreach (new FilesystemIterator($mlRoot, FilesystemIterator::SKIP_DOTS) as $entry) {
                if ($entry->isLink() || $this->newestModificationTime($entry->getPathname()) >= $cutoffTimestamp) {
                    continue;
                }

                if ($entry->isDir() && File::deleteDirectory($entry->getPathname())) {
                    $deleted['directories']++;
                } elseif ($entry->isFile() && @unlink($entry->getPathname())) {
                    $deleted['files']++;
                }
            }
        }

        if (is_dir($systemRoot)) {
            foreach (new FilesystemIterator($systemRoot, FilesystemIterator::SKIP_DOTS) as $entry) {
                if ($entry->isLink() || ! $entry->isFile()) {
                    continue;
                }
                if (! str_starts_with($entry->getFilename(), 'datasensei_csv_')) {
                    continue;
                }
                if ($entry->getMTime() < $cutoffTimestamp && @unlink($entry->getPathname())) {
                    $deleted['files']++;
                }
            }
        }

        return $deleted;
    }

    private function newestModificationTime(string $path): int
    {
        $newest = (int) (@filemtime($path) ?: 0);
        if (! is_dir($path)) {
            return $newest;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST,
        );

        /** @var SplFileInfo $entry */
        foreach ($iterator as $entry) {
            if (! $entry->isLink()) {
                $newest = max($newest, $entry->getMTime());
            }
        }

        return $newest;
    }
}
