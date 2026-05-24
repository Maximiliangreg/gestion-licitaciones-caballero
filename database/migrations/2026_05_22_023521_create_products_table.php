<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->string('sku')->unique();
        $table->string('name');
        $table->decimal('unit_price', 12, 2);
        $table->integer('stock')->default(0); // Mejora a criterio: Control de inventario operativo
        $table->unsignedBigInteger('created_by'); // Auditoría [cite: 41]
        $table->unsignedBigInteger('updated_by')->nullable(); // Auditoría [cite: 42]
        $table->timestamps();

        $table->foreign('created_by')->references('id')->on('users');
        $table->foreign('updated_by')->references('id')->on('users');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
