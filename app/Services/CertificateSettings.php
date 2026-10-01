<?php

namespace App\Services;

use App\Support\Certificates\CertificateLayouts;
use App\Support\SchemaInspector;
use Illuminate\Support\Facades\DB;

/**
 * Admin settings for certificates (DataSensei Updates 13): the issuer
 * shown on the system certificates (and on class certificates of
 * instructors without an institution), the global signatory, the layout of
 * the system certificates, and which of the five predefined layouts are
 * turned on.
 *
 * Settings apply to certificates issued from now on. A certificate already
 * issued keeps the values in its snapshot.
 */
class CertificateSettings
{
    public const DEFAULTS = [
        'issuer_name' => 'DataSensei',
        'issuer_line' => 'Data Science Learning Platform',
        'signatory_name' => 'DataSensei',
        'signatory_title' => 'Learning Platform',
        'system_layout' => CertificateLayouts::ACADEMIC_CLASSIC,
        // The official requirement set of the two core challenge
        // certificates: the challenge level used (agreed: Newbie), and
        // whether the coding certificate needs the coding challenges of
        // every level instead.
        'core_challenge_level' => 'newbie',
        'core_coding_all_levels' => '0',
        // Branding: the issuer's logo on the core certificates (and on class
        // certificates of instructors who belong to no institution), a small
        // JPEG as base64; empty for none (the layouts then show initials).
        'issuer_logo' => '',
    ];

    public const LIMITS = [
        'issuer_name' => 120,
        'issuer_line' => 160,
        'signatory_name' => 120,
        'signatory_title' => 120,
    ];

    /** @var array<string, string>|null */
    private ?array $values = null;

    /** @var array<string, bool>|null */
    private ?array $layouts = null;

    /** The request the cached values were read for. */
    private ?object $readFor = null;

    public function get(string $key): string
    {
        $values = $this->all();

        return (string) ($values[$key] ?? self::DEFAULTS[$key] ?? '');
    }

    /** @return array<string, string> */
    public function all(): array
    {
        $this->forgetOnNewRequest();
        if ($this->values !== null) {
            return $this->values;
        }

        $stored = SchemaInspector::hasTable('certificate_settings')
            ? DB::table('certificate_settings')->pluck('setting_value', 'setting_key')->all()
            : [];

        $values = self::DEFAULTS;
        foreach (self::DEFAULTS as $key => $default) {
            $value = trim((string) ($stored[$key] ?? ''));
            if ($value !== '') {
                $values[$key] = $value;
            }
        }
        $values['core_coding_all_levels'] = in_array($values['core_coding_all_levels'], ['1', 'true', 'on'], true) ? '1' : '0';
        if (! CertificateLayouts::exists($values['system_layout'])) {
            $values['system_layout'] = self::DEFAULTS['system_layout'];
        }

        return $this->values = $values;
    }

    /** The issuer logo (base64 JPEG), or null when none was uploaded. */
    public function logo(): ?string
    {
        $logo = $this->get('issuer_logo');

        return $logo !== '' ? $logo : null;
    }

    /** @param array<string, string> $values */
    public function save(array $values, ?int $userId): void
    {
        foreach ($values as $key => $value) {
            if (! array_key_exists($key, self::DEFAULTS)) {
                continue;
            }

            $exists = DB::table('certificate_settings')->where('setting_key', $key)->exists();
            $row = ['setting_value' => trim((string) $value), 'updated_by' => $userId, 'updated_at' => now()];
            $exists
                ? DB::table('certificate_settings')->where('setting_key', $key)->update($row)
                : DB::table('certificate_settings')->insert($row + ['setting_key' => $key, 'created_at' => now()]);
        }

        $this->values = null;
    }

    /** @return array<string, bool> layout key => enabled, in the predefined order */
    public function layouts(): array
    {
        $this->forgetOnNewRequest();
        if ($this->layouts !== null) {
            return $this->layouts;
        }

        $stored = SchemaInspector::hasTable('certificate_layouts')
            ? DB::table('certificate_layouts')->pluck('is_enabled', 'layout_key')->all()
            : [];

        $layouts = [];
        foreach (array_keys(CertificateLayouts::LAYOUTS) as $key) {
            $layouts[$key] = array_key_exists($key, $stored) ? (bool) $stored[$key] : true;
        }

        return $this->layouts = $layouts;
    }

    public function layoutEnabled(?string $key): bool
    {
        return $key !== null && ($this->layouts()[$key] ?? false);
    }

    /** @return list<string> */
    public function enabledLayouts(): array
    {
        return array_keys(array_filter($this->layouts()));
    }

    public function setLayoutEnabled(string $key, bool $enabled): void
    {
        DB::table('certificate_layouts')->updateOrInsert(
            ['layout_key' => $key],
            ['is_enabled' => $enabled, 'updated_at' => now()]
        );
        $this->layouts = null;
    }

    /**
     * Values are read once per request. A long-lived instance (a controller
     * kept between requests, a queue worker) reads them again for the next
     * request, so a change an administrator saved is seen at once.
     */
    private function forgetOnNewRequest(): void
    {
        $request = app()->bound('request') ? app('request') : null;
        if ($request !== $this->readFor) {
            $this->values = null;
            $this->layouts = null;
            $this->readFor = $request;
        }
    }
}
