<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activite extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'activites';

    protected $fillable = [
        'parcelle_id', 'type', 'description', 'date', 'responsable', 'cout',
        'statut', 'local_uuid', 'sync_status', 'last_synced_at',
    ];

    protected $casts = [
        'date' => 'date',
        'cout' => 'decimal:2',
        'last_synced_at' => 'datetime',
    ];

    public function parcelle()
    {
        return $this->belongsTo(Parcelle::class);
    }
}
