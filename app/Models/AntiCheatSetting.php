<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AntiCheatSetting extends Model
{
    protected $fillable = [
        'instructor_id',
        'class_id',
        'assessment_type',
        'enabled',
        'allow_tab_switch',
        'max_tab_switches',
        'block_on_tab_limit',
        'require_fullscreen',
        'detect_dual_monitor',
        'block_dual_monitor',
        'allow_copy',
        'allow_paste',
        'block_external_paste',
        'allow_right_click',
        'allow_devtools_shortcuts',
        'show_warnings',
        'auto_submit_mcq_on_violation',
        'lock_screen_on_violation',
    ];

    protected $casts = [
        'enabled'                      => 'boolean',
        'allow_tab_switch'             => 'boolean',
        'block_on_tab_limit'           => 'boolean',
        'require_fullscreen'           => 'boolean',
        'detect_dual_monitor'          => 'boolean',
        'block_dual_monitor'           => 'boolean',
        'allow_copy'                   => 'boolean',
        'allow_paste'                  => 'boolean',
        'block_external_paste'         => 'boolean',
        'allow_right_click'            => 'boolean',
        'allow_devtools_shortcuts'     => 'boolean',
        'show_warnings'                => 'boolean',
        'auto_submit_mcq_on_violation' => 'boolean',
        'lock_screen_on_violation'     => 'boolean',
        'max_tab_switches'             => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (AntiCheatSetting $setting) {
            // Instructor anti-cheat settings are intentionally scoped to instructor assignments only.
            $setting->assessment_type = 'assignment';
        });
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }
}
