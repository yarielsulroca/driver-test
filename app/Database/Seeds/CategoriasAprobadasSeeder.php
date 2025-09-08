<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CategoriasAprobadasSeeder extends Seeder
{
    public function run()
    {
        // Este seeder se puede usar para crear ejemplos de categorías aprobadas
        // Por ahora solo muestra un mensaje informativo
        
        echo "📋 Seeder de Categorías Aprobadas\n";
        echo "--------------------------------\n";
        echo "Este seeder está preparado para crear registros de ejemplo\n";
        echo "que relacionen conductores con categorías aprobadas.\n\n";
        echo "Para usarlo, primero asegúrate de tener:\n";
        echo "1. Conductores en la tabla 'conductores'\n";
        echo "2. Categorías en la tabla 'categorias'\n";
        echo "3. Exámenes en la tabla 'examenes'\n\n";
        echo "Luego puedes ejecutar este seeder para crear ejemplos.\n";
        
        // Ejemplo de cómo se insertarían las categorías aprobadas:
        /*
        $categoriasAprobadas = [
            [
                'conductor_id' => 1,
                'categoria_id' => 1, // A1 - Motos hasta 150cc
                'examen_id' => 1,
                'estado' => 'aprobado',
                'puntaje_obtenido' => 85.50,
                'fecha_aprobacion' => date('Y-m-d H:i:s'),
                'fecha_vencimiento' => date('Y-m-d H:i:s', strtotime('+5 years')),
                'observaciones' => 'Aprobado en primera instancia'
            ],
            [
                'conductor_id' => 1,
                'categoria_id' => 4, // B1 - Automóviles particulares
                'examen_id' => 2,
                'estado' => 'aprobado',
                'puntaje_obtenido' => 92.00,
                'fecha_aprobacion' => date('Y-m-d H:i:s'),
                'fecha_vencimiento' => date('Y-m-d H:i:s', strtotime('+5 years')),
                'observaciones' => 'Excelente rendimiento en el examen'
            ]
        ];
        
        foreach ($categoriasAprobadas as $categoriaAprobada) {
            $this->db->table('categorias_aprobadas')->insert($categoriaAprobada);
        }
        */
    }
}
