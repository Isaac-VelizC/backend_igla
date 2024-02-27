<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Http\Controllers\InfoController;
use App\Models\CursoHabilitado;
use App\Models\DocumentoDocente;
use App\Models\Ingrediente;
use App\Models\Receta;
use App\Models\Tema;
use App\Models\Trabajo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CursoController extends Controller
{
    public function index() {
        $idDocente = auth()->user()->persona->docente->id;
        $cursos_A = CursoHabilitado::where(['estado' => true, 'docente_id' => $idDocente ])->get();
        $cursos_I = CursoHabilitado::where(['estado' => false, 'docente_id' => $idDocente ])->get();
        return view('docente.cursos.index', compact('cursos_A', 'cursos_I'));
    }

    public function curso($id) {
        $user = Auth::user();
        $role = $user->roles->first();
        $curso = CursoHabilitado::find($id);
        return view('docente.cursos.show', compact('curso', 'role'));
    }
    public function createTareaNew($id) {
        $isEditing = false;
        $temasCurso = Tema::where('curso_id', $id)->get();
        return view('docente.cursos.create_tarea', compact('temasCurso', 'id', 'isEditing'));
    }
    public function selectReceta(Request $request) {
        $query = $request->input('name');
    
        $recetas = Receta::where('titulo', 'like', "%$query%")->get();
    
        return response()->json($recetas);
    }

    public function crearTarea(Request $request) {
        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:255',
            'fin' => ['date', 'after_or_equal:' . now()->toDateString()],
            'tipo_trabajo' => 'required|string|max:255',
            'tema' => 'nullable|numeric|exists:temas,id',
            'con_nota' => 'required|boolean',
            'evaluacion' => 'required|boolean',
            'tags.*' => 'exists:ingredientes,id',
            'recetas' => 'exists:recetas,id',
            'files.*' => 'file'
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $tarea = Trabajo::create([
                'tipo' => $request->tipo_trabajo,
                'curso_id' => $request->curso,
                'user_id' => auth()->user()->id,
                'tema_id' => $request->tema ?: null,
                'ingredientes' => json_encode($request->tags) ?: null,
                'receta_id' => $request->recetas ?: null,
                'evaluacion' => $request->evaluacion,
                'titulo' => $request->titulo,
                'descripcion' => $request->descripcion,
                'inico' => now(),
                'fin' => $request->fin,
                'con_nota' => $request->con_nota,
                'nota' => 100,
                'estado' => 'Publicado'
            ]);

            $files = $request->file('files');
            if ($files) {
                foreach ($files as $file) {
                    $originalName = $file->getClientOriginalName();
                    $filePath = $file->storeAs('public/files', $originalName);
                    $url = str_replace('public/', '', $filePath);
                    DocumentoDocente::create([
                        'nombre' => $originalName,
                        'url' => 'storage/'.$url,
                        'fecha' => now(),
                        'materia_id' => $request->curso,
                        'user_id' => auth()->user()->id,
                        'tarea_id' => $tarea->id
                    ]);
                }
            }
            $message = "Titulo: " . $tarea->titulo . ", Fecha de entrega " . $tarea->fin . ".";
            InfoController::notificacionTrabajoPublicado($request->curso, $message);
            return redirect()->route('cursos.curso', $request->curso)->with('success', 'Trabajo publicado con éxito');
        } catch (\Throwable $th) {
            return back()->with(['error' => 'Error al crear el evento', 'error' => $th->getMessage()], 500);
        }
    }
    public function editarTareaEdit($id) {
        $trabajo = Trabajo::find($id);
        $ingredientes = json_decode($trabajo->ingredientes, true);
    
        $ingrests = [];
        if ($ingredientes) {
            foreach ($ingredientes as $key => $value) {
                $ingrests[] = Ingrediente::find($value);
            }
        }
        $isEditing = true;
        $temasCurso = Tema::where('curso_id', $trabajo->curso_id)->get();
        $files = DocumentoDocente::where('tarea_id', $id)->get();
        
        return view('docente.cursos.create_tarea', compact('temasCurso', 'isEditing', 'files', 'trabajo', 'ingrests'));
    }
    
    public function updateTrabajo(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:255',
            'fin' => ['date', 'after_or_equal:' . now()->toDateString()],
            'tipo_trabajo' => 'required|string|max:255',
            'tema' => 'nullable|numeric|exists:temas,id',
            'con_nota' => 'required|boolean',
            'evaluacion' => 'required|boolean',
            'tags.*' => 'exists:ingredientes,id',
            'recetas' => 'exists:recetas,id',
            'files.*' => 'file'
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        try {
            $tarea = Trabajo::find($id)->update([
                'tipo' => $request->tipo_trabajo,
                'tema_id' => $request->tema ?: null,
                'ingredientes' => json_encode($request->tags) ?: null,
                'receta_id' => $request->recetas ?: null,
                'evaluacion' => $request->evaluacion,
                'titulo' => $request->titulo,
                'descripcion' => $request->descripcion,
                'inico' => now(),
                'fin' => $request->fin,
                'con_nota' => $request->con_nota,
            ]);
            $files = $request->file('files');
            if ($files) {
                foreach ($files as $file) {
                    $originalName = $file->getClientOriginalName();
                    $filePath = $file->storeAs('public/files', $originalName);
                    $url = str_replace('public/', '', $filePath);
                    DocumentoDocente::create([
                        'nombre' => $originalName,
                        'url' => 'storage/'.$url,
                        'fecha' => now(),
                        'materia_id' => $request->curso,
                        'user_id' => auth()->user()->id,
                        'tarea_id' => $tarea->id
                    ]);
                }
            }
            return redirect()->route('cursos.curso', $request->curso)->with('success', 'Trabajo actualizado con éxito');
        } catch (\Throwable $th) {
            return back()->with(['error' => 'Error al actualizar el trabajo', 'error' => $th->getMessage()], 500);
        }
    }
    public function borrarFile($id) {
        try {
            DocumentoDocente::find($id)->delete();
            return back();
        } catch (\Throwable $th) {
            return back()->with(['error' => 'Error al borrar el archivo', 'error' => $th->getMessage()], 500);
        }
    }

    public function viewTemeEdit($id) {
        $tema = Tema::find($id);
        return view('docente.cursos.edit_tema', compact('tema'));
    }
    public function updateTema(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255|min:5',
            'descripcion' => 'nullable|string|max:255'
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        try {
            $item = Tema::find($id);
            if ($item) {
                $item->update([
                    'tema' => $request->nombre,
                    'descripcion' => $request->descripcion
                ]);

                return redirect()->route('cursos.curso', $item->curso_id);
            } else {
                // Manejar el caso en que no se encuentre el tema con el ID dado
                return back()->with('error', 'El tema no fue encontrado.');
            }
        } catch (\Throwable $th) {
            return back()->with(['error' => 'Error al crear el evento', 'error' => $th->getMessage()], 500);
        }
    }
}
