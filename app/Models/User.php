<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'rol',
        'estado',
    ];

    public const ROLES = [
        'administrador' => 'Administrador',
        'docente' => 'Docente',
        'control' => 'Personal de control',
    ];

    /** "Carlos Choque" -> "CC" */
    public function getInicialesAttribute(): string
    {
        $palabras = preg_split('/\s+/', trim(preg_replace('/^(Lic|Ing|Dr|Dra|Mg|Msc)\.?\s+/i', '', $this->name)));

        return mb_strtoupper(collect($palabras)->take(2)->map(fn ($w) => mb_substr($w, 0, 1))->implode(''));
    }

    public function getRolEtiquetaAttribute(): string
    {
        return self::ROLES[$this->rol] ?? ucfirst((string) $this->rol);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
