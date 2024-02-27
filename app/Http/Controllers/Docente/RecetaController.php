<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\RecetaGenerada;

class RecetaController extends Controller
{
    
    public function listRecetasGeneradas() {
        try {
            $lista = RecetaGenerada::all();
            return view('docente.recetas.lista_recetas', compact('lista'));
        } catch (\Throwable $th) {
            return back()->with('error', 'Error al guardar las recetas: ' . $th->getMessage());
        }
    }

}
