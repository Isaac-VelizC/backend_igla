<?php

namespace App\Http\Controllers\Admin;

use App\Exports\CursosExport;
use App\Http\Controllers\Controller;
use App\Models\Aula;
use App\Models\Calificacion;
use App\Models\Curso;
use App\Models\CursoHabilitado;
use App\Models\Docente;
use App\Models\Estudiante;
use App\Models\Horario;
use App\Models\Programacion;
use App\Models\Semestre;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CursoController extends Controller
{
    public function index() {
        try {
            $cursos = Curso::all();
            $modulos = Semestre::all();
            return response()->json(['cursos' => $cursos, 'modulos' => $modulos], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al recuperar cursos y módulos: ' . $e->getMessage()], 500);
        }
    }

    public function showCurso($id) {
        try {
            $curso = CursoHabilitado::with('inscripciones.estudiante')->find($id);
            if (!$curso) {
                return response()->json(['error' => 'Curso no encontrado'], 404);
            }
            $estudiantes = $curso->inscripciones->pluck('estudiante');
            $num = 1;
            $data = [
                'curso' => $curso,
                'estudiantes' => $estudiantes,
                'num' => $num
            ];
            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al recuperar información del curso: ' . $e->getMessage()], 500);
        }
    }
    

    //Convetirn JSON
    public function guardarCurso(Request $request) {
        try {
            $this->validate($request, [
                'nombre' => 'required|string|max:255|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/u',
                'semestre' => 'required|numeric|exists:semestres,id',
                'descripcion' => 'nullable|string|max:255',
                'dependencia' => 'nullable|numeric|exists:cursos,id'
            ]);
            $curso = new Curso();
            $curso->nombre = $request->nombre;
            $curso->semestre_id = $request->semestre;
            $curso->color = $request->color;
            $curso->descripcion = $request->descripcion;
            $curso->dependencia = $request->dependencia ?? 0;
            $curso->save();
            return back()->with('success', 'La materia ' . $curso->nombre . ' se registro con éxito.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar el curso: ' . $e->getMessage());
        }
    }
    public function darBajaCurso($id) {
        $curso = Curso::updateOrCreate(['id' => $id], ['estado' => false]);
        return back()->with('success', 'La materia '. $curso->nombre .' se dio de baja con éxito.');
    }
    public function deleteCurso($id) {
        Curso::where('id', $id)->delete();
        return back()->with('success', 'La materia se elimino con éxito.');
    }
    public function darAltaCurso($id) {
        $curso = Curso::updateOrCreate(['id' => $id], ['estado' => true]);
        return back()->with('success', 'La materia '. $curso->nombre .' se dio de alta con éxito.');
    }
    public function actualizarCurso(Request $request, $id) {
        try {
            $request->validate([
                'nombre' => 'required|string|max:255|regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/u',
                'semestre' => 'required|numeric|exists:semestres,id',
                'descripcion' => 'nullable|string|max:255',
                'dependencia' => 'nullable|numeric|exists:cursos,id'
            ]);
            $curso = Curso::findOrFail($id);
            $curso->update([
                'nombre' => $request->nombre,
                'semestre_id' => $request->semestre,
                'color' => $request->color,
                'descripcion' => $request->descripcion,
                'dependencia' => $request->dependencia ?? 0,
            ]);
            return redirect()->route('admin.cursos')->with('success', 'La información se ha actualizado con éxito.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar el curso: ' . $e->getMessage());
        }
    }

    public function asignarCurso($id) {
        $docentes = Docente::where('estado', true)->get();
        $aulas = Aula::where('estado', true)->get();
        $horarios = Horario::all();
        $materia = Curso::find($id);
        $isEditing = false;
        return view('admin.cursos.habilitar', compact('materia', 'docentes', 'aulas', 'horarios', 'isEditing'));
    }
    public function asignarGuardarCurso(Request $request) {
        $rules = [
            'docente' => 'required|string|max:255|exists:docentes,id',
            'curso' => 'required|numeric|exists:cursos,id',
            'horario' => 'required|numeric|exists:horarios,id',
            'aula' => 'required|numeric|exists:aulas,id',
            'fechaInicio' => 'required|date',
            'fechaFin' => 'required|date|after:fInico',
            'cupo' => 'required|numeric|min:1'
        ];
        $request->validate($rules);

        $docenteOcupado = CursoHabilitado::where('docente_id', $request->docente)
        ->where('estado', true)
        ->where('horario_id', $request->horario)
        ->where('fecha_fin', '>=', $request->fechaInicio)->first();

        if ($docenteOcupado) {
        return redirect()->back()->with('error', 'El docente ya está asignado en ese horario.');
        }
        $aulaHorarioOcupado = CursoHabilitado::where('aula_id', $request->aula)
        ->where('estado', true)
        ->where('horario_id', $request->horario)
        ->where('fecha_fin', '>=', $request->fechaInicio)->first();

        if ($aulaHorarioOcupado) {
        return redirect()->back()->with('error', 'El aula ya está ocupada en ese horario por una materia activa.');
        }

        $curso = new CursoHabilitado();
        $curso->fill([
        'docente_id' => $request->docente,
        'curso_id' => $request->curso,
        'responsable_id' => auth()->user()->id,
        'aula_id' => $request->aula,
        'cupo' => $request->cupo,
        'horario_id' => $request->horario,
        'fecha_ini' => $request->fechaInicio,
        'fecha_fin' => $request->fechaFin,
        ]);
        $curso->save();
        return redirect()->route('admin.cursos.activos')->with('success', 'El curso se habilito con éxito.');
    }
    public function cursosActivos() {
        $cursos = CursoHabilitado::all();
        return view('admin.cursos.cursos_habilitados', compact('cursos'));
    }
    
    
    public function editCursoAsignado($id) {
        $docentes = Docente::where('estado', true)->get();
        $aulas = Aula::where('estado', true)->get();
        $horarios = Horario::all();
        $asignado = CursoHabilitado::find($id);
        $materia = Curso::find($asignado->curso_id);
        $isEditing = true;
        return view('admin.cursos.habilitar', compact('docentes', 'materia', 'horarios', 'isEditing', 'asignado', 'aulas'));
    }
    public function asignarActualizarCurso(Request $request, $id) {
        $this->validate($request, [
            'docente' => 'string|max:255|exists:docentes,id',
            'curso' => 'required|numeric|exists:cursos,id',
            'horario' => 'required|numeric|exists:horarios,id',
            'aula' => 'numeric|exists:aulas,id',
            'fechaInicio' => 'required|date',
            'fechaFin' => 'required|date|after:fInico',
            'cupo' => 'required|integer|min:1'
        ]);
        $docenteOcupado = CursoHabilitado::where('docente_id', $request->docente)
        ->where('estado', true)
        ->where('horario_id', $request->horario)
        ->where('fecha_fin', '>=', $request->fechaInicio)
        ->where('id', '!=', $id)
        ->first();

        if ($docenteOcupado) {
        return redirect()->back()->with('error', 'El docente ya está asignado en ese horario.');
        }

        $aulaHorarioOcupado = CursoHabilitado::where('aula_id', $request->aula)
        ->where('estado', true)
        ->where('horario_id', $request->horario)
        ->where('fecha_fin', '>=', $request->fechaInicio)
        ->where('id', '!=', $id)
        ->first();

        if ($aulaHorarioOcupado) {
        return redirect()->back()->with('error', 'El aula ya está ocupada en ese horario por una materia activa.');
        }
        $curso = CursoHabilitado::find($id);
        $curso->update([
            'docente_id' => $request->docente ?? $curso->docente_id,
            'curso_id' => $request->curso,
            'aula_id' => $request->aula ?? $curso->aula_id,
            'cupo' => $request->cupo,
            'horario_id' => $request->horario,
            'fecha_ini' => $request->fechaInicio,
            'fecha_fin' => $request->fechaFin,
        ]);
        return redirect()->route('admin.cursos.activos')->with('success', 'El curso se actualizo con éxito.');
    }
    public function gestionarEstadoCurso(Request $request, $id) {
        try {
            $curso = CursoHabilitado::updateOrCreate(['id' => $id], ['estado' => $request->estado]);
            $calificaciones = Calificacion::where('curso_id', $id)->get();
    
            foreach ($calificaciones as $calificacion) {
                $programacion = Programacion::where('estudiante_id', $calificacion->estudiante_id)
                    ->where('curso_id', $id)
                    ->first();
    
                if ($programacion) {
                    if ($calificacion->calificacion > 51) {
                        $programacion->estado_materia = 'Aprobado';
                    } else {
                        $programacion->estado_materia = 'Reprobado';
                    }
    
                    $programacion->save();
    
                    $estadoAprobado = $this->verificarSemestre($curso->curso->semestre_id, $calificacion->estudiante_id);
    
                    if ($estadoAprobado) {
                        Estudiante::find($calificacion->estudiante_id)->increment('grado');
                    }
                }
            }
    
            $action = $request->estado ? 'alta' : 'baja';
            return back()->with('success', "La materia {$curso->nombre} se dio de {$action} con éxito.");
        } catch (\Exception $e) {
            return back()->with('error', 'Ocurrió un error al gestionar el estado del curso: ' . $e->getMessage());
        }
    }
    
    public function verificarSemestre($idSemestre, $idEstudiante) {
        try {
            $cursos = Curso::where('semestre_id', $idSemestre)->get();
    
            $calificaciones = Programacion::whereIn('curso_id', $cursos->pluck('id'))
                ->where('estudiante_id', $idEstudiante)
                ->where('estado_materia', 'Aprobado')
                ->get();
    
            foreach ($cursos as $curso) {
                $calificacion = $calificaciones->where('curso_id', $curso->id)->first();
    
                if (!$calificacion) {
                    return false;
                }
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    public function deleteCursoActivo($id) {
        CursoHabilitado::where('id', $id)->delete();
        return back()->with('success', 'La materia se eliminó con éxito.');
    }
    public function exportarCurso() {
        try {
            $cursos = Curso::with('semestre')
            ->select('nombre', 'color', 'semestre_id', 'estado')
            ->get();
            if ($cursos->isEmpty()) {
                return redirect()->back()->with('error', 'No hay datos para exportar.');
            }
        
            $fileName = 'cursos_' . now()->format('Ymd_His') . '.xlsx';
            return Excel::download(new CursosExport($cursos), $fileName);
        } catch (\Exception $e) {
          return redirect()->back()->with('error', 'Error al exportar los datos: ' . $e->getMessage());
        }
    }
    
    public function obtenerDisponibilidad(Request $request)
    {
        $horario = $request->input('horario');
        // Lógica para obtener docentes disponibles en el horario seleccionado
        $docentesDisponibles = Docente::whereNotIn('id', function ($query) use ($horario) {
            $query->select('docente_id')
                ->from('curso_habilitados')
                ->where('horario_id', $horario)
                ->where('estado', true)
                ->where('fecha_fin', '>=', now());
        })->get();
        $docentes = $docentesDisponibles->map(function ($event) {
            return [
                'id' => $event->id,
                'nombre' => $event->persona->nombre,
                'ap_paterno' => $event->persona->ap_paterno,
                'ap_materno' => $event->persona->ap_materno
            ];
        });
        // Lógica para obtener aulas disponibles en el horario seleccionado
        $aulasDisponibles = Aula::whereNotIn('id', function ($query) use ($horario) {
            $query->select('aula_id')
                ->from('curso_habilitados')
                ->where('horario_id', $horario)
                ->where('estado', true)
                ->where('fecha_fin', '>=', now());
        })->get();

        return response()->json([
            'docentes' => $docentes,
            'aulas' => $aulasDisponibles,
        ]);
    }
    public function obtenerCursosAnteriores(Request $request) {
        $semestreId = $request->input('semestreId');
        $cursos = Curso::where('semestre_id', '<=', $semestreId)->get();
        return response()->json(['cursos' => $cursos]);
    }
}