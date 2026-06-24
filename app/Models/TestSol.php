<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestSol extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'tests_sol';

    protected $fillable = [
        'parcelle_id', 'date', 'ph', 'humidite', 'salinite', 'conductivite',
        'azote', 'phosphore', 'potassium', 'temperature', 'latitude', 'longitude',
        'statut', 'observations', 'local_uuid', 'sync_status', 'last_synced_at',
    ];

    protected $casts = [
        'date' => 'date',
        'ph' => 'decimal:2',
        'humidite' => 'decimal:2',
        'salinite' => 'decimal:2',
        'conductivite' => 'decimal:2',
        'azote' => 'decimal:2',
        'phosphore' => 'decimal:2',
        'potassium' => 'decimal:2',
        'temperature' => 'decimal:2',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'last_synced_at' => 'datetime',
    ];

    public function parcelle()
    {
        return $this->belongsTo(Parcelle::class);
    }
}
