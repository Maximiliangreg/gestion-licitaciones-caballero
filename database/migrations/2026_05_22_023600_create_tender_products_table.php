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
    Schema::create('tender_products', function (Blueprint $table) {
        $table->id();
        $table->foreignId('tender_id')->constrained('tenders')->onDelete('cascade');
        $table->foreignId('product_id')->constrained('products'); // RN-07 [cite: 84]
        $table->integer('quantity');
        $table->decimal('unit_price', 12, 2);
        
        // Mejora a criterio: Trazabilidad completa de quién añade o altera productos en el MVP
        $table->unsignedBigInteger('added_by'); 
        $table->unsignedBigInteger('updated_by')->nullable(); 
        $table->timestamps();

        // RN-09: Restricción UNIQUE compuesta obligatoria [cite: 84]
        $table->unique(['tender_id', 'product_id']);
        
        $table->foreign('added_by')->references('id')->on('users');
        $table->foreign('updated_by')->references('id')->on('users');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tender_products');
    }
};
