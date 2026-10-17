<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bug 7-2 del QA: "Unknown column 'id_habilitacion' in 'where clause'" al inhabilitar.
     *
     * El modelo Habilitacion usa la llave primaria `id_habilitacion`, pero en las bases creadas desde cero con
     * las migraciones la tabla quedó con la columna `id`. Al actualizar una fila existente (por ejemplo pasar
     * de habilitado a inhabilitado) Laravel hacía "WHERE id_habilitacion = ?" y fallaba con error 500.
     * Aquí se renombra `id` a `id_habilitacion` (sin perder datos) para que todas las bases queden iguales.
     */
    public function up(): void
    {
        if (! Schema::hasTable('habilitacion')) {
            return;
        }

        if (Schema::hasColumn('habilitacion', 'id') && ! Schema::hasColumn('habilitacion', 'id_habilitacion')) {
            DB::statement('ALTER TABLE `habilitacion` CHANGE `id` `id_habilitacion` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('habilitacion')) {
            return;
        }

        if (Schema::hasColumn('habilitacion', 'id_habilitacion') && ! Schema::hasColumn('habilitacion', 'id')) {
            DB::statement('ALTER TABLE `habilitacion` CHANGE `id_habilitacion` `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        }
    }
};
