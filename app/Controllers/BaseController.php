<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use Psr\Log\LoggerInterface;

class BaseController extends Controller
{
    // auto-load helper
    protected $helpers = ['nvr'];

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);

        // start session (CI4 auto-handle)
        session();
    }
}
