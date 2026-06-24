<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Culture;
use App\Models\Parcelle;
use Illuminate\Http\Request;

class CultureController extends Controller
{
    private function parcIds(Request $r)
    {
        $expIds = $r->user()->exploitations()->pluck('id');
        return Parcelle::whereIn('exploitation_id', $expIds)->pluck('id');
    }

    public function index(Request $request)
    {
        return Culture::whereIn('parcelle_id', $this->parcIds($request))
            ->with('parcelle:id,code,lieu')
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'parcelle_id' => ['required', 'uuid'],
            'nom' => ['required', 'string', 'max:100'],
            'variete' => ['nullable', 'string', 'max:100'],
            'date_semis' => ['nullable', 'date'],
            'date_recolte_prevue' => ['nullable', 'date'],
            'rendement_attendu' => ['nullable', 'numeric', 'min:0'],
            'statut' => ['nullable', 'string', 'max:40'],
            'local_uuid' => ['nullable', 'uuid'],
        ]);

        abort_unless($this->parcIds($request)->contains($data['parcelle_id']), 403);

        return response()->json(Culture::create($data), 201);
    }

    public function show(Request $request, string $id)
    {
        $culture = Culture::findOrFail($id);
        abort_unless($this->parcIds($request)->contains($culture->parcelle_id), 403);

        return $culture->load('parcelle:id,code,lieu');
    }

    public function update(Request $request, string $id)
    {
        $culture = Culture::findOrFail($id);
        abort_unless($this->parcIds($request)->contains($culture->parcelle_id), 403);

        $culture->update($request->validate([
            'nom' => ['sometimes', 'string', 'max:100'],
            'variete' => ['nullable', 'string', 'max:100'],
            'date_semis' => ['nullable', 'date'],
            'date_recolte_prevue' => ['nullable', 'date'],
            'rendement_attendu' => ['nullable', 'numeric', 'min:0'],
            'statut' => ['nullable', 'string', 'max:40'],
        ]));

        return $culture;
    }

    public function destroy(Request $request, string $id)
    {
        $culture = Culture::findOrFail($id);
        abort_unless($this->parcIds($request)->contains($culture->parcelle_id), 403);
        $culture->delete();

        return response()->json(['message' => 'Culture supprimée.']);
    }
}
