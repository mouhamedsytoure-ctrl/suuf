<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OperationFinanciere extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'operations_financieres';

    protected $fillable = [
        'exploitation_id', 'type', 'categorie', 'montant', 'date', 'description',
        'local_uuid', 'sync_status', 'last_synced_at',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date' => 'date',
        'last_synced_at' => 'datetime',
    ];

    public function exploitation()
    {
        return $this->belongsTo(Exploitation::class);
    }
}
