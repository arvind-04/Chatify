<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnergyData extends Model
{
    use HasFactory;

    protected $fillable = [
        'latitude',
        'longitude',
        'location_name',
        'temperature',
        'radiation',
        'energy_potential',
        'recorded_at'
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
    ];
}