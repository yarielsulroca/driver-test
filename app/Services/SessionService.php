<?php

namespace App\Services;

use Predis\Client;

class SessionService
{
    private $redis;
    private $prefix = 'session:conductor:';
    private $ttl = 3600; // 1 hora

    public function __construct()
    {
        $this->redis = new Client([
            'scheme' => 'tcp',
            'host'   => 'localhost',
            'port'   => 6379,
        ]);
    }

    /**
     * Registra una nueva sesión para un conductor
     */
    public function registrarSesion(string $dni, string $token): bool
    {
        // Invalidar sesión anterior si existe
        $this->invalidarSesionAnterior($dni);

        // Guardar nueva sesión
        return $this->redis->setex(
            $this->prefix . $dni,
            $this->ttl,
            $token
        );
    }

    /**
     * Verifica si un token es válido para un DNI
     */
    public function verificarSesion(string $dni, string $token): bool
    {
        $tokenAlmacenado = $this->redis->get($this->prefix . $dni);
        return $tokenAlmacenado === $token;
    }

    /**
     * Invalida la sesión anterior de un conductor
     */
    private function invalidarSesionAnterior(string $dni): void
    {
        $this->redis->del($this->prefix . $dni);
    }

    /**
     * Invalida la sesión de un conductor
     */
    public function cerrarSesion(string $dni): bool
    {
        return $this->redis->del($this->prefix . $dni) > 0;
    }

    /**
     * Actualiza el token de una sesión existente
     */
    public function actualizarToken(string $dni, string $nuevoToken): bool
    {
        return $this->redis->setex(
            $this->prefix . $dni,
            $this->ttl,
            $nuevoToken
        );
    }
} 