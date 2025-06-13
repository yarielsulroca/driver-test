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
        $authHeader = $request->getHeaderLine('Authorization');
        if (!$authHeader || !preg_match('/Bearer\s(.*)/', $authHeader, $matches)) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON([
                    'status' => 'error',
                    'message' => 'No autorizado. Token no proporcionado.'
                ]);
        }

        $token = $matches[1];
        $key = getenv('JWT_SECRET') ?: 'supersecretkey'; // Cambia esto por tu clave real

        try {
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            // Puedes guardar los datos del usuario en el request para usarlos en el controlador

            // Ejemplo: $request->user = $decoded;
            // Pero en CodeIgniter, lo ideal es usar el servicio de usuario o session

            // Verificar si la sesión es válida en Redis
            $sessionService = new SessionService();
            if (!$sessionService->verificarSesion($decoded->dni, $token)) {
                return service('response')
                    ->setStatusCode(401)
                    ->setJSON([
                        'status' => 'error',
                        'message' => 'Sesión inválida o expirada'
                    ]);
            }

            // Agregar información del conductor al request
            $request->conductor = $decoded;
            return $request;
        } catch (\Exception $e) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Token inválido o expirado',
                    'detail' => $e->getMessage()
                ]);
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