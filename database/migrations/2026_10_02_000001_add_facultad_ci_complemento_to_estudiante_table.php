<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('estudiante', function (Blueprint $table) {
            if (! Schema::hasColumn('estudiante', 'facultad')) {
                $table->string('facultad', 120)->nullable()->after('id_estudiante');
            }
            if (! Schema::hasColumn('estudiante', 'ci_complemento')) {
                // Complemento opcional del carnet de identidad (letras y/o números, máx. 2 caracteres).
                $table->string('ci_complemento', 2)->nullable()->after('documento_identidad');
            }
        });
    }

    public function down(): void
    {
        Schema::table('estudiante', function (Blueprint $table) {
            $table->dropColumn(['facultad', 'ci_complemento']);
        });
    }
};
