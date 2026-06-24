<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cultures', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('parcelle_id')->constrained('parcelles')->cascadeOnDelete();
            $table->string('nom');
            $table->string('variete')->nullable();
            $table->date('date_semis')->nullable();
            $table->date('date_recolte_prevue')->nullable();
            $table->decimal('rendement_attendu', 8, 2)->nullable();
            $table->string('statut')->default('En cours');
            $table->uuid('local_uuid')->nullable()->index();
            $table->string('sync_status')->default('synced');
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cultures');
    }
};
