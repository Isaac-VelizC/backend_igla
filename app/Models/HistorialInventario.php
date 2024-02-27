<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialInventario extends Model
{
    use HasFactory;
    protected $table = "historial_inventarios";
    protected $primaryKey = "id";
    protected $fillable = ['cantidad','user_id', 'inventario_id', 'descripcion', 'fecha', 'estado'];

    public function inventario()
    {
        return $this->belongsTo(Inventario::class, 'inventario_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
