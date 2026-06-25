<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Intrant;
use App\Models\MouvementStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MouvementStockController extends Controller
{
    private function intrantIds(Request $r)
    {
        $expIds = $r->user()->exploitations()->pluck('id');

        return Intrant::whereIn('exploitation_id', $expIds)->pluck('id');
    }

    public function index(Request $request)
    {
        $ids = $this->intrantIds($request);

        $query = MouvementStock::whereIn('intrant_id', $ids)
            ->with('intrant')
            ->latest('date');

        if ($request->filled('intrant_id')) {
            abort_unless($ids->contains($request->intrant_id), 403);
            $query->where('intrant_id', $request->intrant_id);
        }

        return $query->get();
    }

    public function store(Request $request)
    {
        $ids = $this->intrantIds($request);

        $data = $request->validate([
            'intrant_id' => ['required', 'uuid'],
            'type'       => ['required', 'in:entrée,sortie'],
            'quantite'   => ['required', 'numeric', 'min:0.01'],
            'date'       => ['required', 'date'],
            'motif'      => ['nullable', 'string', 'max:255'],
        ]);

        abort_unless($ids->contains($data['intrant_id']), 403);

        return DB::transaction(function () use ($data) {
            $intrant = Intrant::lockForUpdate()->findOrFail($data['intrant_id']);

            if ($data['type'] === 'sortie') {
                abort_if(
                    $intrant->stock < $data['quantite'],
                    422,
                    'Stock insuffisant. Disponible : ' . $intrant->stock . ' ' . $intrant->unite
                );
                $intrant->decrement('stock', $data['quantite']);
            } else {
                $intrant->increment('stock', $data['quantite']);
            }

            $mouvement = MouvementStock::create($data);

            return response()->json($mouvement->load('intrant'), 201);
        });
    }

    public function show(Request $request, string $id)
    {
        $ids = $this->intrantIds($request);
        $mouvement = MouvementStock::whereIn('intrant_id', $ids)->findOrFail($id);

        return $mouvement->load('intrant');
    }
}
