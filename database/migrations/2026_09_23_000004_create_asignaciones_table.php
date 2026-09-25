<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asignaciones', function (Blueprint $table) {
            $table->id();
            // OJO: ajusta 'estudiante' al nombre real de tu tabla de estudiantes
            // (en este proyecto la migración de RQ1 la creó como 'estudiante', singular)
            //$table->foreignId('estudiante_id')->constrained('estudiante')->cascadeOnDelete();
            $table->foreignId('estudiante_id'); 
      	    $table->foreignId('materia_id')->constrained('materias')->cascadeOnDelete();
            $table->foreignId('grupo_id')->constrained('grupos')->cascadeOnDelete();
            $table->foreignId('docente_id')->nullable()->constrained('docentes')->nullOnDelete();
            $table->timestamps();

            // Un estudiante no puede estar asignado dos veces a la misma materia
            $table->unique(['estudiante_id', 'materia_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asignaciones');
    }
};
