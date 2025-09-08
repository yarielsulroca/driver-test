<?php

namespace App\Controllers;

use App\Models\ConductorModel;
use App\Models\UsuarioModel;
use App\Services\SessionService;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController extends ResourceController
{
    use ResponseTrait;

    protected $model;
    protected $usuarioModel;
    protected $sessionService;
    protected $format = 'json';

    public function __construct()
    {
        $this->model = new ConductorModel();
        $this->usuarioModel = new UsuarioModel();
        $this->sessionService = new SessionService();
    }

    /**
     * Registro de nuevo conductor
     */
    public function registro()
    {
        try {
            // Obtener datos del body
            $input = $this->request->getBody();
            log_message('debug', 'Datos raw recibidos: ' . $input);
            
            // Intentar decodificar JSON
            $json = json_decode($input, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                log_message('error', 'Error al decodificar JSON: ' . json_last_error_msg());
                return $this->fail([
                    'status' => 'error',
                    'message' => 'Error al decodificar JSON: ' . json_last_error_msg()
                ], 400);
            }
            
            log_message('debug', 'Datos decodificados: ' . json_encode($json));
            
            if (empty($json)) {
                log_message('error', 'No se recibieron datos JSON válidos');
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
                    'rules' => 'required|min_length[8]|max_length[20]|is_unique[usuarios.dni]',
                    'errors' => [
                        'required' => 'El DNI es obligatorio',
                        'min_length' => 'El DNI debe tener al menos 8 caracteres',
                        'max_length' => 'El DNI no puede exceder los 20 caracteres',
                        'is_unique' => 'Ya existe un usuario registrado con este DNI'
                    ]
                ],
                'email' => [
                    'rules' => 'required|valid_email|max_length[100]|is_unique[usuarios.email]',
                    'errors' => [
                        'required' => 'El email es obligatorio',
                        'valid_email' => 'El formato del email no es válido',
                        'max_length' => 'El email no puede exceder los 100 caracteres',
                        'is_unique' => 'Ya existe un usuario registrado con este email'
                    ]
                ],
                'escuela_id' => [
                    'rules' => 'permit_empty|integer|is_not_unique[escuelas.escuela_id]',
                    'errors' => [
                        'integer' => 'El ID de la escuela debe ser un número entero',
                        'is_not_unique' => 'La escuela especificada no existe'
                    ]
                ]
            ];

            // Validar los datos
            $validation = \Config\Services::validation();
            $validation->setRules($rules);

            if (!$validation->run($json)) {
                $errors = $validation->getErrors();
                log_message('debug', 'Errores de validación: ' . json_encode($errors));
                return $this->fail([
                    'status' => 'error',
                    'message' => 'Error de validación',
                    'errors' => $errors
                ], 400);
            }

            // Crear usuario primero
            $usuarioData = [
                'rol_id' => 3, // ID del rol conductor
                'dni' => $json['dni'],
                'nombre' => $json['nombre'],
                'apellido' => $json['apellido'] ?? '',
                'email' => $json['email'],
                'password' => password_hash($json['dni'], PASSWORD_DEFAULT), // Usar DNI como contraseña inicial
                'estado' => 'activo'
            ];

            if (!$this->usuarioModel->insert($usuarioData)) {
                $errors = $this->usuarioModel->errors();
                log_message('error', 'Error al insertar usuario: ' . json_encode($errors));
                return $this->fail([
                    'status' => 'error',
                    'message' => 'Error al registrar el usuario',
                    'errors' => $errors
                ], 400);
            }

            $usuario_id = $this->usuarioModel->getInsertID();

            // Preparar datos para inserción del conductor
            $conductorData = [
                'usuario_id' => $usuario_id,
                'nombre' => $json['nombre'],
                'apellido' => $json['apellido'] ?? '',
                'dni' => $json['dni'],
                'email' => $json['email'],
                'telefono' => $json['telefono'] ?? null,
                'direccion' => $json['direccion'] ?? null,
                'fecha_nacimiento' => $json['fecha_nacimiento'] ?? null,
                'estado_registro' => 'pendiente'
            ];

            // Agregar escuela_id solo si se proporciona
            if (!empty($json['escuela_id'])) {
                $conductorData['escuela_id'] = $json['escuela_id'];
            }

            log_message('debug', 'Datos a insertar en conductor: ' . json_encode($conductorData));

            // Intentar insertar el conductor
            if (!$this->model->insert($conductorData)) {
                // Si falla, eliminar el usuario creado
                $this->usuarioModel->delete($usuario_id);
                $errors = $this->model->errors();
                log_message('error', 'Error al insertar conductor: ' . json_encode($errors));
                return $this->fail([
                    'status' => 'error',
                    'message' => 'Error al registrar el conductor',
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
                log_message('error', 'Error al generar token: ' . $e->getMessage());
                return $this->fail([
                    'status' => 'error',
                    'message' => 'Error al generar el token',
                    'detail' => $e->getMessage()
                ], 500);
            }

        } catch (\Exception $e) {
            log_message('error', 'Error en registro: ' . $e->getMessage());
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Autenticación de conductor por DNI
     */
    public function login()
    {
        // Agregar headers CORS directamente
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
        
        // Manejar peticiones OPTIONS (preflight)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }
        
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

            $conductor = $this->model->select('conductor_id, nombre, apellido, dni, estado_registro')
                                   ->where('dni', $dni)
                                   ->where('estado_registro !=', 'rechazado')
                                   ->first();

            if (!$conductor) {
                return $this->fail([
                    'status' => 'error',
                    'message' => 'Conductor no encontrado o cuenta rechazada',
                    'detail' => 'No existe un conductor registrado con el DNI proporcionado o su cuenta ha sido rechazada'
                ], 404);
            }

            // Log para depuración
            log_message('debug', 'Estado del conductor antes de actualizar: ' . json_encode($conductor));

            // Actualizar estado si está pendiente
            if ($conductor['estado_registro'] === 'pendiente') {
                $this->model->update($conductor['conductor_id'], [
                    'estado_registro' => 'aprobado'
                ]);
                $conductor['estado_registro'] = 'aprobado';
            }

            // Log para depuración
            log_message('debug', 'Estado del conductor después de actualizar: ' . json_encode($conductor));

            // Obtener historial de exámenes (simplificado temporalmente)
            $historialExamenes = [];

            // Verificar si puede presentar nuevos exámenes (simplificado)
            $puedePresentarExamen = [
                'puede_presentar' => true,
                'mensaje' => 'Puedes presentar nuevos exámenes'
            ];

            // Obtener exámenes disponibles (simplificado)
            $examenesDisponibles = [];

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
                
                // Intentar registrar sesión
                try {
                    $this->sessionService->registrarSesion($conductor['dni'], $token);
                } catch (\Exception $e) {
                    log_message('warning', 'Error al registrar sesión: ' . $e->getMessage());
                }

                return $this->respond([
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
                        ],
                        'examenes' => [
                            'historial' => $historialExamenes,
                            'puede_presentar' => $puedePresentarExamen['puede_presentar'],
                            'mensaje_estado' => $puedePresentarExamen['mensaje'],
                            'disponibles' => $examenesDisponibles
                        ]
                    ]
                ]);

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