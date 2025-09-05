<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use Psr\Log\LoggerInterface;

class BaseController extends Controller
{
    protected $helpers = ['nvr']; // biar helper ke-load tanpa oprek Autoload.php

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        // pastiin session ready
        if (! session()->isValid()) {
            session();
        }
    }
}
