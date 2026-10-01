<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

/*
 * Crea un usuario de prueba por cada rol, para poder entrar al sistema
 * en cualquier computadora sin tener que crearlos a mano con Tinker.
 *
 * Se puede correr las veces que quieras: si el usuario ya existe, solo
 * le restablece la contraseña y el rol (no crea duplicados).
 *
 *   admin@test.com    / 12345678  ->  Administrador
 *   docente@test.com  / 12345678  ->  Docente
 *   control@test.com  / 12345678  ->  Control de ingreso
 *
 * Son usuarios SOLO para desarrollo/pruebas.
 */
class UsuariosSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = [
            ['name' => 'Administrador', 'email' => 'admin@test.com',   'rol' => 'administrador', 'rol_oficial' => 'ADMINISTRADOR'],
            ['name' => 'Docente',       'email' => 'docente@test.com', 'rol' => 'docente',       'rol_oficial' => 'DOCENTE'],
            ['name' => 'Control',       'email' => 'control@test.com', 'rol' => 'control',       'rol_oficial' => 'CONTROL_INGRESO'],
        ];

        foreach ($usuarios as $u) {
            $user = User::updateOrCreate(
                ['email' => $u['email']],
                ['name' => $u['name'], 'password' => Hash::make('12345678')]
            );

            // Caso 1: el rol es una columna de texto en users (así está main hoy).
            if (Schema::hasColumn('users', 'rol')) {
                DB::table('users')->where('id', $user->id)->update(['rol' => $u['rol']]);
            }

            // Caso 2: el rol viene de la tabla oficial 'rol' + 'user_rol' (Parte 1 de los arreglos).
            if (Schema::hasTable('rol') && Schema::hasTable('user_rol')) {
                $rol = DB::table('rol')->where('nombre', $u['rol_oficial'])->first();

                if ($rol) {
                    DB::table('user_rol')->updateOrInsert(
                        ['user_id' => $user->id, 'id_rol' => $rol->id_rol],
                        ['created_at' => now(), 'updated_at' => now()]
                    );
                }
            }
        }
    }
}
