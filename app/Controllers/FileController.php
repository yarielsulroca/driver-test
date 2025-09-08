<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\HTTP\ResponseInterface;

class FileController extends ResourceController
{
    protected $format = 'json';

    public function __construct()
    {
        // Configurar CORS si es necesario
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: X-Requested-With, Content-Type, Accept, Origin, Authorization');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        
        // Manejar peticiones OPTIONS (preflight)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }
    }

    /**
     * Subir imagen para una pregunta
     */
    public function uploadImage()
    {
        try {
            log_message('info', '=== INICIO SUBIDA DE IMAGEN ===');
            log_message('info', 'Método HTTP: ' . $this->request->getMethod());
            log_message('info', 'Headers: ' . print_r($this->request->getHeaders(), true));
            log_message('info', 'FILES: ' . print_r($_FILES, true));
            log_message('info', 'POST: ' . print_r($_POST, true));
            
            $file = $this->request->getFile('imagen');
            log_message('info', 'Archivo recibido: ' . ($file ? 'SÍ' : 'NO'));
            
            if (!$file) {
                log_message('error', 'ERROR: No se recibió ningún archivo');
                log_message('error', 'FILES array vacío o no existe');
                return $this->fail('No se recibió ningún archivo', 400);
            }
            
            if (!$file->isValid()) {
                $errorCode = $file->getError();
                $errorString = $file->getErrorString();
                log_message('error', 'ERROR: Archivo no válido - Código: ' . $errorCode . ', Mensaje: ' . $errorString);
                return $this->fail('Archivo no válido: ' . $errorString, 400);
            }

            // Validar tipo de archivo
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/avif'];
            if (!in_array($file->getMimeType(), $allowedTypes)) {
                return $this->fail('Tipo de archivo no permitido. Solo se permiten: JPG, PNG, GIF, WEBP, AVIF', 400);
            }

            // Validar tamaño (máximo 5MB)
            if ($file->getSize() > 5 * 1024 * 1024) {
                return $this->fail('El archivo es demasiado grande. Máximo 5MB', 400);
            }

            // Obtener información del archivo ANTES de moverlo
            $fileSize = $file->getSize();
            $fileMimeType = $file->getMimeType();
            $originalName = $file->getClientName();
            
            // Generar nombre único
            $newName = $file->getRandomName();
            
            // Mover archivo a directorio de uploads
            $uploadPath = WRITEPATH . 'uploads/preguntas/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            $file->move($uploadPath, $newName);
            
            // Retornar información del archivo
            log_message('info', 'Archivo subido exitosamente: ' . $newName);
            log_message('info', 'Tamaño: ' . $fileSize . ' bytes');
            log_message('info', 'Tipo MIME: ' . $fileMimeType);
            log_message('info', '=== FIN SUBIDA DE IMAGEN ===');
            
            return $this->respond([
                'status' => 'success',
                'data' => [
                    'filename' => $newName,
                    'original_name' => $originalName,
                    'size' => $fileSize,
                    'mime_type' => $fileMimeType,
                    'url' => base_url('writable/uploads/preguntas/' . $newName)
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error subiendo imagen: ' . $e->getMessage());
            return $this->failServerError('Error al subir la imagen: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar imagen
     */
    public function deleteImage()
    {
        try {
            $filename = $this->request->getPost('filename');
            
            if (!$filename) {
                return $this->fail('Nombre de archivo requerido', 400);
            }

            $filePath = WRITEPATH . 'uploads/preguntas/' . $filename;
            
            if (file_exists($filePath)) {
                unlink($filePath);
                return $this->respond([
                    'status' => 'success',
                    'message' => 'Imagen eliminada correctamente'
                ]);
            } else {
                return $this->failNotFound('Archivo no encontrado');
            }

        } catch (\Exception $e) {
            log_message('error', 'Error eliminando imagen: ' . $e->getMessage());
            return $this->failServerError('Error al eliminar la imagen: ' . $e->getMessage());
        }
    }

    /**
     * Obtener imagen
     */
    public function getImage($filename)
    {
        try {
            $filePath = WRITEPATH . 'uploads/preguntas/' . $filename;
            
            if (!file_exists($filePath)) {
                return $this->failNotFound('Archivo no encontrado');
            }

            // Usar una alternativa más robusta para obtener el MIME type
            $mimeType = $this->getMimeType($filePath);
            $this->response->setContentType($mimeType);
            $this->response->setBody(file_get_contents($filePath));
            
            return $this->response;

        } catch (\Exception $e) {
            log_message('error', 'Error obteniendo imagen: ' . $e->getMessage());
            return $this->failServerError('Error al obtener la imagen: ' . $e->getMessage());
        }
    }

    /**
     * Obtener MIME type de forma robusta
     */
    private function getMimeType($filePath)
    {
        // Método 1: Usar finfo si está disponible
        if (function_exists('finfo_open')) {
            try {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $filePath);
                finfo_close($finfo);
                if ($mimeType && $mimeType !== 'application/octet-stream') {
                    return $mimeType;
                }
            } catch (\Exception $e) {
                log_message('warning', 'finfo falló: ' . $e->getMessage());
            }
        }

        // Método 2: Usar mime_content_type si está disponible
        if (function_exists('mime_content_type')) {
            try {
                $mimeType = mime_content_type($filePath);
                if ($mimeType && $mimeType !== 'application/octet-stream') {
                    return $mimeType;
                }
            } catch (\Exception $e) {
                log_message('warning', 'mime_content_type falló: ' . $e->getMessage());
            }
        }

        // Método 3: Detectar por extensión
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'avif' => 'image/avif'
        ];

        return $mimeTypes[$extension] ?? 'application/octet-stream';
    }
}
