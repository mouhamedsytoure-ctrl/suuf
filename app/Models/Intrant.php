<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Intrant extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'exploitation_id', 'nom', 'categorie', 'unite', 'stock', 'seuil_alerte', 'prix_unitaire',
    ];

    protected $casts = [
        'stock' => 'decimal:2',
        'seuil_alerte' => 'decimal:2',
        'prix_unitaire' => 'decimal:2',
    ];

    public function exploitation()
    {
        return $this->belongsTo(Exploitation::class);
    }

    public function mouvements()
    {
        return $this->hasMany(MouvementStock::class);
    }

    // Statut calculé selon le stock vs seuil
    public function getStatutAttribute(): string
    {
        if ($this->stock <= 0) {
            return 'Rupture';
        }
        if ($this->stock <= $this->seuil_alerte) {
            return 'Faible';
        }
        return 'OK';
    }
}
