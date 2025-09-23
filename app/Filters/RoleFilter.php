<?php

namespace App\Filters;

use App\Services\SessionService;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\UsuarioRolModel;

class RoleFilter implements FilterInterface
{
    /**
     * Verifica la autenticación y roles del usuario
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
        $key = getenv('JWT_SECRET') ?: 'supersecretkey';

        try {
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            
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

            // Verificar roles si se especificaron
            if (!empty($arguments)) {
                $rolesRequeridos = $arguments;
                $rolesUsuario = $decoded->roles ?? [];
                
                // Verificar si el usuario tiene alguno de los roles requeridos
                $tieneRol = false;
                foreach ($rolesRequeridos as $rolRequerido) {
                    if (in_array($rolRequerido, $rolesUsuario)) {
                        $tieneRol = true;
                        break;
                    }
                }

                if (!$tieneRol) {
                    return service('response')
                        ->setStatusCode(403)
                        ->setJSON([
                            'status' => 'error',
                            'message' => 'Acceso denegado. No tienes los permisos necesarios.',
                            'required_roles' => $rolesRequeridos,
                            'user_roles' => $rolesUsuario
                        ]);
                }
            }

            // Agregar información del usuario al request
            $request->user = $decoded;
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
