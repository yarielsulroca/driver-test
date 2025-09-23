<?php

namespace App\Services;

use Predis\Client;

class SessionService
{
    private $redis;
    private $prefix = 'session:conductor:';
    private $ttl = 3600; // 1 hora

    /**
     * Constructor: Inicializa la conexión con Redis
     */
    public function __construct()
    {
        $this->redis = new Client([
            'scheme' => 'tcp',
            'host'   => 'localhost',
            'port'   => 6379,
        ]);
    }

    /**
     * Registra una nueva sesión para un conductor en Redis
     * @param string $dni DNI del conductor
     * @param string $token Token de sesión
     * @return bool Verdadero si la operación fue exitosa
     */
    public function registrarSesion(string $dni, string $token): bool
    {
        // Invalidar sesión anterior si existe
        $this->invalidarSesionAnterior($dni);

        // Guardar nueva sesión
        $resultado = $this->redis->setex(
            $this->prefix . $dni,
            $this->ttl,
            $token
        );
        
        return $resultado === 'OK';
    }

    /**
     * Verifica si un token es válido para un DNI específico
     * @param string $dni DNI del conductor
     * @param string $token Token a verificar
     * @return bool Verdadero si el token es válido
     */
    public function verificarSesion(string $dni, string $token): bool
    {
        $tokenAlmacenado = $this->redis->get($this->prefix . $dni);
        return $tokenAlmacenado === $token;
    }

    /**
     * Invalida la sesión anterior de un conductor eliminándola de Redis
     * @param string $dni DNI del conductor
     */
    private function invalidarSesionAnterior(string $dni): void
    {
        $this->redis->del($this->prefix . $dni);
    }

    /**
     * Cierra la sesión activa de un conductor
     * @param string $dni DNI del conductor
     * @return bool Verdadero si se cerró una sesión existente
     */
    public function cerrarSesion(string $dni): bool
    {
        return $this->redis->del($this->prefix . $dni) > 0;
    }

    /**
     * Actualiza el token de una sesión existente
     * @param string $dni DNI del conductor
     * @param string $nuevoToken Nuevo token de sesión
     * @return bool Verdadero si la actualización fue exitosa
     */
    public function actualizarToken(string $dni, string $nuevoToken): bool
    {
        $resultado = $this->redis->setex(
            $this->prefix . $dni,
            $this->ttl,
            $nuevoToken
        );
        
        return $resultado === 'OK';
    }
}