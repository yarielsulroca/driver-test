<?php
namespace App\Models;

use CodeIgniter\Model;

class ConductorModel extends Model
{
    protected $table = 'conductores';
    protected $primaryKey = 'conductor_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'nombre',
        'apellido',
        'dni',
        'fecha_nacimiento',
        'direccion',
        'telefono',
        'email',
        'categoria_id',
        'estado_registro',
        'fecha_registro'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'nombre' => 'required|min_length[3]|max_length[50]',
        'apellido' => 'permit_empty|min_length[3]|max_length[50]',
        'dni' => 'required|min_length[8]|max_length[20]|is_unique[conductores.dni,conductor_id,{conductor_id}]',
        'fecha_nacimiento' => 'permit_empty|valid_date',
        'direccion' => 'permit_empty|min_length[5]|max_length[200]',
        'telefono' => 'permit_empty|min_length[8]|max_length[20]',
        'email' => 'permit_empty|valid_email|is_unique[conductores.email,conductor_id,{conductor_id}]',
        'categoria_id' => 'permit_empty|integer',
        'estado_registro' => 'required|in_list[activo,inactivo,pendiente,rechazado]'
    ];

    protected $validationMessages = [
        'nombre' => [
            'required' => 'El nombre es requerido',
            'min_length' => 'El nombre debe tener al menos 3 caracteres',
            'max_length' => 'El nombre no puede tener más de 50 caracteres'
        ],
        'dni' => [
            'required' => 'El DNI es requerido',
            'min_length' => 'El DNI debe tener al menos 8 caracteres',
            'max_length' => 'El DNI no puede tener más de 20 caracteres',
            'is_unique' => 'Este DNI ya está registrado'
        ],
        'estado_registro' => [
            'required' => 'El estado de registro es requerido',
            'in_list' => 'El estado de registro debe ser: activo, inactivo, pendiente o rechazado'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    protected $beforeInsert = ['setEstadoRegistro'];
    protected $beforeUpdate = [];

    protected function setEstadoRegistro(array $data)
    {
        $data['data']['estado_registro'] = 'pendiente';
        $data['data']['fecha_registro'] = date('Y-m-d H:i:s');
        return $data;
    }

    // Relaciones
    public function resultados()
    {
        return $this->hasMany('App\Models\ResultadoExamenModel', 'conductor_id', 'conductor_id');
    }

    public function categoria()
    {
        return $this->belongsTo('App\Models\CategoriaModel', 'categoria_id', 'categoria_id');
    }

    /**
     * Verifica si el conductor tiene exámenes
     */
    public function tieneExamenes($conductorId = null)
    {
        if ($conductorId === null) {
            return false;
        }

        $db = \Config\Database::connect();
        $count = $db->table('resultados_examenes')
                   ->where('conductor_id', $conductorId)
                   ->countAllResults();

        return $count > 0;
    }

    /**
     * Obtiene información detallada de exámenes y categorías del conductor
     */
    public function getExamenesInfo($conductorId)
    {
        try {
            $db = \Config\Database::connect();
            
            // Primero verificamos si el conductor existe en resultados_examenes
            $hasExams = $db->table('resultados_examenes')
                          ->where('conductor_id', $conductorId)
                          ->countAllResults() > 0;

            if (!$hasExams) {
                return [];
            }
            
            $query = $db->table('resultados_examenes re')
                ->select('re.resultado_examen_id, re.fecha_realizacion, re.puntuacion, 
                         e.examen_id, e.titulo as examen_titulo, e.descripcion as examen_descripcion,
                         c.categoria_id, c.nombre as categoria_nombre, c.descripcion as categoria_descripcion')
                ->join('examenes e', 'e.examen_id = re.examen_id')
                ->join('categorias c', 'c.categoria_id = e.categoria_id')
                ->where('re.conductor_id', $conductorId)
                ->get();

            return $query->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Error al obtener información de exámenes: ' . $e->getMessage());
            return [];
        }
    }
} 