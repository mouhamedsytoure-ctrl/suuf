<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parcelles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('exploitation_id')->constrained('exploitations')->cascadeOnDelete();
            $table->string('code');
            $table->string('lieu')->nullable();
            $table->decimal('surface', 8, 2)->default(0);
            $table->string('type_sol')->nullable();
            $table->string('source_eau')->nullable();
            $table->string('statut')->default('Active');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            // --- Colonnes de synchronisation (offline-first) ---
            $table->uuid('local_uuid')->nullable()->index();
            $table->string('sync_status')->default('synced');
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parcelles');
    }
};
