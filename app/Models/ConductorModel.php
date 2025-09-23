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
        'estado',
        'documentos_presentados'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'usuario_id' => 'required|integer',
        'estado' => 'in_list[p,b]',
        'documentos_presentados' => 'permit_empty|max_length[200]'
    ];

    protected $validationMessages = [
        'usuario_id' => [
            'required' => 'El ID del usuario es requerido',
            'integer' => 'El ID del usuario debe ser un número entero'
        ],
        'estado' => [
            'in_list' => 'El estado debe ser p (pendiente) o b (bueno/aprobado)'
        ],
        'documentos_presentados' => [
            'max_length' => 'Los documentos presentados no pueden exceder los 200 caracteres'
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


    /**
     * Relación con PerfilModel
     */
    public function perfil()
    {
        return $this->hasOne('App\Models\PerfilModel', 'usuario_id', 'usuario_id');
    }

    /**
     * Obtener conductor con información completa del usuario y perfil
     */
    public function getConductorCompleto($conductorId)
    {
        $conductor = $this->find($conductorId);
        if (!$conductor) {
            return null;
        }

        // Cargar información del usuario
        $usuarioModel = new UsuarioModel();
        $conductor['usuario'] = $usuarioModel->find($conductor['usuario_id']);

        // Cargar información del perfil
        $perfilModel = new PerfilModel();
        $conductor['perfil'] = $perfilModel->where('usuario_id', $conductor['usuario_id'])->first();

        return $conductor;
    }

    /**
     * Obtener todos los conductores con información completa
     */
    public function getConductoresCompletos($filtros = [])
    {
        $conductores = $this->findAll();
        
        foreach ($conductores as &$conductor) {
            // Cargar información del usuario
            $usuarioModel = new UsuarioModel();
            $conductor['usuario'] = $usuarioModel->find($conductor['usuario_id']);

            // Cargar información del perfil
            $perfilModel = new PerfilModel();
            $conductor['perfil'] = $perfilModel->where('usuario_id', $conductor['usuario_id'])->first();
        }

        return $conductores;
    }
} 