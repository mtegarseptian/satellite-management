<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Satellite extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'norad_id', 
        'country',
        'launch_date',
        'orbit_type',
        'tle_line1',
        'tle_line2',
        'status',
        'description',
        'ground_station_id',
        'image'
    ];

    protected $casts = [
        'launch_date' => 'date',
    ];

    public function groundStation()
    {
        return $this->belongsTo(GroundStation::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeByCountry($query, $country)
    {
        return $query->where('country', $country);
    }

    public function scopeByOrbit($query, $orbit)
    {
        return $query->where('orbit_type', $orbit);
    }
}