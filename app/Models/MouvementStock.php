<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class MouvementStock extends Model
{
    use HasUuids;

    protected $table = 'mouvements_stock';

    protected $fillable = [
        'intrant_id', 'type', 'quantite', 'date', 'motif',
        'local_uuid', 'sync_status', 'last_synced_at',
    ];

    protected $casts = [
        'quantite' => 'decimal:2',
        'date' => 'date',
        'last_synced_at' => 'datetime',
    ];

    public function intrant()
    {
        return $this->belongsTo(Intrant::class);
    }
}
