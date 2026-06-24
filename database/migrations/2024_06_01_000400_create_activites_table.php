<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activites', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('parcelle_id')->constrained('parcelles')->cascadeOnDelete();
            $table->string('type');
            $table->text('description')->nullable();
            $table->date('date')->nullable();
            $table->string('responsable')->nullable();
            $table->decimal('cout', 12, 2)->default(0);
            $table->string('statut')->default('Terminé');
            $table->uuid('local_uuid')->nullable()->index();
            $table->string('sync_status')->default('synced');
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activites');
    }
};
