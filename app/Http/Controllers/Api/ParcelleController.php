<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Parcelle;
use Illuminate\Http\Request;

class ParcelleController extends Controller
{
    private function expIds(Request $r)
    {
        return $r->user()->exploitations()->pluck('id');
    }

    public function index(Request $request)
    {
        return Parcelle::whereIn('exploitation_id', $this->expIds($request))
            ->withCount('cultures')
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'exploitation_id' => ['required', 'uuid'],
            'code' => ['required', 'string', 'max:50'],
            'lieu' => ['nullable', 'string', 'max:150'],
            'surface' => ['nullable', 'numeric', 'min:0'],
            'type_sol' => ['nullable', 'string', 'max:80'],
            'source_eau' => ['nullable', 'string', 'max:80'],
            'statut' => ['nullable', 'string', 'max:40'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'local_uuid' => ['nullable', 'uuid'],
        ]);

        abort_unless($this->expIds($request)->contains($data['exploitation_id']), 403, 'Exploitation non autorisée.');

        return response()->json(Parcelle::create($data), 201);
    }

    public function show(Request $request, string $id)
    {
        $parcelle = Parcelle::findOrFail($id);
        abort_unless($this->expIds($request)->contains($parcelle->exploitation_id), 403);

        return $parcelle->load(['cultures', 'activites', 'testsSol']);
    }

    public function update(Request $request, string $id)
    {
        $parcelle = Parcelle::findOrFail($id);
        abort_unless($this->expIds($request)->contains($parcelle->exploitation_id), 403);

        $data = $request->validate([
            'code' => ['sometimes', 'string', 'max:50'],
            'lieu' => ['nullable', 'string', 'max:150'],
            'surface' => ['nullable', 'numeric', 'min:0'],
            'type_sol' => ['nullable', 'string', 'max:80'],
            'source_eau' => ['nullable', 'string', 'max:80'],
            'statut' => ['nullable', 'string', 'max:40'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
        ]);

        $parcelle->update($data);

        return $parcelle;
    }

    public function destroy(Request $request, string $id)
    {
        $parcelle = Parcelle::findOrFail($id);
        abort_unless($this->expIds($request)->contains($parcelle->exploitation_id), 403);
        $parcelle->delete();

        return response()->json(['message' => 'Parcelle supprimée.']);
    }
}
