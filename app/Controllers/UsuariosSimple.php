<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

class UsuariosSimple extends BaseController
{
    public function index()
    {
        try {
            $usuarioModel = new UsuarioModel();
            
            // Obtener usuarios que NO tienen conductor asignado
            $usuarios = $usuarioModel->select('usuarios.*')
                ->join('conductores', 'conductores.usuario_id = usuarios.usuario_id', 'left')
                ->where('conductores.usuario_id IS NULL')
                ->findAll();
            
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Usuarios sin conductor asignado obtenidos exitosamente',
                'data' => $usuarios
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Error al obtener usuarios: ' . $e->getMessage(),
                'data' => []
            ]);
        }
    }
}
