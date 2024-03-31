<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Availability extends Model
{
    use HasFactory;

    protected $table = 'availabilities';
    protected $guarded = [];
    public $timestamps = true;

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'availability_id', 'id');
    }
}
