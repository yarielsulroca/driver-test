<?php

namespace App\Models;

use CodeIgniter\Model;

class SupervisoreModel extends Model
{
    protected $table = 'supervisores';
    protected $primaryKey = 'supervisor_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'usuario_id',
        'escuela_id',
        'estado',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Relaciones
    public function usuario()
    {
        return $this->belongsTo('App\Models\UsuarioModel', 'usuario_id', 'usuario_id');
    }

    public function escuela()
    {
        return $this->belongsTo('App\Models\EscuelaModel', 'escuela_id', 'escuela_id');
    }

}
