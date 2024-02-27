<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

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
    
    public function persona()
    {
        return $this->hasOne(Persona::class, 'user_id');
    }
    public function trabajos()
    {
        return $this->hasMany(Trabajo::class, 'user_id');
    }
    public function comentarios()
    {
        return $this->hasMany(ComentarioCurso::class, 'autor_id');
    }

    public function cursosResponsable()
    {
        return $this->hasMany(CursoHabilitado::class, 'responsable_id');
    }
    
    public function pagos()
    {
        return $this->hasMany(Pagos::class, 'responsable_id');
    }
}
