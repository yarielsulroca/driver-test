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
        'usuario_id',
        'licencia',
        'fecha_vencimiento',
        'estado'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'usuario_id' => 'required|integer|is_not_unique[usuarios.usuario_id]',
        'licencia' => 'required|min_length[3]|max_length[20]',
        'fecha_vencimiento' => 'required|valid_date',
        'estado' => 'required|in_list[activo,inactivo]'
    ];

    protected $validationMessages = [
        'usuario_id' => [
            'required' => 'El ID del usuario es requerido',
            'integer' => 'El ID del usuario debe ser un número entero',
            'is_not_unique' => 'El usuario especificado no existe'
        ],
        'licencia' => [
            'required' => 'El número de licencia es requerido',
            'min_length' => 'La licencia debe tener al menos 3 caracteres',
            'max_length' => 'La licencia no puede exceder los 20 caracteres'
        ],
        'fecha_vencimiento' => [
            'required' => 'La fecha de vencimiento es requerida',
            'valid_date' => 'La fecha de vencimiento no es válida'
        ],
        'estado' => [
            'required' => 'El estado es requerido',
            'in_list' => 'El estado debe ser activo o inactivo'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Relaciones
    public function usuario()
    {
        return $this->belongsTo('App\Models\UsuarioModel', 'usuario_id', 'usuario_id');
    }

    public function escuelas()
    {
        return $this->belongsToMany('App\Models\EscuelaModel', 'conductor_escuela', 'conductor_id', 'escuela_id');
    }

    public function categoriasAprobadas()
    {
        return $this->hasMany('App\Models\CategoriaAprobadaModel', 'conductor_id', 'conductor_id');
    }

    public function resultados()
    {
        return $this->hasMany('App\Models\ResultadoExamenModel', 'conductor_id', 'conductor_id');
    }

    /**
     * Obtener escuelas de un conductor usando el modelo pivote
     */
    public function getEscuelas($conductorId)
    {
        $conductorEscuelaModel = new \App\Models\ConductorEscuelaModel();
        return $conductorEscuelaModel->getEscuelasConductor($conductorId);
    }

    /**
     * Asignar escuelas a un conductor
     */
    public function asignarEscuelas($conductorId, $escuelaIds)
    {
        $conductorEscuelaModel = new \App\Models\ConductorEscuelaModel();
        return $conductorEscuelaModel->asignarEscuelas($conductorId, $escuelaIds);
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
                ->select('re.resultado_id, re.fecha_realizacion, re.puntaje_total, 
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

    /**
     * Obtiene el perfil completo del conductor incluyendo datos del usuario
     */
    public function getPerfilCompleto($conductorId)
    {
        return $this->select('
            conductores.*,
            usuarios.nombre,
            usuarios.apellido,
            usuarios.email,
            usuarios.dni,
            usuarios.estado as estado_usuario
        ')
        ->join('usuarios', 'usuarios.usuario_id = conductores.usuario_id')
        ->where('conductores.conductor_id', $conductorId)
        ->first();
    }

    /**
     * Obtiene conductores con información de usuario y perfil
     */
    public function getConductoresConPerfil($filtros = [])
    {
        $query = $this->select('
            conductores.*,
            usuarios.nombre,
            usuarios.apellido,
            usuarios.email,
            usuarios.dni,
            perfiles.telefono,
            perfiles.direccion,
            perfiles.fecha_nacimiento,
            perfiles.genero
        ')
        ->join('usuarios', 'usuarios.usuario_id = conductores.usuario_id')
        ->join('perfiles', 'perfiles.usuario_id = usuarios.usuario_id', 'left');
        
        // Aplicar filtros si se especifican
        if (!empty($filtros['estado'])) {
            $query->where('conductores.estado', $filtros['estado']);
        }
        
        if (!empty($filtros['estado_usuario'])) {
            $query->where('usuarios.estado', $filtros['estado_usuario']);
        }
        
        return $query->findAll();
    }
} 