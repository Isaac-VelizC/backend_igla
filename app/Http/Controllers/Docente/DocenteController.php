<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\CursoHabilitado;
use App\Models\Receta;
use Illuminate\Http\Request;

class DocenteController extends Controller
{
    public function index() {
        $cursos = CursoHabilitado::where('docente_id', auth()->user()->persona->docente->id)->get();
        $recetas = Receta::all();
        return view('docente.home', compact('cursos', 'recetas'));
    }
    public function planificacion(Request $request, $id) {
        $request->validate([
            'imagen' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);        
        try {
            $curso = CursoHabilitado::find($id);
            $curso->descripcion = $request->planificacion;
            if ($request->imagen) {
                $path = $request->imagen->store('public/files');
                $path = str_replace('public/', '', $path);
                $curso->imagen = 'storage/'.$path;
            }
            $curso->update();
    
            return back()->with('message', 'Planificacion guardada');
        } catch (\Exception $e) {
            return back()->with('message', 'Error al guardar la planificacion');
        }
    }
}
