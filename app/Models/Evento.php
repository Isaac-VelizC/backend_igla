<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    use HasFactory;
    protected $table = "eventos";
    protected $primaryKey = "id";
    static $rules = [
        'tipo_id' => 'required|numeric',
        'title' => 'required|string',
        'start' => 'required|date',
        'end' => 'required|date',
    ];
    protected $fillable = ['tipo_id', 'start', 'end', 'title', 'descripcion', 'estado'];

    public function tipo()
    {
        return $this->belongsTo(TipoEvento::class, 'tipo_id');
    }
}
