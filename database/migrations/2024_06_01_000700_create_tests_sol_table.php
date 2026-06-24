<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tests_sol', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('parcelle_id')->constrained('parcelles')->cascadeOnDelete();
            $table->date('date')->nullable();
            $table->decimal('ph', 4, 2)->nullable();
            $table->decimal('humidite', 5, 2)->nullable();
            $table->decimal('salinite', 6, 2)->nullable();
            $table->decimal('conductivite', 6, 2)->nullable();
            $table->decimal('azote', 6, 2)->nullable();
            $table->decimal('phosphore', 6, 2)->nullable();
            $table->decimal('potassium', 6, 2)->nullable();
            $table->decimal('temperature', 5, 2)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('statut')->nullable();
            $table->text('observations')->nullable();
            $table->uuid('local_uuid')->nullable()->index();
            $table->string('sync_status')->default('synced');
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tests_sol');
    }
};
