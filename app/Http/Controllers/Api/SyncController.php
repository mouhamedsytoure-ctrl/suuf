<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activite;
use App\Models\Culture;
use App\Models\OperationFinanciere;
use App\Models\Parcelle;
use App\Models\SyncQueue;
use App\Models\TestSol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SyncController extends Controller
{
    // Modèles synchronisables : type envoyé par le mobile => classe
    private array $map = [
        'parcelle' => Parcelle::class,
        'culture' => Culture::class,
        'activite' => Activite::class,
        'test_sol' => TestSol::class,
        'operation_financiere' => OperationFinanciere::class,
    ];

    /**
     * PUSH — le mobile envoie ses opérations créées hors ligne.
     * Body: { "changes": [ {entity_type, operation, local_uuid, data:{...}}, ... ] }
     */
    public function push(Request $request)
    {
        $request->validate([
            'changes' => ['required', 'array'],
            'changes.*.entity_type' => ['required', 'string'],
            'changes.*.operation' => ['required', 'in:create,update,delete'],
            'changes.*.local_uuid' => ['required', 'uuid'],
        ]);

        $results = [];

        foreach ($request->input('changes') as $change) {
            $type = $change['entity_type'];
            $modelClass = $this->map[$type] ?? null;

            $queue = SyncQueue::create([
                'user_id' => $request->user()->id,
                'entity_type' => $type,
                'entity_uuid' => $change['local_uuid'],
                'operation' => $change['operation'],
                'payload' => $change['data'] ?? [],
                'status' => 'pending',
            ]);

            if (! $modelClass) {
                $queue->update(['status' => 'failed', 'error' => 'Type inconnu', 'processed_at' => now()]);
                $results[] = ['local_uuid' => $change['local_uuid'], 'status' => 'failed'];
                continue;
            }

            try {
                DB::transaction(function () use ($modelClass, $change) {
                    $data = $change['data'] ?? [];
                    $data['local_uuid'] = $change['local_uuid'];
                    $data['sync_status'] = 'synced';
                    $data['last_synced_at'] = now();

                    if ($change['operation'] === 'delete') {
                        $modelClass::where('local_uuid', $change['local_uuid'])->delete();
                        return;
                    }

                    // create OR update : on retrouve par local_uuid (anti-doublon)
                    $modelClass::updateOrCreate(
                        ['local_uuid' => $change['local_uuid']],
                        $data
                    );
                });

                $queue->update(['status' => 'done', 'processed_at' => now()]);
                $results[] = ['local_uuid' => $change['local_uuid'], 'status' => 'done'];
            } catch (\Throwable $e) {
                $queue->update(['status' => 'failed', 'error' => $e->getMessage(), 'processed_at' => now()]);
                $results[] = ['local_uuid' => $change['local_uuid'], 'status' => 'failed', 'error' => $e->getMessage()];
            }
        }

        return response()->json([
            'synced_at' => now()->toIso8601String(),
            'results' => $results,
        ]);
    }

    /**
     * PULL — le mobile récupère ce qui a changé depuis sa dernière synchro.
     * Query: ?since=2024-06-01T00:00:00Z
     */
    public function pull(Request $request)
    {
        $since = $request->query('since');
        $expIds = $request->user()->exploitations()->pluck('id');
        $parcIds = Parcelle::whereIn('exploitation_id', $expIds)->pluck('id');

        $filter = function ($query) use ($since) {
            if ($since) {
                $query->where('updated_at', '>', $since);
            }
            return $query;
        };

        return response()->json([
            'server_time' => now()->toIso8601String(),
            'parcelles' => $filter(Parcelle::whereIn('exploitation_id', $expIds))->get(),
            'cultures' => $filter(Culture::whereIn('parcelle_id', $parcIds))->get(),
            'activites' => $filter(Activite::whereIn('parcelle_id', $parcIds))->get(),
            'tests_sol' => $filter(TestSol::whereIn('parcelle_id', $parcIds))->get(),
            'operations' => $filter(OperationFinanciere::whereIn('exploitation_id', $expIds))->get(),
        ]);
    }
}
