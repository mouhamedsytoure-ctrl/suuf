<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Intrant;
use Illuminate\Http\Request;

class IntrantController extends Controller
{
    private function expIds(Request $r)
    {
        return $r->user()->exploitations()->pluck('id');
    }

    public function index(Request $request)
    {
        return Intrant::whereIn('exploitation_id', $this->expIds($request))
            ->latest()
            ->get()
            ->append('statut');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'exploitation_id' => ['required', 'uuid'],
            'nom' => ['required', 'string', 'max:120'],
            'categorie' => ['nullable', 'string', 'max:80'],
            'unite' => ['nullable', 'string', 'max:20'],
            'stock' => ['nullable', 'numeric', 'min:0'],
            'seuil_alerte' => ['nullable', 'numeric', 'min:0'],
            'prix_unitaire' => ['nullable', 'numeric', 'min:0'],
        ]);

        abort_unless($this->expIds($request)->contains($data['exploitation_id']), 403);

        return response()->json(Intrant::create($data)->append('statut'), 201);
    }

    public function show(Request $request, string $id)
    {
        $intrant = Intrant::findOrFail($id);
        abort_unless($this->expIds($request)->contains($intrant->exploitation_id), 403);

        return $intrant->append('statut')->load('mouvements');
    }

    public function update(Request $request, string $id)
    {
        $intrant = Intrant::findOrFail($id);
        abort_unless($this->expIds($request)->contains($intrant->exploitation_id), 403);

        $intrant->update($request->validate([
            'nom' => ['sometimes', 'string', 'max:120'],
            'categorie' => ['nullable', 'string', 'max:80'],
            'unite' => ['nullable', 'string', 'max:20'],
            'stock' => ['nullable', 'numeric', 'min:0'],
            'seuil_alerte' => ['nullable', 'numeric', 'min:0'],
            'prix_unitaire' => ['nullable', 'numeric', 'min:0'],
        ]));

        return $intrant->append('statut');
    }

    public function destroy(Request $request, string $id)
    {
        $intrant = Intrant::findOrFail($id);
        abort_unless($this->expIds($request)->contains($intrant->exploitation_id), 403);
        $intrant->delete();

        return response()->json(['message' => 'Intrant supprimé.']);
    }
}
