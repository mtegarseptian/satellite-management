<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon; // 

class Satellite extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'norad_id', 
        'tle_url',
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

    // Ini kode asli Anda yang sangat penting untuk format tanggal peluncuran
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

    // FUNGSI BARU: Mengekstrak TLE Line 1 menjadi format tanggal Epoch
    public function getEpochAttribute()
    {
        if (empty($this->tle_line1) || strlen($this->tle_line1) < 32) {
            return '-';
        }

        try {
            $yearPart = (int) substr($this->tle_line1, 18, 2);
            $dayPart = (float) substr($this->tle_line1, 20, 12);

            $year = ($yearPart < 57) ? 2000 + $yearPart : 1900 + $yearPart;
            
            // Set ke 1 Januari UTC pada tahun tersebut
            $date = Carbon::create($year, 1, 1, 0, 0, 0, 'UTC');
            
            // Hitung total detik dari pecahan hari
            $totalSeconds = ($dayPart - 1) * 86400;
            
            // Tambahkan microsecond agar presisi milidetiknya muncul
            $date->addMicroseconds($totalSeconds * 1000000);

            // Format disamakan dengan tampilan JS: YYYY-MM-DD HH:mm:ss.SSS UTC
            return $date->format('Y-m-d H:i:s.v') . ' UTC';
        } catch (\Exception $e) {
            return 'Invalid TLE';
        }
    }
}