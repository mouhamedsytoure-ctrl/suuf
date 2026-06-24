<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OperationFinanciere;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    private function expIds(Request $r)
    {
        return $r->user()->exploitations()->pluck('id');
    }

    public function index(Request $request)
    {
        $expIds = $this->expIds($request);

        $operations = OperationFinanciere::whereIn('exploitation_id', $expIds)
            ->latest('date')
            ->get();

        return response()->json([
            'recettes' => (float) $operations->where('type', 'recette')->sum('montant'),
            'depenses' => (float) $operations->where('type', 'depense')->sum('montant'),
            'operations' => $operations,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'exploitation_id' => ['required', 'uuid'],
            'type' => ['required', 'in:depense,recette'],
            'categorie' => ['nullable', 'string', 'max:80'],
            'montant' => ['required', 'numeric', 'min:0'],
            'date' => ['nullable', 'date'],
            'description' => ['nullable', 'string'],
            'local_uuid' => ['nullable', 'uuid'],
        ]);

        abort_unless($this->expIds($request)->contains($data['exploitation_id']), 403);

        return response()->json(OperationFinanciere::create($data), 201);
    }

    public function destroy(Request $request, string $id)
    {
        $op = OperationFinanciere::findOrFail($id);
        abort_unless($this->expIds($request)->contains($op->exploitation_id), 403);
        $op->delete();

        return response()->json(['message' => 'Opération supprimée.']);
    }
}
