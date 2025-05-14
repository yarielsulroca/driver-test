<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Config\Services;

class AuthTecnicoFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $response = service('response');

        try {
            $token = $request->getHeaderLine('Authorization');
            
            if (empty($token)) {
                return $response->setJSON([
                    'status' => 'error',
                    'message' => 'Token no proporcionado'
                ])->setStatusCode(401);
            }

            $token = str_replace('Bearer ', '', $token);
            $key = defined('JWT_SECRET_KEY') ? JWT_SECRET_KEY : null;
            
            if (empty($key)) {
                return $response->setJSON([
                    'status' => 'error',
                    'message' => 'Error de configuración del servidor'
                ])->setStatusCode(500);
            }

            $decoded = JWT::decode($token, new Key($key, 'HS256'));

            // Verificar que el rol sea técnico
            if (!isset($decoded->rol) || $decoded->rol !== 'tecnico') {
                return $response->setJSON([
                    'status' => 'error',
                    'message' => 'Acceso no autorizado. Se requiere rol de técnico.'
                ])->setStatusCode(403);
            }

            return $request;
        } catch (\Exception $e) {
            return $response->setJSON([
                'status' => 'error',
                'message' => 'Token inválido o expirado'
            ])->setStatusCode(401);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No hacer nada después
    }
} 