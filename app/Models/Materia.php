<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    protected $fillable = ['nombre', 'sigla'];

    public function grupos()
    {
        return $this->hasMany(Grupo::class);
    }
}
