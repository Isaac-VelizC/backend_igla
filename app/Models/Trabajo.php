<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trabajo extends Model
{
    use HasFactory;
    protected $table = "trabajos";
    protected $primaryKey = "id";
    protected $fillable = ['tipo',
        'curso_id',
        'user_id',
        'tema_id',
        'criterio_id',
        'ingredientes',
        'receta_id',
        'evaluacion',
        'titulo',
        'descripcion',
        'inico',
        'fin',
        'con_nota',
        'nota',
        'visible',
        'estado'
    ];
    
    public function catCritTrabajos() {
        return $this->hasMany(CatCritTrabajo::class, 'tarea_id');
    }

    public function curso()
    {
        return $this->belongsTo(CursoHabilitado::class, 'curso_id');
    }
    public function tema()
    {
        return $this->belongsTo(Tema::class, 'tema_id');
    }
    public function tareasEstudiantes()
    {
        return $this->hasMany(TrabajoEstudiante::class, 'tarea_id');
    }
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function criterio()
    {
        return $this->belongsTo(Criterio::class, 'criterio_id');
    }
    public function receta()
    {
        return $this->belongsTo(Receta::class, 'receta_id', 'id');
    }
    public function ingredientes()
    {
        return $this->belongsToMany(Ingrediente::class, 'ingrediente_recetas', 'receta_id', 'ingrediente_id')
            ->withPivot(['cantidad', 'unida_media']);
    }
}
