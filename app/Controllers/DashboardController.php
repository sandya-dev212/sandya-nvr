<?php

namespace App\Controllers;

use App\Models\CamerasModel;

class DashboardController extends BaseController
{
    public function index()
    {
        // simple guard: kalau belum login → ke /login
        if (! session()->get('uid')) {
            return redirect()->to('/login');
        }

        $isAdmin = (int) (session()->get('is_admin') ?? 0);
        $cam = new CamerasModel();

        if ($isAdmin) {
            // nanti kita ambil pagination dari settings; sementara 10
            $cameras = $cam->orderBy('name', 'asc')->findAll(10);
        } else {
            // TODO: filter by assignment user_camera
            $cameras = [];
        }

        return view('dashboard/index', [
            'cameras' => $cameras,
            'stats'   => sys_stats(),
        ]);
    }
}
