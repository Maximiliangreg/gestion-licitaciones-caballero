<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique(); // RN-08: El correo debe ser único [cite: 84]
        $table->timestamp('email_verified_at')->nullable();
        $table->string('password');


        // 1. Rol obligatorio del MVP (Sección 3.4) 
        $table->enum('role', ['admin', 'user'])->default('user');

        // 2. Auditoría obligatoria (Sección 3.3) 
        // Se definen como nullables porque el admin inicial se creará mediante un Seeder
        // cuando no haya nadie logueado en el sistema aún.
        $table->unsignedBigInteger('created_by')->nullable();
        $table->unsignedBigInteger('updated_by')->nullable();


        $table->rememberToken();
        $table->timestamps(); // Genera automáticamente created_at y updated_at [cite: 43]

        // Relaciones de llaves foráneas para auditar quién creó o modificó al usuario [cite: 41, 42]
        $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
    });

    Schema::create('password_reset_tokens', function (Blueprint $table) {
        $table->string('email')->primary();
        $table->string('token');
        $table->timestamp('created_at')->nullable();
    });

    Schema::create('sessions', function (Blueprint $table) {
        $table->string('id')->primary();
        $table->foreignId('user_id')->nullable()->index();
        $table->string('ip_address', 45)->nullable();
        $table->text('user_agent')->nullable();
        $table->longText('payload');
        $table->integer('last_activity')->index();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
