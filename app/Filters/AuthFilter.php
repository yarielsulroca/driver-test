<?php

namespace App\Filters;

use App\Services\SessionService;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Config\Services;

class AuthFilter implements FilterInterface
{
    /**
     * Verifica la autenticación del conductor
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $response = Services::response();
        $token = $request->getHeaderLine('Authorization');

        if (empty($token)) {
            return $response->setJSON([
                'status' => 'error',
                'message' => 'Token no proporcionado'
            ])->setStatusCode(401);
        }

        try {
            $token = str_replace('Bearer ', '', $token);
            $key = getenv('JWT_SECRET_KEY');
            $decoded = JWT::decode($token, new Key($key, 'HS256'));

            // Verificar si la sesión es válida en Redis
            $sessionService = new SessionService();
            if (!$sessionService->verificarSesion($decoded->dni, $token)) {
                return $response->setJSON([
                    'status' => 'error',
                    'message' => 'Sesión inválida o expirada'
                ])->setStatusCode(401);
            }

            // Agregar información del conductor al request
            $request->conductor = $decoded;
            return $request;

        } catch (\Exception $e) {
            return $response->setJSON([
                'status' => 'error',
                'message' => 'Token inválido: ' . $e->getMessage()
            ])->setStatusCode(401);
        }
    }

    /**
     * No hacemos nada después de la petición
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No hacemos nada
    }
} 