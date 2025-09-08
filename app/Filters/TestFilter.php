<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class TestFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        file_put_contents(__DIR__.'/../../writable/test_filter.log', 'TEST FILTER ejecutado: ' . date('c') . ' - URI: ' . $request->getUri()->getPath() . PHP_EOL, FILE_APPEND);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }
} 