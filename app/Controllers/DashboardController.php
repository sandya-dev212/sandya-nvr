<?php
namespace App\Controllers;
use App\Models\CamerasModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $isAdmin = (int) (session()->get('is_admin') ?? 0);
        $cam = new CamerasModel();
        if ($isAdmin) {
            $cameras = $cam->orderBy('name')->findAll(10); // default 10 dulu
        } else {
            // TODO: filter by assignment user_camera
            $cameras = [];
        }
        return view('dashboard/index', ['cameras'=>$cameras, 'stats'=>sys_stats()]);
    }
}
