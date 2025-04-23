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
        'password',
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
        'apellido' => 'required|min_length[3]|max_length[50]',
        'dni' => 'required|min_length[8]|max_length[20]|is_unique[conductores.dni,conductor_id,{conductor_id}]',
        'fecha_nacimiento' => 'required|valid_date',
        'direccion' => 'required|min_length[5]|max_length[200]',
        'telefono' => 'required|min_length[8]|max_length[20]',
        'email' => 'required|valid_email|is_unique[conductores.email,conductor_id,{conductor_id}]',
        'password' => 'required|min_length[8]',
        'categoria_id' => 'required|integer',
        'estado_registro' => 'required|in_list[pendiente,aprobado,rechazado]'
    ];

    protected $beforeInsert = ['hashPassword', 'setEstadoRegistro'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (!isset($data['data']['password'])) return $data;

        $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        return $data;
    }

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

    public function verificarCredenciales($email, $password)
    {
        $conductor = $this->where('email', $email)
                         ->where('estado_registro !=', 'rechazado')
                         ->first();

        if (!$conductor) return false;

        return password_verify($password, $conductor['password']);
    }
} 