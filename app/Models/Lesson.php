<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Lesson extends Model
{
    protected $fillable = ['module_id', 'title', 'content', 'order_index', 'blocks'];

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'lesson_user')->withTimestamps();
    }

    /**
     * The block editor's view of this lesson.
     *
     * A lesson written before the editor existed has no blocks column value:
     * its hand-written HTML becomes one "html" block, byte for byte, so it
     * renders exactly as before and can be edited or built around.
     *
     * @return list<array<string, mixed>>
     */
    public function editorBlocks(): array
    {
        $raw = $this->blocks;

        if (is_string($raw) && $raw !== '') {
            $decoded = json_decode($raw, true);

            if (is_array($decoded)) {
                return array_values(array_filter($decoded, 'is_array'));
            }
        }

        if ((string) $this->content === '') {
            return [];
        }

        return [['type' => 'html', 'html' => (string) $this->content]];
    }

    /** True when this lesson still carries its original hand-written HTML only. */
    public function usesLegacyHtml(): bool
    {
        return ! is_string($this->blocks) || $this->blocks === '';
    }
}
