<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';
    protected $guarded = [];
    public $timestamps = true;
    protected $hidden = ['password', 'remember_token', 'ip', 'user_agent'];
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    protected $appends = ['overall_rating'];

    public function image(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class, 'user_id', 'id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'user_id', 'id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(
            Feedback::class,
            'reviewee_id',
            'id'
        )->orderByDesc('id');
    }

    public function getOverallRatingAttribute(): int
    {
        return $this->reviews()->average('rating') ?? 0;
    }
}
