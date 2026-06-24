<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Parcelle;
use App\Models\TestSol;
use Illuminate\Http\Request;

class TestSolController extends Controller
{
    private function parcIds(Request $r)
    {
        $expIds = $r->user()->exploitations()->pluck('id');
        return Parcelle::whereIn('exploitation_id', $expIds)->pluck('id');
    }

    public function index(Request $request)
    {
        return TestSol::whereIn('parcelle_id', $this->parcIds($request))
            ->with('parcelle:id,code,lieu')
            ->latest('date')
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'parcelle_id' => ['required', 'uuid'],
            'date' => ['nullable', 'date'],
            'ph' => ['nullable', 'numeric'],
            'humidite' => ['nullable', 'numeric'],
            'salinite' => ['nullable', 'numeric'],
            'conductivite' => ['nullable', 'numeric'],
            'azote' => ['nullable', 'numeric'],
            'phosphore' => ['nullable', 'numeric'],
            'potassium' => ['nullable', 'numeric'],
            'temperature' => ['nullable', 'numeric'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'statut' => ['nullable', 'string', 'max:40'],
            'observations' => ['nullable', 'string'],
            'local_uuid' => ['nullable', 'uuid'],
        ]);

        abort_unless($this->parcIds($request)->contains($data['parcelle_id']), 403);

        return response()->json(TestSol::create($data), 201);
    }

    public function show(Request $request, string $id)
    {
        $test = TestSol::findOrFail($id);
        abort_unless($this->parcIds($request)->contains($test->parcelle_id), 403);

        return $test->load('parcelle:id,code,lieu');
    }

    public function update(Request $request, string $id)
    {
        $test = TestSol::findOrFail($id);
        abort_unless($this->parcIds($request)->contains($test->parcelle_id), 403);

        $test->update($request->validate([
            'date' => ['nullable', 'date'],
            'ph' => ['nullable', 'numeric'],
            'humidite' => ['nullable', 'numeric'],
            'salinite' => ['nullable', 'numeric'],
            'conductivite' => ['nullable', 'numeric'],
            'azote' => ['nullable', 'numeric'],
            'phosphore' => ['nullable', 'numeric'],
            'potassium' => ['nullable', 'numeric'],
            'temperature' => ['nullable', 'numeric'],
            'statut' => ['nullable', 'string', 'max:40'],
            'observations' => ['nullable', 'string'],
        ]));

        return $test;
    }

    public function destroy(Request $request, string $id)
    {
        $test = TestSol::findOrFail($id);
        abort_unless($this->parcIds($request)->contains($test->parcelle_id), 403);
        $test->delete();

        return response()->json(['message' => 'Test de sol supprimé.']);
    }
}
