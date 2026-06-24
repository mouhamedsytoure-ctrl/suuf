<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activite;
use App\Models\Parcelle;
use Illuminate\Http\Request;

class ActiviteController extends Controller
{
    private function parcIds(Request $r)
    {
        $expIds = $r->user()->exploitations()->pluck('id');
        return Parcelle::whereIn('exploitation_id', $expIds)->pluck('id');
    }

    public function index(Request $request)
    {
        return Activite::whereIn('parcelle_id', $this->parcIds($request))
            ->with('parcelle:id,code,lieu')
            ->latest('date')
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'parcelle_id' => ['required', 'uuid'],
            'type' => ['required', 'string', 'max:80'],
            'description' => ['nullable', 'string'],
            'date' => ['nullable', 'date'],
            'responsable' => ['nullable', 'string', 'max:120'],
            'cout' => ['nullable', 'numeric', 'min:0'],
            'statut' => ['nullable', 'string', 'max:40'],
            'local_uuid' => ['nullable', 'uuid'],
        ]);

        abort_unless($this->parcIds($request)->contains($data['parcelle_id']), 403);

        return response()->json(Activite::create($data), 201);
    }

    public function show(Request $request, string $id)
    {
        $activite = Activite::findOrFail($id);
        abort_unless($this->parcIds($request)->contains($activite->parcelle_id), 403);

        return $activite->load('parcelle:id,code,lieu');
    }

    public function update(Request $request, string $id)
    {
        $activite = Activite::findOrFail($id);
        abort_unless($this->parcIds($request)->contains($activite->parcelle_id), 403);

        $activite->update($request->validate([
            'type' => ['sometimes', 'string', 'max:80'],
            'description' => ['nullable', 'string'],
            'date' => ['nullable', 'date'],
            'responsable' => ['nullable', 'string', 'max:120'],
            'cout' => ['nullable', 'numeric', 'min:0'],
            'statut' => ['nullable', 'string', 'max:40'],
        ]));

        return $activite;
    }

    public function destroy(Request $request, string $id)
    {
        $activite = Activite::findOrFail($id);
        abort_unless($this->parcIds($request)->contains($activite->parcelle_id), 403);
        $activite->delete();

        return response()->json(['message' => 'Activité supprimée.']);
    }
}
