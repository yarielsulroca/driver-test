<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CategoriasArgentinasSeeder extends Seeder
{
    public function run()
    {
        $categorias = [
            [
                'codigo' => 'A1',
                'nombre' => 'Ciclomotor',
                'descripcion' => 'Vehículos de dos ruedas con motor de hasta 50cc o hasta 4kW de potencia',
                'requisitos' => json_encode([
                    'Edad mínima: 16 años',
                    'Documentación: DNI',
                    'Examen teórico obligatorio',
                    'Examen práctico obligatorio',
                    'Sin licencia previa requerida'
                ]),
                'estado' => 'activo'
            ],
            [
                'codigo' => 'A2',
                'nombre' => 'Motocicleta',
                'descripcion' => 'Vehículos de dos ruedas con motor de más de 50cc hasta 300cc',
                'requisitos' => json_encode([
                    'Edad mínima: 18 años',
                    'Documentación: DNI',
                    'Examen teórico obligatorio',
                    'Examen práctico obligatorio',
                    'Sin licencia previa requerida'
                ]),
                'estado' => 'activo'
            ],
            [
                'codigo' => 'A3',
                'nombre' => 'Motocicleta Avanzada',
                'descripcion' => 'Vehículos de dos ruedas con motor de más de 300cc',
                'requisitos' => json_encode([
                    'Edad mínima: 21 años',
                    'Documentación: DNI',
                    'Examen teórico obligatorio',
                    'Examen práctico obligatorio',
                    'Licencia previa: A2 con al menos 2 años de antigüedad'
                ]),
                'estado' => 'activo'
            ],
            [
                'codigo' => 'B1',
                'nombre' => 'Automóvil',
                'descripcion' => 'Vehículos de hasta 3500kg de peso máximo, hasta 9 asientos incluido el conductor',
                'requisitos' => json_encode([
                    'Edad mínima: 18 años',
                    'Documentación: DNI',
                    'Examen teórico obligatorio',
                    'Examen práctico obligatorio',
                    'Sin licencia previa requerida'
                ]),
                'estado' => 'activo'
            ],
            [
                'codigo' => 'B2',
                'nombre' => 'Automóvil con Acoplado',
                'descripcion' => 'Vehículos de hasta 3500kg con acoplado de hasta 750kg',
                'requisitos' => json_encode([
                    'Edad mínima: 18 años',
                    'Documentación: DNI',
                    'Examen teórico obligatorio',
                    'Examen práctico obligatorio',
                    'Licencia previa: B1 con al menos 1 año de antigüedad'
                ]),
                'estado' => 'activo'
            ],
            [
                'codigo' => 'C1',
                'nombre' => 'Camión Liviano',
                'descripcion' => 'Vehículos de más de 3500kg hasta 6000kg de peso máximo',
                'requisitos' => json_encode([
                    'Edad mínima: 21 años',
                    'Documentación: DNI',
                    'Examen teórico obligatorio',
                    'Examen práctico obligatorio',
                    'Licencia previa: B1 con al menos 2 años de antigüedad',
                    'Certificado médico específico'
                ]),
                'estado' => 'activo'
            ],
            [
                'codigo' => 'C2',
                'nombre' => 'Camión Mediano',
                'descripcion' => 'Vehículos de más de 6000kg hasta 15000kg de peso máximo',
                'requisitos' => json_encode([
                    'Edad mínima: 21 años',
                    'Documentación: DNI',
                    'Examen teórico obligatorio',
                    'Examen práctico obligatorio',
                    'Licencia previa: C1 con al menos 1 año de antigüedad',
                    'Certificado médico específico'
                ]),
                'estado' => 'activo'
            ],
            [
                'codigo' => 'C3',
                'nombre' => 'Camión Pesado',
                'descripcion' => 'Vehículos de más de 15000kg de peso máximo',
                'requisitos' => json_encode([
                    'Edad mínima: 21 años',
                    'Documentación: DNI',
                    'Examen teórico obligatorio',
                    'Examen práctico obligatorio',
                    'Licencia previa: C2 con al menos 1 año de antigüedad',
                    'Certificado médico específico'
                ]),
                'estado' => 'activo'
            ],
            [
                'codigo' => 'D1',
                'nombre' => 'Microbús',
                'descripcion' => 'Vehículos para transporte de pasajeros de hasta 20 asientos',
                'requisitos' => json_encode([
                    'Edad mínima: 21 años',
                    'Documentación: DNI',
                    'Examen teórico obligatorio',
                    'Examen práctico obligatorio',
                    'Licencia previa: B1 con al menos 2 años de antigüedad',
                    'Certificado médico específico',
                    'Certificado de antecedentes penales'
                ]),
                'estado' => 'activo'
            ],
            [
                'codigo' => 'D2',
                'nombre' => 'Ómnibus',
                'descripcion' => 'Vehículos para transporte de pasajeros de más de 20 asientos',
                'requisitos' => json_encode([
                    'Edad mínima: 21 años',
                    'Documentación: DNI',
                    'Examen teórico obligatorio',
                    'Examen práctico obligatorio',
                    'Licencia previa: D1 con al menos 1 año de antigüedad',
                    'Certificado médico específico',
                    'Certificado de antecedentes penales'
                ]),
                'estado' => 'activo'
            ],
            [
                'codigo' => 'E1',
                'nombre' => 'Casa Rodante',
                'descripcion' => 'Vehículos de hasta 3500kg con casa rodante',
                'requisitos' => json_encode([
                    'Edad mínima: 21 años',
                    'Documentación: DNI',
                    'Examen teórico obligatorio',
                    'Examen práctico obligatorio',
                    'Licencia previa: B1 con al menos 2 años de antigüedad'
                ]),
                'estado' => 'activo'
            ],
            [
                'codigo' => 'E2',
                'nombre' => 'Casa Rodante Pesada',
                'descripcion' => 'Vehículos de más de 3500kg con casa rodante',
                'requisitos' => json_encode([
                    'Edad mínima: 21 años',
                    'Documentación: DNI',
                    'Examen teórico obligatorio',
                    'Examen práctico obligatorio',
                    'Licencia previa: C1 con al menos 1 año de antigüedad'
                ]),
                'estado' => 'activo'
            ],
            [
                'codigo' => 'F',
                'nombre' => 'Discapacitados',
                'descripcion' => 'Vehículos adaptados para personas con discapacidad',
                'requisitos' => json_encode([
                    'Edad mínima: 18 años',
                    'Documentación: DNI',
                    'Certificado de discapacidad',
                    'Examen teórico obligatorio',
                    'Examen práctico obligatorio',
                    'Evaluación médica específica'
                ]),
                'estado' => 'activo'
            ],
            [
                'codigo' => 'G',
                'nombre' => 'Maquinaria Agrícola',
                'descripcion' => 'Tractores y maquinaria agrícola',
                'requisitos' => json_encode([
                    'Edad mínima: 18 años',
                    'Documentación: DNI',
                    'Examen teórico obligatorio',
                    'Examen práctico obligatorio',
                    'Sin licencia previa requerida'
                ]),
                'estado' => 'activo'
            ],
            [
                'codigo' => 'H',
                'nombre' => 'Maquinaria Vial',
                'descripcion' => 'Excavadoras, palas mecánicas y maquinaria vial',
                'requisitos' => json_encode([
                    'Edad mínima: 21 años',
                    'Documentación: DNI',
                    'Examen teórico obligatorio',
                    'Examen práctico obligatorio',
                    'Licencia previa: B1 con al menos 1 año de antigüedad'
                ]),
                'estado' => 'activo'
            ]
        ];

        // Insertar categorías
        foreach ($categorias as $categoria) {
            $this->db->table('categorias')->insert($categoria);
        }

        echo "✅ Categorías argentinas insertadas exitosamente\n";
        echo "📊 Total de categorías creadas: " . count($categorias) . "\n";
    }
} 