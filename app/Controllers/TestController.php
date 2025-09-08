<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\API\ResponseTrait;

class TestController extends Controller
{
    use ResponseTrait;

    public function testExamen()
    {
        try {
            // Conectar a la base de datos
            $db = \Config\Database::connect();
            
            // Verificar conexión
            if (!$db->connect()) {
                return $this->failServerError('Error de conexión a la base de datos');
            }
            
            // Verificar que la tabla examenes existe
            $tables = $db->listTables();
            if (!in_array('examenes', $tables)) {
                return $this->failServerError('La tabla examenes no existe');
            }
            
            // Verificar que la tabla examen_categoria existe
            if (!in_array('examen_categoria', $tables)) {
                return $this->failServerError('La tabla examen_categoria no existe');
            }
            
            // Verificar estructura de la tabla examenes
            $fields = $db->getFieldNames('examenes');
            $requiredFields = ['examen_id', 'titulo', 'nombre', 'tiempo_limite', 'duracion_minutos', 'puntaje_minimo', 'estado'];
            
            foreach ($requiredFields as $field) {
                if (!in_array($field, $fields)) {
                    return $this->failServerError("El campo {$field} no existe en la tabla examenes");
                }
            }
            
            // Intentar insertar un examen de prueba
            $examenData = [
                'titulo' => 'Test Examen',
                'nombre' => 'Test Examen',
                'descripcion' => 'Examen de prueba',
                'tiempo_limite' => 60,
                'duracion_minutos' => 60,
                'puntaje_minimo' => 70.00,
                'fecha_inicio' => date('Y-m-d H:i:s'),
                'fecha_fin' => date('Y-m-d H:i:s', strtotime('+1 year')),
                'numero_preguntas' => 1,
                'estado' => 'activo',
                'dificultad' => 'medio'
            ];
            
            $result = $db->table('examenes')->insert($examenData);
            
            if (!$result) {
                $error = $db->error();
                return $this->failServerError('Error al insertar examen: ' . json_encode($error));
            }
            
            $examen_id = $db->insertID();
            
            // Intentar insertar en examen_categoria
            $categoriaData = [
                'examen_id' => $examen_id,
                'categoria_id' => 5
            ];
            
            $result2 = $db->table('examen_categoria')->insert($categoriaData);
            
            if (!$result2) {
                $error = $db->error();
                return $this->failServerError('Error al insertar categoría: ' . json_encode($error));
            }
            
            // Limpiar datos de prueba
            $db->table('examen_categoria')->where('examen_id', $examen_id)->delete();
            $db->table('examenes')->where('examen_id', $examen_id)->delete();
            
            return $this->respond([
                'status' => 'success',
                'message' => 'Prueba exitosa - Todas las operaciones funcionan correctamente'
            ]);
            
        } catch (\Exception $e) {
            return $this->failServerError('Error en la prueba: ' . $e->getMessage());
        }
    }
}
