<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    use HasFactory;
    protected $table = "estudiantes";
    protected $primaryKey = "id";
    protected $fillable = ['persona_id', 'contact_id', 'turno_id', 'direccion', 'fecha_nacimiento', 'grado', 'estado', 'titulo', 'graduado'];
    public function asistencias()
    {
        return $this->hasMany(Asistencia::class, 'estudiante_id', 'id');
    }
    public function contacto()
    {
        return $this->belongsTo(Contacto::class, 'contact_id');
    }
    public function inscripciones()
    {
        return $this->hasMany(Programacion::class, 'estudiante_id');
    }
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }
    public function turnos()
    {
        return $this->belongsTo(Horario::class, 'turno_id');
    }
    public function trabajosEstudiantes()
    {
        return $this->hasMany(TrabajoEstudiante::class, 'estudiante_id');
    }
    public function pagos()
    {
        return $this->hasMany(Pagos::class, 'est_id');
    }
    public function calificaciones()
    {
        return $this->hasMany(Calificacion::class, 'estudiante_id');
    }
    public function programados()
    {
        return $this->hasMany(Programacion::class, 'estudiante_id');
    }
}
