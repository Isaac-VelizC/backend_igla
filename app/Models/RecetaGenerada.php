<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecetaGenerada extends Model
{
    use HasFactory;
    protected $table = "receta_generadas";
    protected $primaryKey = "id";
    protected $fillable = ['receta', 'fecha', 'estado'];    
    protected $casts = [
        'receta' => 'json',
    ];
}
