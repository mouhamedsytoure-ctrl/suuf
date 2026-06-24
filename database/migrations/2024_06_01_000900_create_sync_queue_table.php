<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // File d'attente de synchronisation (offline_sync_queue du cahier des charges)
        Schema::create('sync_queue', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('entity_type');   // ex: parcelle, culture, test_sol
            $table->uuid('entity_uuid');      // local_uuid généré sur le mobile
            $table->string('operation');      // create | update | delete
            $table->json('payload')->nullable();
            $table->string('status')->default('pending'); // pending | done | failed
            $table->text('error')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_queue');
    }
};
