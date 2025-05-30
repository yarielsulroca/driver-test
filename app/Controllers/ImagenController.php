<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class ImagenController extends ResourceController
{
    use ResponseTrait;

    protected $format = 'json';
    protected $uploadPath = WRITEPATH . 'uploads/';

    public function __construct()
    {
        // Crear el directorio de uploads si no existe
        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0777, true);
        }
    }

    /**
     * Sube una imagen
     */
    public function upload()
    {
        try {
            $file = $this->request->getFile('imagen');

            if (!$file || !$file->isValid()) {
                return $this->fail([
                    'status' => 'error',
                    'message' => 'No se recibió una imagen válida'
                ], 400);
            }

            // Validar tipo de archivo
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($file->getMimeType(), $allowedTypes)) {
                return $this->fail([
                    'status' => 'error',
                    'message' => 'Tipo de archivo no permitido. Solo se permiten imágenes JPG, PNG y GIF'
                ], 400);
            }

            // Validar tamaño (máximo 5MB)
            if ($file->getSize() > 5242880) {
                return $this->fail([
                    'status' => 'error',
                    'message' => 'La imagen excede el tamaño máximo permitido (5MB)'
                ], 400);
            }

            // Generar nombre único
            $newName = $file->getRandomName();

            // Mover el archivo
            if (!$file->move($this->uploadPath, $newName)) {
                return $this->fail([
                    'status' => 'error',
                    'message' => 'Error al guardar la imagen'
                ], 500);
            }

            return $this->respond([
                'status' => 'success',
                'message' => 'Imagen subida exitosamente',
                'data' => [
                    'nombre_archivo' => $newName,
                    'ruta' => 'uploads/' . $newName,
                    'tipo' => $file->getMimeType(),
                    'tamaño' => $file->getSize()
                ]
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Elimina una imagen
     */
    public function delete($nombre)
    {
        try {
            $ruta = $this->uploadPath . $nombre;

            if (!file_exists($ruta)) {
                return $this->failNotFound([
                    'status' => 'error',
                    'message' => 'Imagen no encontrada'
                ]);
            }

            if (!unlink($ruta)) {
                return $this->fail([
                    'status' => 'error',
                    'message' => 'Error al eliminar la imagen'
                ], 500);
            }

            return $this->respondDeleted([
                'status' => 'success',
                'message' => 'Imagen eliminada exitosamente'
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }
} 