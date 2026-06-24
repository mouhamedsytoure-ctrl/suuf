<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Culture extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'parcelle_id', 'nom', 'variete', 'date_semis', 'date_recolte_prevue',
        'rendement_attendu', 'statut', 'local_uuid', 'sync_status', 'last_synced_at',
    ];

    protected $casts = [
        'date_semis' => 'date',
        'date_recolte_prevue' => 'date',
        'rendement_attendu' => 'decimal:2',
        'last_synced_at' => 'datetime',
    ];

    public function parcelle()
    {
        return $this->belongsTo(Parcelle::class);
    }
}
