<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pagos extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "pagos";
    protected $primaryKey = "id";
    protected $fillable = ['responsable_id', 'est_id', 'forma_id', 'metodo_id', 'fecha', 'monto', 'estado', 'comentario', 'pagoMes_id'];
    
    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'est_id');
    }

    public function formaPago()
    {
        return $this->belongsTo(FormaPago::class, 'forma_id');
    }

    public function metodoPago()
    {
        return $this->belongsTo(MetodoPago::class, 'metodo_id');
    }
    public function pagoMensual()
    {
        return $this->belongsTo(PagoMensual::class, 'pagoMes_id', 'id');
    }
}
