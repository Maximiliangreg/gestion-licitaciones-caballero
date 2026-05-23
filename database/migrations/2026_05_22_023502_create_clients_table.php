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
    Schema::create('clients', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->nullable();
        $table->string('phone')->nullable();
        $table->unsignedBigInteger('created_by'); // Auditoría obligatoria [cite: 41]
        $table->unsignedBigInteger('updated_by')->nullable(); // Auditoría obligatoria [cite: 42]
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
        Schema::dropIfExists('clients');
    }
};
