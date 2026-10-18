<?php

use App\Support\Catalogo;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Facultad a la que pertenece la materia (se deduce de la carrera en las materias que ya existían). */
    public function up(): void
    {
        if (! Schema::hasColumn('materias', 'facultad')) {
            Schema::table('materias', function (Blueprint $table) {
                $table->string('facultad', 120)->nullable()->after('sigla');
            });
        }

        foreach (DB::table('materias')->whereNotNull('carrera')->whereNull('facultad')->get(['id', 'carrera']) as $m) {
            $facultad = Catalogo::facultadDeCarrera($m->carrera);
            if ($facultad) {
                DB::table('materias')->where('id', $m->id)->update(['facultad' => $facultad]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('materias', function (Blueprint $table) {
            $table->dropColumn('facultad');
        });
    }
};
