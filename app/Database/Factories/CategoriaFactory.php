<?php

namespace App\Database\Factories;

use App\Models\CategoriaModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\Fabricator;

class CategoriaFactory extends Fabricator
{
    protected $model = CategoriaModel::class;

    public function getDefinition(): array
    {
        return [
            'sigla' => $this->faker->randomElement(['A1', 'A2', 'A', 'B1', 'B2', 'C1', 'C2', 'C3', 'D1', 'D2', 'D3', 'E1', 'E2', 'E3']),
            'nombre' => $this->faker->sentence(3),
            'descripcion' => $this->faker->paragraph(2),
            'requisitos' => $this->faker->paragraph(3),
            'edad_minima' => $this->faker->numberBetween(18, 21),
            'experiencia_requerida' => $this->faker->numberBetween(0, 2),
        ];
    }

    // Métodos específicos para cada categoría
    public function categoriaA1()
    {
        return $this->setDefinition([
            'sigla' => 'A1',
            'nombre' => 'Motocicletas hasta 125cc',
            'descripcion' => 'Licencia para conducir motocicletas y similares con cilindrada hasta 125cc.',
            'requisitos' => "1. Tener 18 años cumplidos.\n2. Aprobar examen teórico.\n3. Aprobar examen práctico.\n4. No tener impedimentos físicos o mentales.",
            'edad_minima' => 18,
            'experiencia_requerida' => 0
        ]);
    }

    public function categoriaA2()
    {
        return $this->setDefinition([
            'sigla' => 'A2',
            'nombre' => 'Motocicletas hasta 35kW',
            'descripcion' => 'Licencia para conducir motocicletas y similares con potencia hasta 35kW.',
            'requisitos' => "1. Tener 18 años cumplidos.\n2. Poseer licencia A1 por al menos 2 años.\n3. Aprobar examen teórico.\n4. Aprobar examen práctico.",
            'edad_minima' => 18,
            'experiencia_requerida' => 2
        ]);
    }

    public function categoriaA()
    {
        return $this->setDefinition([
            'sigla' => 'A',
            'nombre' => 'Motocicletas sin restricción',
            'descripcion' => 'Licencia para conducir todo tipo de motocicletas sin restricción de potencia.',
            'requisitos' => "1. Tener 20 años cumplidos.\n2. Poseer licencia A2 por al menos 2 años.\n3. Aprobar examen teórico.\n4. Aprobar examen práctico.",
            'edad_minima' => 20,
            'experiencia_requerida' => 2
        ]);
    }

    public function categoriaB1()
    {
        return $this->setDefinition([
            'sigla' => 'B1',
            'nombre' => 'Automóviles particulares',
            'descripcion' => 'Licencia para conducir automóviles y camionetas de uso particular.',
            'requisitos' => "1. Tener 18 años cumplidos.\n2. Aprobar examen teórico.\n3. Aprobar examen práctico.\n4. No tener impedimentos físicos o mentales.",
            'edad_minima' => 18,
            'experiencia_requerida' => 0
        ]);
    }

    public function categoriaB2()
    {
        return $this->setDefinition([
            'sigla' => 'B2',
            'nombre' => 'Automóviles de servicio público',
            'descripcion' => 'Licencia para conducir vehículos de servicio público como taxis.',
            'requisitos' => "1. Tener 21 años cumplidos.\n2. Poseer licencia B1 por al menos 2 años.\n3. Aprobar examen teórico.\n4. Aprobar examen práctico.\n5. Certificado de antecedentes penales.",
            'edad_minima' => 21,
            'experiencia_requerida' => 2
        ]);
    }

    public function categoriaC1()
    {
        return $this->setDefinition([
            'sigla' => 'C1',
            'nombre' => 'Camiones ligeros',
            'descripcion' => 'Licencia para conducir camiones ligeros de hasta 3.5 toneladas.',
            'requisitos' => "1. Tener 21 años cumplidos.\n2. Poseer licencia B1 por al menos 1 año.\n3. Aprobar examen teórico.\n4. Aprobar examen práctico.",
            'edad_minima' => 21,
            'experiencia_requerida' => 1
        ]);
    }

    public function categoriaC2()
    {
        return $this->setDefinition([
            'sigla' => 'C2',
            'nombre' => 'Camiones pesados',
            'descripcion' => 'Licencia para conducir camiones rígidos de más de 3.5 toneladas.',
            'requisitos' => "1. Tener 21 años cumplidos.\n2. Poseer licencia C1 por al menos 1 año.\n3. Aprobar examen teórico.\n4. Aprobar examen práctico.",
            'edad_minima' => 21,
            'experiencia_requerida' => 1
        ]);
    }

    public function categoriaC3()
    {
        return $this->setDefinition([
            'sigla' => 'C3',
            'nombre' => 'Camiones articulados',
            'descripcion' => 'Licencia para conducir camiones con remolque o articulados.',
            'requisitos' => "1. Tener 21 años cumplidos.\n2. Poseer licencia C2 por al menos 1 año.\n3. Aprobar examen teórico.\n4. Aprobar examen práctico.",
            'edad_minima' => 21,
            'experiencia_requerida' => 1
        ]);
    }

    public function categoriaD1()
    {
        return $this->setDefinition([
            'sigla' => 'D1',
            'nombre' => 'Microbuses',
            'descripcion' => 'Licencia para conducir microbuses de hasta 16 pasajeros.',
            'requisitos' => "1. Tener 21 años cumplidos.\n2. Poseer licencia B2 por al menos 2 años.\n3. Aprobar examen teórico.\n4. Aprobar examen práctico.\n5. Certificado de antecedentes penales.",
            'edad_minima' => 21,
            'experiencia_requerida' => 2
        ]);
    }

    public function categoriaD2()
    {
        return $this->setDefinition([
            'sigla' => 'D2',
            'nombre' => 'Buses',
            'descripcion' => 'Licencia para conducir buses de más de 16 pasajeros.',
            'requisitos' => "1. Tener 23 años cumplidos.\n2. Poseer licencia D1 por al menos 2 años.\n3. Aprobar examen teórico.\n4. Aprobar examen práctico.\n5. Certificado de antecedentes penales.",
            'edad_minima' => 23,
            'experiencia_requerida' => 2
        ]);
    }

    public function categoriaD3()
    {
        return $this->setDefinition([
            'sigla' => 'D3',
            'nombre' => 'Buses articulados',
            'descripcion' => 'Licencia para conducir buses articulados.',
            'requisitos' => "1. Tener 23 años cumplidos.\n2. Poseer licencia D2 por al menos 2 años.\n3. Aprobar examen teórico.\n4. Aprobar examen práctico.\n5. Certificado de antecedentes penales.",
            'edad_minima' => 23,
            'experiencia_requerida' => 2
        ]);
    }

    public function categoriaE1()
    {
        return $this->setDefinition([
            'sigla' => 'E1',
            'nombre' => 'Vehículos con Remolque',
            'descripcion' => 'Licencia para conducir vehículos de clase C o D con remolque.',
            'requisitos' => "1. Tener 21 años cumplidos.\n2. Poseer licencia clase C o D.\n3. Aprobar examen teórico específico.\n4. Aprobar examen práctico.\n5. Certificado de aptitud física.",
            'edad_minima' => 21,
            'experiencia_requerida' => 1
        ]);
    }

    public function categoriaE2()
    {
        return $this->setDefinition([
            'sigla' => 'E2',
            'nombre' => 'Vehículos de emergencia',
            'descripcion' => 'Licencia para conducir ambulancias, vehículos de bomberos y policía.',
            'requisitos' => "1. Tener 21 años cumplidos.\n2. Poseer licencia B1 por al menos 2 años.\n3. Aprobar examen teórico.\n4. Aprobar examen práctico.\n5. Certificado de primeros auxilios.\n6. Certificado de antecedentes penales.",
            'edad_minima' => 21,
            'experiencia_requerida' => 2
        ]);
    }

    public function categoriaE3()
    {
        return $this->setDefinition([
            'sigla' => 'E3',
            'nombre' => 'Vehículos de transporte especial',
            'descripcion' => 'Licencia para conducir vehículos de transporte de materiales peligrosos y especiales.',
            'requisitos' => "1. Tener 23 años cumplidos.\n2. Poseer licencia C2 o D2 por al menos 2 años.\n3. Aprobar examen teórico específico.\n4. Aprobar examen práctico.\n5. Certificado de manejo de materiales peligrosos.\n6. Certificado de antecedentes penales.",
            'edad_minima' => 23,
            'experiencia_requerida' => 2
        ]);
    }
} 