<?php
namespace App\Controllers;
use App\Models\SettingsModel;

class SettingsController extends BaseController
{
    public function index()
    {
        // admin only – guard sederhana
        if (!(session()->get('is_admin'))) return redirect()->to('/login');
        $m = new SettingsModel();
        $items = $m->findAll();
        return view('settings/index',['items'=>$items,'stats'=>sys_stats()]);
    }
}
