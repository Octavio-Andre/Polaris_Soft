<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Docente extends Model
{
    protected $fillable = ['nombre', 'apellido', 'grado', 'ci'];

    public function grupos()
    {
        return $this->hasMany(Grupo::class);
    }

    /** "Juan" + "Pérez" -> "Ing. Juan Pérez" (si tiene grado) */
    public function getNombreCompletoAttribute(): string
    {
        $base = trim($this->nombre.' '.$this->apellido);

        return $this->grado ? "{$this->grado} {$base}" : $base;
    }
}
