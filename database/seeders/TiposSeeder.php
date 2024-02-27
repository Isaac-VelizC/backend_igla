<?php

namespace Database\Seeders;

use App\Models\Aula;
use App\Models\Curso;
use App\Models\FormaPago;
use App\Models\Horario;
use App\Models\MetodoPago;
use App\Models\Semestre;
use App\Models\TipoEvento;
use App\Models\TipoIngrediente;
use Illuminate\Database\Seeder;

class TiposSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Horario::create(['turno' => 'Mañana', 'inicio' => '08:00', 'fin' => '12:00']);
        Horario::create(['turno' => 'Tarde', 'inicio' => '14:00', 'fin' => '18:00']);
        Horario::create(['turno' => 'Noche', 'inicio' => '19:00', 'fin' => '22:30']);
        Horario::create(['turno' => 'Sabado', 'inicio' => '08:00', 'fin' => '12:00']);

        Aula::create(['nombre' => 'Aula de Cocina 101', 'codigo' => 'GA101', 'capacidad' => 25]);
        Aula::create(['nombre' => 'Aula de Pastelería 201', 'codigo' => 'GP201', 'capacidad' => 20]);
        Aula::create(['nombre' => 'Laboratorio de Enología 301', 'codigo' => 'GE301', 'capacidad' => 30]);
        Aula::create(['nombre' => 'Sala de Degustación 401', 'codigo' => 'GD401', 'capacidad' => 15]);
        Aula::create(['nombre' => 'Aula de Panadería 501', 'codigo' => 'GP501', 'capacidad' => 25]);
        
        Semestre::create(['nombre' => 'Primer Semestre', 'descripcion' => 'Modalidad Regular']);
        Semestre::create(['nombre' => 'Segundo Semestre', 'descripcion' => 'Modalidad Intensiva']);
        Semestre::create(['nombre' => 'Tercer Semestre', 'descripcion' => 'Modalidad Nocturna']);
        Semestre::create(['nombre' => 'Cuarto Semestre', 'descripcion' => 'Modalidad Fin de Semana']);

        // Lista de materias de gastronomía
        $materias = [
            'Computación',
            'Dietética y alimentación',
            'Pastelería',
            'Ingles',
            'Cocina',
            'Cócteleria',
            'Dietética y alimentación II',
            'Pastelería II',
            'Ingles II',
            'Cocina II',
        ];
        // Itera para crear registros de cursos con materias reales
        foreach ($materias as $materia) {
            Curso::create([
                'nombre' => $materia,
                'semestre_id' => rand(1, 2),
                'color' => '#ff0000',
            ]);
        }

        $tiposIngredientes = [
            'Frutas',
            'Verduras',
            'Carnes',
            'Pescados y Mariscos',
            'Lácteos',
            'Cereales',
            'Legumbres',
            'Especias',
            'Aceites y Grasas',
            'Frutos Secos',
            'Condimentos',
            'Azúcares y Endulzantes',
            'Bebidas',
            'Otros',
        ];
        
        // Itera para crear registros de tipos de ingredientes con nombres reales
        foreach ($tiposIngredientes as $tipoIngrediente) {
            TipoIngrediente::create([
                'nombre' => $tipoIngrediente,
            ]);
        }
        
        MetodoPago::create(['nombre' => 'Cuotas', 'monto' => 450]);
        MetodoPago::create(['nombre' => 'Total', 'monto' => 16200]);
        
        FormaPago::create(['nombre' => 'Transferencia Bancaria']);
        FormaPago::create(['nombre' => 'Pago en Efectivo']);
        FormaPago::create(['nombre' => 'Pagos a Través de Aplicaciones Móviles']);

        TipoEvento::create(['nombre' => 'Clases Regulares']);
        TipoEvento::create(['nombre' => 'Límite para Inscripciones y Pago']);
        TipoEvento::create(['nombre' => 'Límites de Pago']);
        TipoEvento::create(['nombre' => 'Inicio y Final de Períodos']);
        TipoEvento::create(['nombre' => 'Festivales y Eventos Especiales']);
    }
}
