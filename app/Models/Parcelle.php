<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Parcelle extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'exploitation_id', 'code', 'lieu', 'surface', 'type_sol', 'source_eau',
        'statut', 'latitude', 'longitude', 'local_uuid', 'sync_status', 'last_synced_at',
    ];

    protected $casts = [
        'surface' => 'decimal:2',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'last_synced_at' => 'datetime',
    ];

    public function exploitation()
    {
        return $this->belongsTo(Exploitation::class);
    }

    public function cultures()
    {
        return $this->hasMany(Culture::class);
    }

    public function activites()
    {
        return $this->hasMany(Activite::class);
    }

    public function testsSol()
    {
        return $this->hasMany(TestSol::class);
    }
}
