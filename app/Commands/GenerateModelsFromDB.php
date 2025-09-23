<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class GenerateModelsFromDB extends BaseCommand
{
    protected $group = 'Database';
    protected $name = 'generate:models';
    protected $description = 'Genera modelos y controladores basados en la estructura de la base de datos';

    public function run(array $params)
    {
        CLI::write('🔍 Generando modelos y controladores desde la base de datos...', 'yellow');
        
        // Definir las tablas conocidas basándome en las migraciones existentes
        $tables = [
            'usuarios' => [
                'fields' => ['usuario_id', 'dni', 'nombre', 'apellido', 'email', 'password', 'estado', 'created_at', 'updated_at'],
                'primary' => 'usuario_id',
                'foreign_keys' => []
            ],
            'roles' => [
                'fields' => ['rol_id', 'nombre', 'descripcion', 'created_at', 'updated_at'],
                'primary' => 'rol_id',
                'foreign_keys' => []
            ],
            'usuario_roles' => [
                'fields' => ['id', 'usuario_id', 'rol_id', 'created_at', 'updated_at'],
                'primary' => 'id',
                'foreign_keys' => [
                    'usuario_id' => 'usuarios',
                    'rol_id' => 'roles'
                ]
            ],
            'conductores' => [
                'fields' => ['conductor_id', 'usuario_id', 'licencia', 'fecha_vencimiento', 'estado', 'categoria_principal', 'fecha_registro', 'created_at', 'updated_at'],
                'primary' => 'conductor_id',
                'foreign_keys' => ['usuario_id' => 'usuarios']
            ],
            'perfiles' => [
                'fields' => ['perfil_id', 'usuario_id', 'telefono', 'direccion', 'fecha_nacimiento', 'genero', 'foto', 'created_at', 'updated_at'],
                'primary' => 'perfil_id',
                'foreign_keys' => ['usuario_id' => 'usuarios']
            ],
            'categorias' => [
                'fields' => ['categoria_id', 'codigo', 'nombre', 'descripcion', 'requisitos', 'estado', 'created_at', 'updated_at'],
                'primary' => 'categoria_id',
                'foreign_keys' => []
            ],
            'examenes' => [
                'fields' => ['examen_id', 'titulo', 'nombre', 'descripcion', 'duracion_minutos', 'puntaje_minimo', 'estado', 'created_at', 'updated_at'],
                'primary' => 'examen_id',
                'foreign_keys' => []
            ],
            'preguntas' => [
                'fields' => ['pregunta_id', 'examen_id', 'categoria_id', 'enunciado', 'tipo', 'puntaje', 'imagen_url', 'estado', 'created_at', 'updated_at'],
                'primary' => 'pregunta_id',
                'foreign_keys' => ['examen_id' => 'examenes', 'categoria_id' => 'categorias']
            ],
            'respuestas' => [
                'fields' => ['respuesta_id', 'pregunta_id', 'texto', 'es_correcta', 'explicacion', 'imagen_url', 'created_at', 'updated_at'],
                'primary' => 'respuesta_id',
                'foreign_keys' => ['pregunta_id' => 'preguntas']
            ],
            'escuelas' => [
                'fields' => ['escuela_id', 'nombre', 'direccion', 'telefono', 'email', 'ciudad', 'estado', 'created_at', 'updated_at'],
                'primary' => 'escuela_id',
                'foreign_keys' => []
            ],
            'supervisores' => [
                'fields' => ['supervisor_id', 'usuario_id', 'escuela_id', 'estado', 'created_at', 'updated_at'],
                'primary' => 'supervisor_id',
                'foreign_keys' => ['usuario_id' => 'usuarios', 'escuela_id' => 'escuelas']
            ],
            'categorias_aprobadas' => [
                'fields' => ['categoria_aprobada_id', 'conductor_id', 'categoria_id', 'examen_id', 'estado', 'puntaje_obtenido', 'fecha_aprobacion', 'fecha_vencimiento', 'observaciones', 'created_at', 'updated_at'],
                'primary' => 'categoria_aprobada_id',
                'foreign_keys' => ['conductor_id' => 'conductores', 'categoria_id' => 'categorias', 'examen_id' => 'examenes']
            ],
            'examen_escuela' => [
                'fields' => ['id', 'examen_id', 'escuela_id', 'created_at', 'updated_at'],
                'primary' => 'id',
                'foreign_keys' => ['examen_id' => 'examenes', 'escuela_id' => 'escuelas']
            ],
            'conductor_escuela' => [
                'fields' => ['id', 'conductor_id', 'escuela_id', 'fecha_registro', 'estado', 'created_at', 'updated_at'],
                'primary' => 'id',
                'foreign_keys' => ['conductor_id' => 'conductores', 'escuela_id' => 'escuelas']
            ]
        ];

        CLI::write("\n📋 Tablas identificadas:", 'green');
        foreach (array_keys($tables) as $table) {
            CLI::write("  - $table");
        }

        // Generar modelos
        CLI::write("\n🔧 Generando modelos...", 'blue');
        foreach ($tables as $tableName => $tableInfo) {
            $this->generateModel($tableName, $tableInfo);
        }

        // Generar controladores
        CLI::write("\n🎮 Generando controladores...", 'blue');
        foreach ($tables as $tableName => $tableInfo) {
            if (!in_array($tableName, ['usuario_roles', 'examen_escuela', 'conductor_escuela'])) {
                $this->generateController($tableName, $tableInfo);
            }
        }

        CLI::write("\n✅ Generación completada!", 'green');
    }

    private function generateModel($tableName, $tableInfo)
    {
        $modelName = $this->tableToModelName($tableName);
        $modelFile = APPPATH . "Models/{$modelName}.php";
        
        if (file_exists($modelFile)) {
            CLI::write("  - {$modelName}.php ya existe, saltando...", 'yellow');
            return;
        }

        $fields = $tableInfo['fields'];
        $primaryKey = $tableInfo['primary'];
        $foreignKeys = $tableInfo['foreign_keys'];

        // Campos permitidos (excluyendo timestamps y primary key)
        $allowedFields = array_filter($fields, function($field) use ($primaryKey) {
            return !in_array($field, ['created_at', 'updated_at', 'deleted_at']) && $field !== $primaryKey;
        });

        $modelContent = "<?php\n\nnamespace App\\Models;\n\nuse CodeIgniter\\Model;\n\nclass {$modelName} extends Model\n{\n";
        $modelContent .= "    protected \$table = '{$tableName}';\n";
        $modelContent .= "    protected \$primaryKey = '{$primaryKey}';\n";
        $modelContent .= "    protected \$useAutoIncrement = true;\n";
        $modelContent .= "    protected \$returnType = 'array';\n";
        $modelContent .= "    protected \$useSoftDeletes = false;\n";
        $modelContent .= "    protected \$protectFields = true;\n";
        $modelContent .= "    protected \$allowedFields = [\n";
        
        foreach ($allowedFields as $field) {
            $modelContent .= "        '{$field}',\n";
        }
        
        $modelContent .= "    ];\n\n";
        $modelContent .= "    // Dates\n";
        $modelContent .= "    protected \$useTimestamps = true;\n";
        $modelContent .= "    protected \$dateFormat = 'datetime';\n";
        $modelContent .= "    protected \$createdField = 'created_at';\n";
        $modelContent .= "    protected \$updatedField = 'updated_at';\n";
        $modelContent .= "    protected \$deletedField = 'deleted_at';\n\n";

        // Agregar relaciones
        if (!empty($foreignKeys)) {
            $modelContent .= "    // Relaciones\n";
            foreach ($foreignKeys as $fkField => $fkTable) {
                $relatedModel = $this->tableToModelName($fkTable);
                $relationType = $this->getRelationType($tableName, $fkField, $fkTable);
                
                if ($relationType === 'belongsTo') {
                    $modelContent .= "    public function {$this->fieldToMethodName($fkField)}()\n";
                    $modelContent .= "    {\n";
                    $modelContent .= "        return \$this->belongsTo('App\\Models\\{$relatedModel}', '{$fkField}', '{$this->getPrimaryKey($fkTable)}');\n";
                    $modelContent .= "    }\n\n";
                } elseif ($relationType === 'hasOne') {
                    $modelContent .= "    public function {$this->tableToMethodName($fkTable)}()\n";
                    $modelContent .= "    {\n";
                    $modelContent .= "        return \$this->hasOne('App\\Models\\{$relatedModel}', '{$fkField}', '{$primaryKey}');\n";
                    $modelContent .= "    }\n\n";
                }
            }
        }

        $modelContent .= "}\n";

        file_put_contents($modelFile, $modelContent);
        CLI::write("  ✅ {$modelName}.php generado", 'green');
    }

    private function generateController($tableName, $tableInfo)
    {
        $controllerName = $this->tableToControllerName($tableName);
        $controllerFile = APPPATH . "Controllers/{$controllerName}.php";
        
        if (file_exists($controllerFile)) {
            CLI::write("  - {$controllerName}.php ya existe, saltando...", 'yellow');
            return;
        }

        $modelName = $this->tableToModelName($tableName);
        
        $controllerContent = "<?php\n\nnamespace App\\Controllers;\n\nuse App\\Models\\{$modelName};\nuse CodeIgniter\\RESTful\\ResourceController;\nuse CodeIgniter\\API\\ResponseTrait;\n\nclass {$controllerName} extends ResourceController\n{\n";
        $controllerContent .= "    use ResponseTrait;\n\n";
        $controllerContent .= "    protected \$model;\n";
        $controllerContent .= "    protected \$format = 'json';\n\n";
        $controllerContent .= "    public function __construct()\n";
        $controllerContent .= "    {\n";
        $controllerContent .= "        \$this->model = new {$modelName}();\n";
        $controllerContent .= "    }\n\n";
        $controllerContent .= "    /**\n";
        $controllerContent .= "     * Listar todos los registros\n";
        $controllerContent .= "     */\n";
        $controllerContent .= "    public function index()\n";
        $controllerContent .= "    {\n";
        $controllerContent .= "        try {\n";
        $controllerContent .= "            \$data = \$this->model->findAll();\n";
        $controllerContent .= "            return \$this->respond([\n";
        $controllerContent .= "                'status' => 'success',\n";
        $controllerContent .= "                'data' => \$data\n";
        $controllerContent .= "            ]);\n";
        $controllerContent .= "        } catch (\\Exception \$e) {\n";
        $controllerContent .= "            return \$this->failServerError('Error al obtener datos: ' . \$e->getMessage());\n";
        $controllerContent .= "        }\n";
        $controllerContent .= "    }\n\n";
        $controllerContent .= "    /**\n";
        $controllerContent .= "     * Obtener un registro específico\n";
        $controllerContent .= "     */\n";
        $controllerContent .= "    public function show(\$id = null)\n";
        $controllerContent .= "    {\n";
        $controllerContent .= "        try {\n";
        $controllerContent .= "            \$data = \$this->model->find(\$id);\n";
        $controllerContent .= "            if (!\$data) {\n";
        $controllerContent .= "                return \$this->failNotFound('Registro no encontrado');\n";
        $controllerContent .= "            }\n";
        $controllerContent .= "            return \$this->respond([\n";
        $controllerContent .= "                'status' => 'success',\n";
        $controllerContent .= "                'data' => \$data\n";
        $controllerContent .= "            ]);\n";
        $controllerContent .= "        } catch (\\Exception \$e) {\n";
        $controllerContent .= "            return \$this->failServerError('Error al obtener registro: ' . \$e->getMessage());\n";
        $controllerContent .= "        }\n";
        $controllerContent .= "    }\n\n";
        $controllerContent .= "    /**\n";
        $controllerContent .= "     * Crear un nuevo registro\n";
        $controllerContent .= "     */\n";
        $controllerContent .= "    public function create()\n";
        $controllerContent .= "    {\n";
        $controllerContent .= "        try {\n";
        $controllerContent .= "            \$data = \$this->request->getJSON(true);\n";
        $controllerContent .= "            if (!\$data) {\n";
        $controllerContent .= "                return \$this->fail('No se recibieron datos válidos');\n";
        $controllerContent .= "            }\n\n";
        $controllerContent .= "            if (!\$this->model->insert(\$data)) {\n";
        $controllerContent .= "                return \$this->failValidationError('Error de validación', \$this->model->errors());\n";
        $controllerContent .= "            }\n\n";
        $controllerContent .= "            \$id = \$this->model->getInsertID();\n";
        $controllerContent .= "            \$data = \$this->model->find(\$id);\n\n";
        $controllerContent .= "            return \$this->respondCreated([\n";
        $controllerContent .= "                'status' => 'success',\n";
        $controllerContent .= "                'message' => 'Registro creado exitosamente',\n";
        $controllerContent .= "                'data' => \$data\n";
        $controllerContent .= "            ]);\n";
        $controllerContent .= "        } catch (\\Exception \$e) {\n";
        $controllerContent .= "            return \$this->failServerError('Error al crear registro: ' . \$e->getMessage());\n";
        $controllerContent .= "        }\n";
        $controllerContent .= "    }\n\n";
        $controllerContent .= "    /**\n";
        $controllerContent .= "     * Actualizar un registro\n";
        $controllerContent .= "     */\n";
        $controllerContent .= "    public function update(\$id = null)\n";
        $controllerContent .= "    {\n";
        $controllerContent .= "        try {\n";
        $controllerContent .= "            \$data = \$this->request->getJSON(true);\n";
        $controllerContent .= "            if (!\$data) {\n";
        $controllerContent .= "                return \$this->fail('No se recibieron datos válidos');\n";
        $controllerContent .= "            }\n\n";
        $controllerContent .= "            if (!\$this->model->find(\$id)) {\n";
        $controllerContent .= "                return \$this->failNotFound('Registro no encontrado');\n";
        $controllerContent .= "            }\n\n";
        $controllerContent .= "            if (!\$this->model->update(\$id, \$data)) {\n";
        $controllerContent .= "                return \$this->failValidationError('Error de validación', \$this->model->errors());\n";
        $controllerContent .= "            }\n\n";
        $controllerContent .= "            \$data = \$this->model->find(\$id);\n\n";
        $controllerContent .= "            return \$this->respond([\n";
        $controllerContent .= "                'status' => 'success',\n";
        $controllerContent .= "                'message' => 'Registro actualizado exitosamente',\n";
        $controllerContent .= "                'data' => \$data\n";
        $controllerContent .= "            ]);\n";
        $controllerContent .= "        } catch (\\Exception \$e) {\n";
        $controllerContent .= "            return \$this->failServerError('Error al actualizar registro: ' . \$e->getMessage());\n";
        $controllerContent .= "        }\n";
        $controllerContent .= "    }\n\n";
        $controllerContent .= "    /**\n";
        $controllerContent .= "     * Eliminar un registro\n";
        $controllerContent .= "     */\n";
        $controllerContent .= "    public function delete(\$id = null)\n";
        $controllerContent .= "    {\n";
        $controllerContent .= "        try {\n";
        $controllerContent .= "            if (!\$this->model->find(\$id)) {\n";
        $controllerContent .= "                return \$this->failNotFound('Registro no encontrado');\n";
        $controllerContent .= "            }\n\n";
        $controllerContent .= "            \$this->model->delete(\$id);\n\n";
        $controllerContent .= "            return \$this->respondDeleted([\n";
        $controllerContent .= "                'status' => 'success',\n";
        $controllerContent .= "                'message' => 'Registro eliminado exitosamente'\n";
        $controllerContent .= "            ]);\n";
        $controllerContent .= "        } catch (\\Exception \$e) {\n";
        $controllerContent .= "            return \$this->failServerError('Error al eliminar registro: ' . \$e->getMessage());\n";
        $controllerContent .= "        }\n";
        $controllerContent .= "    }\n";
        $controllerContent .= "}\n";

        file_put_contents($controllerFile, $controllerContent);
        CLI::write("  ✅ {$controllerName}.php generado", 'green');
    }

    private function tableToModelName($tableName)
    {
        $singular = rtrim($tableName, 's');
        return ucfirst($singular) . 'Model';
    }

    private function tableToControllerName($tableName)
    {
        $singular = rtrim($tableName, 's');
        return ucfirst($singular) . 'Controller';
    }

    private function fieldToMethodName($fieldName)
    {
        return str_replace('_id', '', $fieldName);
    }

    private function tableToMethodName($tableName)
    {
        $singular = rtrim($tableName, 's');
        return $singular;
    }

    private function getPrimaryKey($tableName)
    {
        $singular = rtrim($tableName, 's');
        return $singular . '_id';
    }

    private function getRelationType($tableName, $fkField, $fkTable)
    {
        // Lógica simple para determinar el tipo de relación
        if (strpos($fkField, 'usuario_id') !== false && $tableName !== 'usuarios') {
            return 'belongsTo';
        }
        
        if ($tableName === 'usuarios' && strpos($fkField, '_id') !== false) {
            return 'hasOne';
        }
        
        return 'belongsTo';
    }
}
