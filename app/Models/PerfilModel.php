<?php

namespace App\Models;

use CodeIgniter\Model;

class PerfilModel extends Model
{
    protected $table = 'perfiles';
    protected $primaryKey = 'perfil_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'usuario_id',
        'telefono',
        'direccion',
        'fecha_nacimiento',
        'genero',
        'foto'
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
        'telefono' => 'permit_empty|min_length[7]|max_length[15]|regex_match[/^[0-9+\-\s\(\)]+$/]',
        'direccion' => 'permit_empty|max_length[255]',
        'fecha_nacimiento' => 'permit_empty|valid_date|valid_date_range[1900-01-01,now]',
        'genero' => 'permit_empty|in_list[M,F,O]',
        'foto' => 'permit_empty|max_length[255]|valid_url_strict'
    ];

    protected $validationMessages = [
        'usuario_id' => [
            'required' => 'El ID del usuario es requerido',
            'integer' => 'El ID del usuario debe ser un número entero',
            'is_not_unique' => 'El usuario especificado no existe'
        ],
        'telefono' => [
            'min_length' => 'El teléfono debe tener al menos 7 caracteres',
            'max_length' => 'El teléfono no puede exceder los 15 caracteres',
            'regex_match' => 'El formato del teléfono no es válido'
        ],
        'direccion' => [
            'max_length' => 'La dirección no puede exceder los 255 caracteres'
        ],
        'fecha_nacimiento' => [
            'valid_date' => 'La fecha de nacimiento no es válida',
            'valid_date_range' => 'La fecha de nacimiento debe estar entre 1900 y la fecha actual'
        ],
        'genero' => [
            'in_list' => 'El género debe ser M (Masculino), F (Femenino) u O (Otro)'
        ],
        'foto' => [
            'max_length' => 'La URL de la foto no puede exceder los 255 caracteres',
            'valid_url_strict' => 'La URL de la foto no es válida'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Obtiene el usuario asociado a este perfil
     * @return \CodeIgniter\Database\BaseResult
     */
    public function usuario()
    {
        return $this->belongsTo('App\Models\UsuarioModel', 'usuario_id', 'usuario_id');
    }

    /**
     * Obtiene el perfil completo de un usuario incluyendo datos del usuario
     * @param int $usuarioId
     * @return array|null
     */
    public function getPerfilCompleto($usuarioId)
    {
        return $this->select('
            perfiles.*,
            usuarios.nombre,
            usuarios.apellido,
            usuarios.email,
            usuarios.dni,
            usuarios.estado as estado_usuario
        ')
        ->join('usuarios', 'usuarios.usuario_id = perfiles.usuario_id')
        ->where('perfiles.usuario_id', $usuarioId)
        ->first();
    }

    /**
     * Crea o actualiza un perfil para un usuario
     * @param int $usuarioId
     * @param array $datosPerfil
     * @return bool|int
     */
    public function crearOActualizarPerfil($usuarioId, $datosPerfil)
    {
        $datosPerfil['usuario_id'] = $usuarioId;
        
        // Buscar si ya existe un perfil para este usuario
        $perfilExistente = $this->where('usuario_id', $usuarioId)->first();
        
        if ($perfilExistente) {
            // Actualizar perfil existente
            return $this->update($perfilExistente['perfil_id'], $datosPerfil);
        } else {
            // Crear nuevo perfil
            return $this->insert($datosPerfil);
        }
    }

    /**
     * Obtiene perfiles con información básica del usuario
     * @param array $filtros
     * @return array
     */
    public function getPerfilesConUsuario($filtros = [])
    {
        $query = $this->select('
            perfiles.*,
            usuarios.nombre,
            usuarios.apellido,
            usuarios.email,
            usuarios.dni
        ')
        ->join('usuarios', 'usuarios.usuario_id = perfiles.usuario_id');
        
        // Aplicar filtros si se especifican
        if (!empty($filtros['genero'])) {
            $query->where('perfiles.genero', $filtros['genero']);
        }
        
        if (!empty($filtros['estado_usuario'])) {
            $query->where('usuarios.estado', $filtros['estado_usuario']);
        }
        
        return $query->findAll();
    }
} 