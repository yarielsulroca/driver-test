<?php

namespace App\Controllers;

class DocsController extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Documentación API - Sistema de Exámenes de Conducción',
            'description' => 'Documentación completa de la API del Sistema de Exámenes de Conducción de Lomas de Zamora'
        ];

        return view('docs/index', $data);
    }
} 