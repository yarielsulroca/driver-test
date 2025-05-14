<?php

namespace App\Controllers;

use App\Models\ConductorModel;
use App\Services\SessionService;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController extends ResourceController
{
    use ResponseTrait;

    protected $model;
    protected $sessionService;
    protected $format = 'json';

    public function __construct()
    {
        $this->model = new ConductorModel();
        $this->sessionService = new SessionService();
    }

    /**
     * Registro de nuevo conductor
     */
    public function registro()
    {
        try {
            // Obtener datos JSON del body
            $json = $this->request->getJSON(true);
            if (empty($json)) {
                return $this->fail([
                    'status' => 'error',
                    'message' => 'No se recibieron datos JSON válidos'
                ], 400);
            }

            // Validar campos requeridos
            $rules = [
                'nombre' => [
                    'rules' => 'required|min_length[3]|max_length[50]',
                    'errors' => [
                        'required' => 'El nombre es obligatorio',
                        'min_length' => 'El nombre debe tener al menos 3 caracteres',
                        'max_length' => 'El nombre no puede exceder los 50 caracteres'
                    ]
                ],
                'dni' => [
                    'rules' => 'required|min_length[8]|max_length[20]|is_unique[conductores.dni]',
                    'errors' => [
                        'required' => 'El DNI es obligatorio',
                        'min_length' => 'El DNI debe tener al menos 8 caracteres',
                        'max_length' => 'El DNI no puede exceder los 20 caracteres',
                        'is_unique' => 'Ya existe un conductor registrado con este DNI'
                    ]
                ]
            ];

            // Si se proporciona email, agregar validación
            if (isset($json['email'])) {
                $rules['email'] = [
                    'rules' => 'valid_email|max_length[100]|is_unique[conductores.email]',
                    'errors' => [
                        'valid_email' => 'El formato del email no es válido',
                        'max_length' => 'El email no puede exceder los 100 caracteres',
                        'is_unique' => 'Ya existe un conductor registrado con este email'
                    ]
                ];
            }

            if (!$this->validate($rules)) {
                return $this->fail([
                    'status' => 'error',
                    'message' => 'Error de validación',
                    'errors' => $this->validator->getErrors()
                ], 400);
            }

            // Preparar datos para inserción
            $data = [
                'nombre' => $json['nombre'],
                'dni' => $json['dni'],
                'estado_registro' => 'activo'
            ];

            // Campos opcionales
            $camposOpcionales = [
                'apellido',
                'fecha_nacimiento',
                'direccion',
                'telefono',
                'email'
            ];

            foreach ($camposOpcionales as $campo) {
                if (isset($json[$campo])) {
                    $data[$campo] = $json[$campo];
                }
            }

            // Intentar insertar el conductor
            if (!$this->model->insert($data)) {
                $errors = $this->model->errors();
                $errorMessage = 'Error al registrar el conductor en la base de datos';
                
                // Personalizar mensaje según el tipo de error
                if (isset($errors['dni'])) {
                    $errorMessage = 'Ya existe un conductor registrado con este DNI';
                } elseif (isset($errors['email'])) {
                    $errorMessage = 'Ya existe un conductor registrado con este email';
                }
                
                return $this->fail([
                    'status' => 'error',
                    'message' => $errorMessage,
                    'errors' => $errors
                ], 400);
            }

            $conductor_id = $this->model->getInsertID();
            $conductor = $this->model->find($conductor_id);

            // Obtener información de exámenes
            $examenes = $this->model->getExamenesInfo($conductor_id);
            $tieneExamenes = !empty($examenes);

            // Generar token JWT
            $key = defined('JWT_SECRET_KEY') ? JWT_SECRET_KEY : null;
            if (empty($key)) {
                return $this->fail([
                    'status' => 'error',
                    'message' => 'Error de configuración del servidor',
                    'detail' => 'La clave JWT no está configurada'
                ], 500);
            }

            $iat = time();
            $exp = $iat + (defined('JWT_TIME_TO_LIVE') ? JWT_TIME_TO_LIVE : 3600);

            $payload = [
                'iss' => 'Sistema de Exámenes',
                'sub' => $conductor['conductor_id'],
                'iat' => $iat,
                'exp' => $exp,
                'rol' => 'conductor',
                'dni' => $conductor['dni']
            ];

            try {
                $token = JWT::encode($payload, $key, 'HS256');
                
                // Intentar registrar sesión, pero continuar incluso si falla
                try {
                    $this->sessionService->registrarSesion($conductor['dni'], $token);
                } catch (\Exception $e) {
                    log_message('warning', 'Error al registrar sesión: ' . $e->getMessage());
                    // Continuar sin manejo de sesiones
                }

                return $this->respond([
                    'status' => 'success',
                    'message' => '¡Registro exitoso! Bienvenido al sistema de exámenes.',
                    'data' => [
                        'token' => $token,
                        'conductor' => [
                            'id' => $conductor['conductor_id'],
                            'nombre' => $conductor['nombre'],
                            'apellido' => $conductor['apellido'] ?? '',
                            'dni' => $conductor['dni'],
                            'telefono' => $conductor['telefono'] ?? '',
                            'email' => $conductor['email'] ?? '',
                            'estado_registro' => $conductor['estado_registro'],
                            'tiene_examenes' => $tieneExamenes,
                            'examenes' => $examenes
                        ],
                        'token_expira_en' => date('Y-m-d H:i:s', $exp),
                        'instrucciones' => 'Por favor, guarde este token de forma segura. Lo necesitará para futuras autenticaciones.'
                    ]
                ], 201);

            } catch (\Exception $e) {
                return $this->fail([
                    'status' => 'error',
                    'message' => 'Error al generar el token',
                    'detail' => $e->getMessage()
                ], 500);
            }

        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Autenticación de conductor por DNI
     */
    public function login()
    {
        try {
            // Obtener datos JSON del body
            $json = $this->request->getJSON(true);
            if (empty($json)) {
                return $this->fail([
                    'status' => 'error',
                    'message' => 'No se recibieron datos JSON válidos'
                ], 400);
            }

            $rules = [
                'dni' => [
                    'rules' => 'required|min_length[8]|max_length[20]',
                    'errors' => [
                        'required' => 'El DNI es obligatorio',
                        'min_length' => 'El DNI debe tener al menos 8 caracteres',
                        'max_length' => 'El DNI no puede exceder los 20 caracteres'
                    ]
                ]
            ];

            if (!$this->validate($rules)) {
                return $this->fail([
                    'status' => 'error',
                    'message' => 'Error de validación',
                    'errors' => $this->validator->getErrors()
                ], 400);
            }

            $dni = $json['dni'];

            $conductor = $this->model->where('dni', $dni)
                                   ->where('estado_registro !=', 'rechazado')
                                   ->first();

            if (!$conductor) {
                return $this->fail([
                    'status' => 'error',
                    'message' => 'Conductor no encontrado o cuenta rechazada',
                    'detail' => 'No existe un conductor registrado con el DNI proporcionado o su cuenta ha sido rechazada'
                ], 404);
            }

            // Actualizar estado si está pendiente
            if ($conductor['estado_registro'] === 'pendiente') {
                $this->model->update($conductor['conductor_id'], [
                    'estado_registro' => 'activo'
                ]);
                $conductor['estado_registro'] = 'activo';
            }

            // Generar token JWT
            $key = defined('JWT_SECRET_KEY') ? JWT_SECRET_KEY : null;
            if (empty($key)) {
                return $this->fail([
                    'status' => 'error',
                    'message' => 'Error de configuración del servidor',
                    'detail' => 'La clave JWT no está configurada'
                ], 500);
            }

            $iat = time();
            $exp = $iat + (defined('JWT_TIME_TO_LIVE') ? JWT_TIME_TO_LIVE : 3600);

            $payload = [
                'iss' => 'Sistema de Exámenes',
                'sub' => $conductor['conductor_id'],
                'iat' => $iat,
                'exp' => $exp,
                'rol' => 'conductor',
                'dni' => $conductor['dni']
            ];

            try {
                $token = JWT::encode($payload, $key, 'HS256');
                
                // Intentar registrar sesión, pero continuar incluso si falla
                try {
                    $this->sessionService->registrarSesion($conductor['dni'], $token);
                } catch (\Exception $e) {
                    log_message('warning', 'Error al registrar sesión: ' . $e->getMessage());
                    // Continuar sin manejo de sesiones
                }

                // Obtener información de exámenes
                $examenes = $this->model->getExamenesInfo($conductor['conductor_id']);
                
                // Preparar respuesta base
                $response = [
                    'status' => 'success',
                    'message' => '¡Inicio de sesión exitoso!',
                    'data' => [
                        'token' => $token,
                        'token_expira_en' => date('Y-m-d H:i:s', $exp),
                        'conductor' => [
                            'id' => $conductor['conductor_id'],
                            'nombre' => $conductor['nombre'],
                            'apellido' => $conductor['apellido'] ?? '',
                            'dni' => $conductor['dni'],
                            'estado_registro' => $conductor['estado_registro']
                        ]
                    ]
                ];

                // Agregar información de exámenes si existen
                if (!empty($examenes)) {
                    $response['data']['examenes'] = [
                        'estado' => 'Con exámenes asociados',
                        'detalle' => $examenes
                    ];
                } else {
                    $response['data']['examenes'] = [
                        'estado' => 'Sin exámenes asociados',
                        'detalle' => []
                    ];
                }

                return $this->respond($response);

            } catch (\Exception $e) {
                return $this->fail([
                    'status' => 'error',
                    'message' => 'Error al generar el token',
                    'detail' => $e->getMessage()
                ], 500);
            }

        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Cerrar sesión
     */
    public function logout()
    {
        try {
            $token = $this->request->getHeaderLine('Authorization');
            if (empty($token)) {
                return $this->fail([
                    'status' => 'error',
                    'message' => 'Token no proporcionado'
                ], 401);
            }

            $token = str_replace('Bearer ', '', $token);
            $key = defined('JWT_SECRET_KEY') ? JWT_SECRET_KEY : null;
            $decoded = JWT::decode($token, new Key($key, 'HS256'));

            // Cerrar sesión en Redis
            $this->sessionService->cerrarSesion($decoded->dni);

            return $this->respond([
                'status' => 'success',
                'message' => 'Sesión cerrada exitosamente. ¡Hasta pronto!',
                'data' => [
                    'info' => 'Su token ha sido invalidado y la sesión ha sido cerrada correctamente.'
                ]
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Renovar token
     */
    public function refreshToken()
    {
        try {
            $key = defined('JWT_SECRET_KEY') ? JWT_SECRET_KEY : null;
            $token = $this->request->getHeaderLine('Authorization');
            
            if (empty($token)) {
                return $this->fail([
                    'status' => 'error',
                    'message' => 'Token no proporcionado'
                ], 401);
            }

            $token = str_replace('Bearer ', '', $token);
            
            // Verificar que el token actual sea válido
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            
            if (!$this->sessionService->verificarSesion($decoded->dni, $token)) {
                return $this->fail([
                    'status' => 'error',
                    'message' => 'Sesión inválida'
                ], 401);
            }

            // Generar nuevo token
            $iat = time();
            $exp = $iat + 3600;

            $payload = [
                'iss' => 'Sistema de Exámenes',
                'sub' => $decoded->sub,
                'iat' => $iat,
                'exp' => $exp,
                'rol' => $decoded->rol,
                'dni' => $decoded->dni
            ];

            $newToken = JWT::encode($payload, $key, 'HS256');

            // Actualizar token en Redis
            $this->sessionService->actualizarToken($decoded->dni, $newToken);

            return $this->respond([
                'status' => 'success',
                'message' => 'Token renovado exitosamente',
                'data' => [
                    'token' => $newToken,
                    'token_expira_en' => date('Y-m-d H:i:s', $exp),
                    'info' => 'Su nuevo token es válido por 1 hora.'
                ]
            ]);

        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }
} 