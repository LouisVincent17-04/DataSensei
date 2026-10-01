<?php

namespace App\Models;

use App\Support\FinalExamAccess;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
     * The lesson HTML. A module's Final Exam is open to everyone (DataSensei
     * Updates 7): a lesson still stored with the old "University /
     * Organization Access Only" lock is read without it, even before the
     * 2026_09_28_000003 migration has rewritten it.
     */
    protected function content(): Attribute
    {
        return Attribute::make(get: fn (?string $value) => FinalExamAccess::open($value));
    }

    /** The block editor's copy of the lesson (JSON), read without the old lock. */
    protected function blocks(): Attribute
    {
        return Attribute::make(get: function (?string $value): ?string {
            if (! FinalExamAccess::isRestricted($value)) {
                return $value;
            }

            $blocks = json_decode((string) $value, true);
            if (! is_array($blocks)) {
                return $value;
            }

            foreach ($blocks as $index => $block) {
                if (is_array($block) && isset($block['html']) && is_string($block['html'])) {
                    $blocks[$index]['html'] = FinalExamAccess::open($block['html']);
                }
            }

            return json_encode($blocks, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        });
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
