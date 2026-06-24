<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activite;
use App\Models\Culture;
use App\Models\Intrant;
use App\Models\OperationFinanciere;
use App\Models\Parcelle;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $expIds = $request->user()->exploitations()->pluck('id');
        $parcIds = Parcelle::whereIn('exploitation_id', $expIds)->pluck('id');

        $depenses = (float) OperationFinanciere::whereIn('exploitation_id', $expIds)
            ->where('type', 'depense')->sum('montant');
        $recettes = (float) OperationFinanciere::whereIn('exploitation_id', $expIds)
            ->where('type', 'recette')->sum('montant');

        $intrants = Intrant::whereIn('exploitation_id', $expIds)->get();
        $alertesStock = $intrants->filter(fn ($i) => $i->stock <= $i->seuil_alerte)->count();

        return response()->json([
            'surface_totale' => (float) Parcelle::whereIn('exploitation_id', $expIds)->sum('surface'),
            'nb_parcelles' => $parcIds->count(),
            'cultures_actives' => Culture::whereIn('parcelle_id', $parcIds)->where('statut', 'En cours')->count(),
            'depenses_mois' => $depenses,
            'recettes_mois' => $recettes,
            'marge' => $recettes - $depenses,
            'alertes_stock' => $alertesStock,
            'activites_recentes' => Activite::whereIn('parcelle_id', $parcIds)
                ->latest('date')->take(5)->get(),
        ]);
    }
}
