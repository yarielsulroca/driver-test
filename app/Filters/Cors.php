<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class Cors implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Log simple para verificar si el filtro se ejecuta
        file_put_contents(__DIR__.'/../../writable/cors_simple.log', 'CORS SIMPLE ejecutado: ' . date('c') . ' - URI: ' . $request->getUri()->getPath() . ' - Method: ' . $request->getMethod() . PHP_EOL, FILE_APPEND);
        
        // Enviar headers CORS directamente
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Access-Control-Request-Headers, Authorization');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE, PATCH');
        header('Access-Control-Max-Age: 86400');
        
        // Si es una petición OPTIONS (preflight), terminar aquí
        if ($request->getMethod() === 'OPTIONS') {
            http_response_code(200);
            exit();
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Asegurar que los headers CORS estén en la respuesta final
        $response->setHeader('Access-Control-Allow-Origin', '*');
        $response->setHeader('Access-Control-Allow-Headers', 'X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Access-Control-Request-Headers, Authorization');
        $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, DELETE, PATCH');
        $response->setHeader('Access-Control-Max-Age', '86400');
        
        return $response;
    }
}
