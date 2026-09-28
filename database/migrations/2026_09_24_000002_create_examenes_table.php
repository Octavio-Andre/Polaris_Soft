<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('examenes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('materia_id')->constrained('materias')->cascadeOnDelete();
            $table->string('carrera')->nullable();
            $table->date('fecha');
            $table->time('hora_inicio');
            $table->unsignedSmallInteger('duracion_min')->default(90);
            $table->string('ambiente', 100);
            $table->unsignedSmallInteger('capacidad')->default(0);
            $table->string('estado', 20)->default('programado'); // programado | abierto | finalizado
            $table->text('normas_admision')->nullable();
            $table->text('normas_salida')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('examenes');
    }
};
