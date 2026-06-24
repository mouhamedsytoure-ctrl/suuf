<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class SyncQueue extends Model
{
    use HasUuids;

    protected $table = 'sync_queue';

    protected $fillable = [
        'user_id', 'entity_type', 'entity_uuid', 'operation', 'payload',
        'status', 'error', 'processed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'processed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
