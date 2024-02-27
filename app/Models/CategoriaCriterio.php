<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaCriterio extends Model
{
    use HasFactory;
    
    protected $table = "categorias_criterio";
    protected $primaryKey = "id";
    protected $fillable = ['nombre', 'porcentaje', 'total', 'criterio_id'];

    public function catCritTrabajos()
    {
        return $this->hasMany(CatCritTrabajo::class, 'cat_id');
    }
    
    public function criterio()
    {
        return $this->belongsTo(Criterio::class, 'criterio_id');
    }
}
