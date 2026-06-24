<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('intrants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('exploitation_id')->constrained('exploitations')->cascadeOnDelete();
            $table->string('nom');
            $table->string('categorie')->nullable();
            $table->string('unite')->default('kg');
            $table->decimal('stock', 12, 2)->default(0);
            $table->decimal('seuil_alerte', 12, 2)->default(0);
            $table->decimal('prix_unitaire', 12, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('intrants');
    }
};
