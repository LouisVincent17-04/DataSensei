<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ChallengeOption extends Model
{
    protected $fillable = ['challenge_question_id', 'option_text', 'is_correct', 'challenge_category_id'];
}