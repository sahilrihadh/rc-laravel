<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebinarSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'video_url', 'session_date', 'is_active', 'order_position'
    ];

    protected $casts = [
        'session_date' => 'datetime',
        'is_active' => 'boolean'
    ];

    public function polls()
    {
        return $this->hasMany(Poll::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function userSessions()
    {
        return $this->hasMany(UserSession::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    // Get active poll for this webinar
    public function activePoll()
    {
        return $this->hasOne(Poll::class)->where('is_active', true)->where(function($q) {
            $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
        });
    }
}