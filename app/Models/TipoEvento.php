<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoEvento extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "tipo_eventos";
    protected $primaryKey = "id";
    protected $fillable = ['nombre', 'backgroundColor', 'textColor'];
    public function eventos()
    {
        return $this->hasMany(Evento::class, 'tipo_id');
    }
}
