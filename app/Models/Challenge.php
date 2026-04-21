<?php
// Challenge.php - The main Challenge model representing each challenge in the system.
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Challenge extends Model
{
    protected $fillable = ['challenge_category_id', 'title', 'description', 'time_limit_seconds', 'base_xp', 'order_index', 'is_coding_challenge'];

    public function questions()
    {
        return $this->hasMany(ChallengeQuestion::class);
    }
}