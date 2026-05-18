<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroundStation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'country',
        'latitude',
        'longitude',
        'altitude',
        'description'
    ];

    // Relasi: 1 Ground Station has many Satellites
    public function satellites()
    {
        return $this->hasMany(Satellite::class);
    }
}