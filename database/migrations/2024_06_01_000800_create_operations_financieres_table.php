<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operations_financieres', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('exploitation_id')->constrained('exploitations')->cascadeOnDelete();
            $table->string('type'); // depense | recette
            $table->string('categorie')->nullable();
            $table->decimal('montant', 14, 2);
            $table->date('date')->nullable();
            $table->text('description')->nullable();
            $table->uuid('local_uuid')->nullable()->index();
            $table->string('sync_status')->default('synced');
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operations_financieres');
    }
};
