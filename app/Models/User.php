<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    
    protected $fillable = [
        'full_name',
        'mobile_number',
        'email_id',
        'clinic_name',
        'registration_number',
        'city',
        'state',
        'city_pincode',
        'registered_at',
        'last_seen_at',
        'is_online',
        'is_admin'
    ];

    protected $hidden = [
        'remember_token',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'is_online' => 'boolean',
        'is_admin' => 'boolean',
    ];

    // Authentication using email_id
    public function getAuthIdentifierName()
    {
        return 'email_id';
    }

    public function getAuthPassword()
    {
        return null;
    }

    // Relationships
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function pollVotes()
    {
        return $this->hasMany(PollVote::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function userSessions()
    {
        return $this->hasMany(UserSession::class);
    }
}