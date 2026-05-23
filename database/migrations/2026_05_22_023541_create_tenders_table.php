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
    Schema::create('tenders', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->text('description')->nullable();
        $table->foreignId('client_id')->constrained('clients'); // RN-06 [cite: 84]
        $table->decimal('max_budget', 12, 2); // RN-02 [cite: 84]
        $table->decimal('total_amount', 12, 2)->default(0.00); // RN-04 [cite: 84]
        $table->enum('status', ['activa', 'por_cobrar', 'perdida', 'finalizada'])->default('activa'); // STA-03 [cite: 109]
        
        // Mejora a criterio: Fecha de entrega para alertas visuales en el Front
        $table->dateTime('delivery_deadline')->nullable(); 
        
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
        Schema::dropIfExists('tenders');
    }
};
