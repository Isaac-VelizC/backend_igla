<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CalendarioController;
use App\Http\Controllers\Admin\CursoController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();
Route::get('check-email-unique/{email}/{id}', [AdminController::class, 'checkEmailUnique']);
Route::get('check-ci-unique/{ci}/{id}', [AdminController::class, 'checkCiUnique']);
Route::get('check-telefono-unique/{telefono}/{id}', [AdminController::class, 'checkTelefonoUnique']);

Route::middleware(['auth:sanctum'])->group(function (){
    Route::get('auth/me', [AdminController::class, 'myInfo']);
    Route::get('auth/logout', [LoginController::class, 'logout']);
    Route::get('auth/roles', [AdminController::class, 'getRoles']);
    
    Route::get('auth/admin-users', [AdminController::class, 'allUsers'])->name('admin.users');
    //Estudiantes
    Route::get('auth/admin-estudiantes', [UsersController::class, 'estudiantesAll'])->name('admin.estudinte');
    Route::get('/show/{id}/estudiante', [UsersController::class, 'showEstudiante'])->name('admin.E.show');
    Route::post('/selectEstudi',[UsersController::class, 'selectEstudiante'])->name('search.estudiantes');
    //Docentes
    Route::get('auth/admin-docentes', [UsersController::class, 'allDocentes']);
    Route::put('auth/admin-docentes/{id}/edit', [UsersController::class, 'updateDocenteInfo']);
    Route::post('create-docentes/store', [UsersController::class, 'store']);
    //Personals
    Route::get('auth/admin-personal', [UsersController::class, 'allPersonal'])->name('admin.personal');
    Route::post('personal-new/store', [AdminController::class, 'storePersonal'])->name('admin.personal.store');
    //Cursos
    Route::get('auth/admin-cursos', [CursoController::class, 'index'])->name('admin.cursos');
    Route::get('/admin/show/{id}', [CursoController::class, 'showCurso'])->name('admin.cursos.show');
    //ACCIONES DE DADO DE BAJA
    Route::delete('admin/P/{id}/{accion}', [UsersController::class, 'gestionarEstadoPersonal'])->name('admin.P.gestionarEstado');
    Route::delete('admin/D/{id}/{accion}', [UsersController::class, 'gestionarEstadoDocente'])->name('admin.D.gestionarEstado');
    Route::delete('admin/E/{id}/{accion}', [UsersController::class, 'gestionarEstadoEstudiante'])->name('admin.E.gestionarEstado');
    
//***PROBAR TODOS PARA ABAJO */
    Route::get('/admin-dashboard', [AdminController::class, 'index'])->name('admin.home');
    /*Route::get('/gestionar/permisos/admin', GestionPermisos::class)->name('admin.gestion.permisos');
    Route::get('/backup', [BackupController::class, 'downloadBackup'])->name('admin.backup.db_igla');*/
    
    Route::get('/admin-inscripcions', [UsersController::class, 'formInscripcion'])->name('admin.inscripcion');
    Route::post('/admin-inscripcions/store', [UsersController::class, 'inscripcion'])->name('admin.inscripcion.store');
    Route::put('/create-student-{id}-update', [UsersController::class, 'update'])->name('update.estudiantes');
    //Personal de la institucion
    //Calendario
    Route::get('/calendar', [CalendarioController::class, 'index'])->name('admin.calendario');
    Route::post('/calendar/store', [CalendarioController::class, 'store'])->name('admin.calendario.store');
    Route::post('/calendar/{id}/evento/edit', [CalendarioController::class, 'edit'])->name('admin.calendario.edit');
    Route::post('/calendar/{id}/evento/update', [CalendarioController::class, 'update'])->name('admin.calendario.update');
    Route::post('/calendar/{id}/evento/delete', [CalendarioController::class, 'delete'])->name('admin.calendario.delete');
    Route::get('/calendar/{id}/evento/show', [CalendarioController::class, 'show'])->name('admin.calendario.show');
    //Cursos
    
    Route::post('/curso-info', [CursoController::class, 'guardarCurso'])->name('admin.guardar-curso');
    Route::put('/curso-info/{id}/edit', [CursoController::class, 'actualizarCurso'])->name('admin.actualizar-curso');
    Route::delete('admin/materia/{id}/baja', [CursoController::class, 'darBajaCurso'])->name('admin.cursos.darBaja');
    Route::delete('admin/materia/{id}/alta', [CursoController::class, 'darAltaCurso'])->name('admin.cursos.darAlta');
    Route::get('admin/materia/{id}/delete', [CursoController::class, 'deleteCurso'])->name('admin.cursos.delete');
    Route::get('/asignando-curso/{id}', [CursoController::class, 'asignarCurso'])->name('admin.asignar.curso');
    Route::get('/cursos-curso/meshgv', [CursoController::class, 'cursosActivos'])->name('admin.cursos.activos');
    Route::post('/curso-info/asignar', [CursoController::class, 'asignarGuardarCurso'])->name('admin.asignar.guardar.curso');
    Route::put('/curso-info/{id}/edit/asignar', [CursoController::class, 'asignarActualizarCurso'])->name('admin.asignar.actualizar-curso');
    Route::get('/asignados/cursos/{id}/edit', [CursoController::class, 'editCursoAsignado'])->name('admin.asigando.edit');
    Route::post('/asignados/cambiar/{id}', [CursoController::class, 'gestionarEstadoCurso'])->name('admin.cursos.cambiarEstado');
    Route::get('/borrar/cambiar-estado/{id}', [CursoController::class, 'deleteCursoActivo'])->name('admin.borrar.curso.activo');
    Route::get('/ruta/del/server/para/obtener/disponibilidad', [CursoController::class, 'obtenerDisponibilidad']);
    Route::get('/ruta/al/servidor/para/obtener/cursos', [CursoController::class, 'obtenerCursosAnteriores']);
    ///pagos
    /*Route::get('/admin-pagos-all', [PagosController::class, 'allPagos'])->name('admin.lista.pagos');
    Route::get('/pagos/formulario/hjfse', [PagosController::class, 'formPagos'])->name('admin.create.pago');
    Route::post('/pagos/store', [PagosController::class, 'storePagosSimples'])->name('admin.store.pago');
    Route::get('/pagos/guadar/imprimir/{id}', [PagosController::class, 'guardarImprimirPago'])->name('admin.pago.guardar.imprimir');
    Route::get('/pagos/habilitar/mes', [PagosController::class, 'habilitarPagosMes'])->name('admin.habiltar.pagos.mes');
    //Cocina
    //Acerda de IGLA
    Route::get('/informacion', [HomeController::class, 'acercaDe'])->name('admin.ajustes');
    //Administracion de informacion
    Route::get('/administrar-info', AdminInfo::class)->name('admin.administracion');
    //Evaluacion docente
    Route::get('/evaluacion/add/docente', EvaluacionDocente::class)->name('evaluacion.docente');
    Route::get('/evaluacion/listado/docente', MateriaEvaluacionDocente::class)->name('materia.evaluacion.docente');
    Route::get('/evaluacion/historial/docente', HistorialEvaluacionDocente::class)->name('historial.evaluacion.docente');
    //Rutas para exportar
    Route::get('/cursos/exp/pdf', [CursoController::class, 'exportarCurso'])->name('export.cursos');
    //Rutasp para reportes
    Route::get('/estudiantes/reporte/export', EstudianteReportes::class)->name('admin.estudiantes.informe');
    Route::get('/asistencias/reporte/export', AsistenciaReportes::class)->name('admin.asistencias.informe');
    Route::get('/materias/reporte/export', MateriaReportes::class)->name('admin.materias.informe');
    Route::get('/pagos/reporte/export', PagosReportes::class)->name('admin.pagos.informe');
    ///Gestion Inventario
    Route::get('/lista/inventario/ingredientes', [CocinaController::class, 'inventarioIndex'])->name('admin.gestion.inventario');
    Route::get('/inventario/ingrediente/form', [CocinaController::class, 'createForm'])->name('admin.gestion.inventario.form');
    Route::get('/inventario/ingrediente/edit/{id}', [CocinaController::class, 'editForm'])->name('admin.gestion.inventario.edit');
    Route::post('/inventario/store/form', [CocinaController::class, 'guardarInventario'])->name('admin.gestion.inventario.store');
    Route::put('/inventario/update/form/{id}', [CocinaController::class, 'updateInventario'])->name('admin.gestion.inventario.update');
    Route::delete('/inventario/deba/{id}', [CocinaController::class, 'darBajaInvetario'])->name('admin.gestion.inventario.estado');
    Route::get('/inventario/borrar/{id}', [CocinaController::class, 'eliminarInvetario'])->name('admin.gestion.inventario.borrar');
    Route::post('/cantidad/update/{id}', [CocinaController::class, 'updateCantidad'])->name('admin.inventario.update.cantidad');
    Route::get('/historial/inventario', HistorialInventario::class)->name('admin.inventario.historial');
*/
});

/*
Route::middleware(['auth', 'role:Docente'])->group(function () {
    Route::get('/chef-dashboard', [DocenteController::class, 'index'])->name('docente.home');
    //Route::get('/trabajo/nueva/post/{id}', NewTarea::class)->name('nueva.tarea.docente');
    Route::get('/trabajo/nueva/post/{id}', [DocenteCursoController::class, 'createTareaNew'])->name('nueva.tarea.docente');
    Route::get('/trabajo/editar/post/{id}', [DocenteCursoController::class, 'editarTareaEdit'])->name('editra.trabajo.docente');
    Route::post('/trabajo/tarea/new', [DocenteCursoController::class, 'crearTarea'])->name('guardar.tarea.new');
    
    Route::get('/trabajo/file/delete/{id}/file', [DocenteCursoController::class, 'borrarFile'])->name('docente.borrar.file');

    Route::put('/trabajo/tarea/edit/{id}', [DocenteCursoController::class, 'updateTrabajo'])->name('docente.update.trabajo');
    Route::get('/editar/tema/{id}', [DocenteCursoController::class, 'viewTemeEdit'])->name('docente.edit.tema');
    Route::put('/editar/tema/{id}/update', [DocenteCursoController::class, 'updateTema'])->name('docente.update.tema');

    Route::get('/calificando/tarea/{id}', CalificarTarea::class)->name('calificar.tarea.estudiante');
    Route::post('/planificacion/curso/{id}', [DocenteController::class, 'planificacion'])->name('guardar.planificacion');
    Route::get('/criterios/tareas/{id}/eval', CriteriosTrabajos::class)->name('docente.tareas.criterios');
    Route::post('/selectReceta',[DocenteCursoController::class, 'selectReceta'])->name('search.recetas');
});
Route::middleware(['auth', 'role:Estudiante'])->group(function () {
    Route::get('/calendar/mostrar/trabajos', [CalendarioController::class, 'mostrarTrabajos'])->name('admin.calendario.trabajos');
    Route::get('/estud-dashboard', [EstudianteController::class, 'index'])->name('estudiante.home');
    Route::get('/cursos/carrera/bamos', [EstudianteController::class, 'cursos'])->name('cursos.carrera');
    Route::get('/estud-submit/{id}/{edit}/editar', SubirTarea::class)->name('estudiante.subir.tarea');
    Route::get('/calificaiones', [EstudianteController::class, 'calificaionesMaterias'])->name('estudiante.calificaciones');
});

Route::middleware(['auth'])->group(function () {
    //calendario
    Route::get('/calendar/mostrar', [CalendarioController::class, 'mostrar'])->name('admin.calendario.ver');
    Route::get('/calendar/inicio/fin', [CalendarioController::class, 'mostrarInicioFin'])->name('admin.calendario.ver.curso.asignar');
    //cocina Ingredientes
    Route::get('/ingretientes-all', [CocinaController::class, 'allIngredientes'])->name('admin.ingredientes');
    Route::get('/recetas-all/dsgsa', [CocinaController::class, 'allrecetas'])->name('admin.recetas');
    Route::get('/recetas-show/{id}', [CocinaController::class, 'showReceta'])->name('admin.show.receta');
    Route::delete('/recetas-eliminar/{id}', [CocinaController::class, 'deleteReceta'])->name('admin.receta.eliminar');
    Route::post('/select',[CocinaController::class, 'selectIngredientes'])->name('search.ingredientes');
    Route::post('/admin/buscar-ingredientes', [CocinaController::class, 'buscarIngredientes'])->name('admin.buscar-ingredientes');
    Route::get('/agregar-receta/nueva', NewReceta::class)->name('recetas.add');
    Route::get('/profile', ProfilePage::class)->name('users.profile');
    Route::post('/new-type/ingrediente', [CocinaController::class, 'guardarIngrediente'])->name('new.ingrediente.db');
    Route::post('/new/recipe/generate', [CocinaController::class, 'generarRecetaOpenAI'])->name('new.receta.generation');
    //Cursos
    Route::get('/cursos', [DocenteCursoController::class, 'index'])->name('chef.cursos');
    Route::get('/curso/{id}/materia', [DocenteCursoController::class, 'curso'])->name('cursos.curso');
    //Componetes
    Route::get('/posts-tareas/{id}', ShowTarea::class)->name('show.tarea');
    Route::post('/store/evaluacion/jajaja', [EstudianteController::class, 'evaluacionDocente'])->name('store.evaluacion.docente');
    //notificaciones
    Route::get('/send-whatsapp', [InfoController::class, 'sendWhatsAppMessage']);

    Route::get('/suma', [CocinaController::class, 'suma']);

    Route::get('/generar-receta/add', [RecetaController::class, 'listRecetasGeneradas'])->name('receta.generadas.list');

});
 */