<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatCritTrabajo extends Model
{
    use HasFactory;
    protected $table = "catCritTrabajo";
    protected $primaryKey = "id";
    protected $fillable = ['cat_id', 'tarea_id'];
    
    public function categoriaCriterio()
    {
        return $this->belongsTo(CategoriaCriterio::class, 'cat_id');
    }

    public function trabajo()
    {
        return $this->belongsTo(Trabajo::class, 'tarea_id');
    }
}
