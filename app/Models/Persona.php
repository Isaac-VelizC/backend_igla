<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    use HasFactory;
    protected $table = "personas";
    protected $primaryKey = "id";
    protected $fillable = ['user_id', 'nombre', 'ap_paterno', 'ap_materno', 'ci','genero', 'email', 'photo', 'rol', 'estado'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function docente()
    {
        return $this->hasOne(Docente::class, 'id_persona');
    }

    public function numTelefono()
    {
        return $this->hasOne(NumTelefono::class, 'id_persona');
    }
    public function personal()
    {
        return $this->hasOne(Personal::class, 'persona_id', 'id');
    }
    public function estudiante()
    {
        return $this->hasOne(Estudiante::class, 'persona_id');
    }
    public function contacto()
    {
        return $this->hasOne(Contacto::class, 'persona_id');
    }
}
