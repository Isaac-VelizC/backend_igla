<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear roles
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'Estudiante']);
        Role::create(['name' => 'Docente']);
        Role::create(['name' => 'Secretario/a']);

        // Crear permisos
        Permission::create(['name' => 'GestionUsers']);
        Permission::create(['name' => 'GestionDocente']);
        Permission::create(['name' => 'GestionEstudiante']);
        Permission::create(['name' => 'Informes']);
        Permission::create(['name' => 'GestionCurso']);
        Permission::create(['name' => 'GestionCalendario']);
        Permission::create(['name' => 'GestionRecetas']);
        Permission::create(['name' => 'EvaluacionDocente']);
        Permission::create(['name' => 'ProgramacionCurso']);
        Permission::create(['name' => 'GestionPagos']);
        Permission::create(['name' => 'GestionInventario']);

        // Asignar permisos a roles
        $adminRole = Role::findByName('Admin');
        $adminRole->givePermissionTo([
            'GestionUsers',
            'GestionDocente',
            'GestionEstudiante',
            'Informes',
            'GestionCurso',
            'GestionCalendario',
            'GestionRecetas',
            'EvaluacionDocente',
            'ProgramacionCurso',
            'GestionPagos',
            'GestionInventario'
        ]);
        $secretaryRole = Role::findByName('Secretario/a');
        $secretaryRole->givePermissionTo([
            'GestionDocente',
            'GestionEstudiante',
            'GestionCurso',
            'GestionCalendario',
            'GestionRecetas',
            'EvaluacionDocente',
            'ProgramacionCurso',
            'GestionPagos',
            'GestionInventario'
        ]);
        // Asignar roles a usuarios
        $userAdmin = User::find(1);
        $userAdmin->assignRole('Admin');
        $userSecretary = User::find(2);
        $userSecretary->assignRole('Secretario/a');
    }
}
