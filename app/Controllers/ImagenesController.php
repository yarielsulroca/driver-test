<?php

namespace App\Controllers;

use App\Models\ImagenModel;
use CodeIgniter\RESTful\ResourceController;

class ImagenesController extends ResourceController
{
    protected $imagenModel;
    protected $format = 'json';

    public function __construct()
    {
        $this->imagenModel = new ImagenModel();
    }

    public function index()
    {
        $imagenes = $this->imagenModel->findAll();
        return $this->respond($imagenes);
    }

    public function show($id = null)
    {
        $imagen = $this->imagenModel->find($id);
        if (!$imagen) {
            return $this->failNotFound('Imagen no encontrada');
        }
        return $this->respond($imagen);
    }

    public function create()
    {
        $rules = [
            'nombre' => 'required|min_length[3]|max_length[255]',
            'descripcion' => 'permit_empty|max_length[1000]',
            'imagen' => 'uploaded[imagen]|max_size[imagen,2048]|is_image[imagen]'
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $file = $this->request->getFile('imagen');
        $nombreArchivo = $file->getRandomName();
        
        // Crear directorio si no existe
        $directorio = FCPATH . 'uploads/imagenes';
        if (!is_dir($directorio)) {
            mkdir($directorio, 0777, true);
        }

        // Mover el archivo
        $file->move($directorio, $nombreArchivo);

        $data = [
            'nombre' => $this->request->getPost('nombre'),
            'descripcion' => $this->request->getPost('descripcion'),
            'ruta' => 'uploads/imagenes/' . $nombreArchivo,
            'tipo' => $file->getClientMimeType(),
            'tamano' => $file->getSize()
        ];

        $imagenId = $this->imagenModel->insert($data);
        $data['imagen_id'] = $imagenId;

        return $this->respondCreated($data);
    }

    public function update($id = null)
    {
        $imagen = $this->imagenModel->find($id);
        if (!$imagen) {
            return $this->failNotFound('Imagen no encontrada');
        }

        $rules = [
            'nombre' => 'required|min_length[3]|max_length[255]',
            'descripcion' => 'permit_empty|max_length[1000]'
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $data = [
            'nombre' => $this->request->getPost('nombre'),
            'descripcion' => $this->request->getPost('descripcion')
        ];

        $this->imagenModel->update($id, $data);
        return $this->respond($this->imagenModel->find($id));
    }

    public function delete($id = null)
    {
        $imagen = $this->imagenModel->find($id);
        if (!$imagen) {
            return $this->failNotFound('Imagen no encontrada');
        }

        // Eliminar archivo físico
        $rutaArchivo = FCPATH . $imagen['ruta'];
        if (file_exists($rutaArchivo)) {
            unlink($rutaArchivo);
        }

        $this->imagenModel->delete($id);
        return $this->respondDeleted(['message' => 'Imagen eliminada correctamente']);
    }

    public function getImagen($id = null)
    {
        $imagen = $this->imagenModel->find($id);
        if (!$imagen) {
            return $this->failNotFound('Imagen no encontrada');
        }

        $rutaArchivo = FCPATH . $imagen['ruta'];
        if (!file_exists($rutaArchivo)) {
            return $this->failNotFound('Archivo de imagen no encontrado');
        }

        return $this->response->download($rutaArchivo, null)->setFileName($imagen['nombre']);
    }
} 