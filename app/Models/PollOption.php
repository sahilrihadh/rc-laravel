<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'poll_id', 'option_text', 'vote_count'
    ];

    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }

    public function votes()
    {
        return $this->hasMany(PollVote::class);
    }

    // Increment vote count
    public function incrementVotes()
    {
        $this->increment('vote_count');
    }
}