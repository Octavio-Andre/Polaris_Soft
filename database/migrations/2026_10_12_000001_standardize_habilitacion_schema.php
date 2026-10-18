<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Compatibilidad con la versión previa de la tabla `habilitacion`.
     * La base actual del proyecto usa columnas `estudiante_id`, `examen_id`, `estado`, etc.
     * Si en la base existe la estructura antigua (`id_estudiante`, `id_examen`, `habilitado`),
     * esta migración crea los campos nuevos y copia los datos.
     */
    public function up(): void
    {
        if (! Schema::hasTable('habilitacion')) {
            return;
        }

        if (Schema::hasColumn('habilitacion', 'estudiante_id')) {
            return;
        }

        if (! Schema::hasColumn('habilitacion', 'estudiante_id')) {
            DB::statement('ALTER TABLE `habilitacion` ADD `estudiante_id` BIGINT UNSIGNED NULL AFTER `id_habilitacion`');
        }

        if (! Schema::hasColumn('habilitacion', 'examen_id')) {
            DB::statement('ALTER TABLE `habilitacion` ADD `examen_id` BIGINT UNSIGNED NULL AFTER `estudiante_id`');
        }

        if (! Schema::hasColumn('habilitacion', 'estado')) {
            DB::statement('ALTER TABLE `habilitacion` ADD `estado` VARCHAR(20) NOT NULL DEFAULT "habilitado" AFTER `examen_id`');
        }

        if (! Schema::hasColumn('habilitacion', 'motivo_inhabilitacion_id')) {
            DB::statement('ALTER TABLE `habilitacion` ADD `motivo_inhabilitacion_id` BIGINT UNSIGNED NULL AFTER `estado`');
        }

        if (! Schema::hasColumn('habilitacion', 'observacion')) {
            DB::statement('ALTER TABLE `habilitacion` ADD `observacion` TEXT NULL AFTER `motivo_inhabilitacion_id`');
        }

        if (! Schema::hasColumn('habilitacion', 'created_at')) {
            DB::statement('ALTER TABLE `habilitacion` ADD `created_at` TIMESTAMP NULL DEFAULT NULL AFTER `observacion`');
        }

        if (! Schema::hasColumn('habilitacion', 'updated_at')) {
            DB::statement('ALTER TABLE `habilitacion` ADD `updated_at` TIMESTAMP NULL DEFAULT NULL AFTER `created_at`');
        }

        if (Schema::hasColumn('habilitacion', 'id_estudiante')) {
            DB::statement('UPDATE `habilitacion` SET `estudiante_id` = `id_estudiante` WHERE `estudiante_id` IS NULL');
        }

        if (Schema::hasColumn('habilitacion', 'id_examen')) {
            DB::statement('UPDATE `habilitacion` SET `examen_id` = `id_examen` WHERE `examen_id` IS NULL');
        }

        if (Schema::hasColumn('habilitacion', 'habilitado')) {
            DB::statement("UPDATE `habilitacion` SET `estado` = CASE WHEN CAST(habilitado AS UNSIGNED) = 1 THEN 'habilitado' ELSE 'inhabilitado' END WHERE `estado` = 'habilitado' OR `estado` = 'inhabilitado' OR `estado` = '' OR `estado` IS NULL");
        }

        if (Schema::hasColumn('habilitacion', 'motivo_inhabilitacion')) {
            DB::statement("UPDATE `habilitacion` SET `motivo_inhabilitacion_id` = CAST(motivo_inhabilitacion AS UNSIGNED) WHERE `motivo_inhabilitacion_id` IS NULL AND motivo_inhabilitacion REGEXP '^[0-9]+$'");
        }

        if (Schema::hasColumn('habilitacion', 'fecha_verificacion')) {
            DB::statement('UPDATE `habilitacion` SET `created_at` = `fecha_verificacion`, `updated_at` = `fecha_verificacion` WHERE `created_at` IS NULL');
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('habilitacion')) {
            return;
        }

        if (Schema::hasColumn('habilitacion', 'estudiante_id') && Schema::hasColumn('habilitacion', 'id_estudiante')) {
            DB::statement('UPDATE `habilitacion` SET `id_estudiante` = `estudiante_id` WHERE `id_estudiante` IS NULL');
        }

        if (Schema::hasColumn('habilitacion', 'examen_id') && Schema::hasColumn('habilitacion', 'id_examen')) {
            DB::statement('UPDATE `habilitacion` SET `id_examen` = `examen_id` WHERE `id_examen` IS NULL');
        }

        if (Schema::hasColumn('habilitacion', 'estado') && Schema::hasColumn('habilitacion', 'habilitado')) {
            DB::statement("UPDATE `habilitacion` SET `habilitado` = CASE WHEN `estado` = 'habilitado' THEN 1 ELSE 0 END");
        }

        if (Schema::hasColumn('habilitacion', 'motivo_inhabilitacion_id') && Schema::hasColumn('habilitacion', 'motivo_inhabilitacion')) {
            DB::statement('UPDATE `habilitacion` SET `motivo_inhabilitacion` = `motivo_inhabilitacion_id` WHERE `motivo_inhabilitacion` IS NULL');
        }

        if (Schema::hasColumn('habilitacion', 'created_at') && Schema::hasColumn('habilitacion', 'fecha_verificacion')) {
            DB::statement('UPDATE `habilitacion` SET `fecha_verificacion` = `created_at` WHERE `fecha_verificacion` IS NULL');
        }
    }
};
