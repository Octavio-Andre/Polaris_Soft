<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Bitácora de cada verificación de identidad hecha en la puerta (código, CI o QR). */
class Verificacion extends Model
{
    protected $table = 'verificacion';

    public $timestamps = false;

    public const TIPOS = ['CODIGO_UNIVERSITARIO', 'DOCUMENTO_IDENTIDAD', 'CODIGO_QR'];

    protected $fillable = [
        'estudiante_id', 'examen_id', 'controlador_id', 'tipo', 'dato_verificado', 'resultado', 'fecha_hora',
    ];

    protected $casts = ['fecha_hora' => 'datetime'];
}
