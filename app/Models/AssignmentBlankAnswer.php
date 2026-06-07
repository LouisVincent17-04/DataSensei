<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignmentBlankAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_question_id',
        'answer_text',
        'is_case_sensitive',
    ];

    protected $casts = [
        'is_case_sensitive' => 'boolean',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(AssignmentQuestion::class, 'assignment_question_id');
    }
}
