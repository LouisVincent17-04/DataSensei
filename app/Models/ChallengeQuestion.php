<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ChallengeQuestion extends Model
{
    protected $fillable = ['challenge_id', 'question_text', 'challenge_category_id'];

    public function options()
    {
        return $this->hasMany(ChallengeOption::class);
    }
}