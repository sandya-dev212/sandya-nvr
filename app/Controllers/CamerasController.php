<?php
namespace App\Controllers;

use App\Models\CamerasModel;
use App\Models\SettingsModel;

class CamerasController extends BaseController
{
    private function guardAdmin()
    {
        if (!session()->get('uid')) return redirect()->to('/login');
        if (!session()->get('is_admin')) return redirect()->to('/dashboard');
        return null;
    }

    public function index()
    {
        if ($r = $this->guardAdmin()) return $r;

        $m = new CamerasModel();
        $pageSize = 10;
        $s = new SettingsModel();
        $row = $s->find('dashboard_pagination');
        if ($row && is_numeric($row['value'])) $pageSize = (int)$row['value'];

        $cameras = $m->orderBy('name','asc')->paginate($pageSize);
        return view('cameras/index', [
            'cameras' => $cameras,
            'pager'   => $m->pager,
            'stats'   => sys_stats(),
        ]);
    }

    public function create()
    {
        if ($r = $this->guardAdmin()) return $r;

        if ($this->request->getMethod()==='post') {
            $data = $this->validateAndCollect();
            if ($data['__ok']) {
                unset($data['__ok']);
                $m = new CamerasModel();
                if ($m->insert($data) !== false) {
                    return redirect()->to('/cameras');
                }
                // gagal dari Model → tampilkan error
                return view('cameras/form', [
                    'mode'   =>'create',
                    'errors' => $m->errors(),
                    'data'   => $this->request->getPost(),
                    'stats'  => sys_stats(),
                ]);
            }
            // gagal validasi → tampilkan error
            return view('cameras/form', [
                'mode'   =>'create',
                'errors' => $data['__errors'],
                'data'   => $this->request->getPost(),
                'stats'  => sys_stats(),
            ]);
        }

        return view('cameras/form', [
            'mode'=>'create','errors'=>[],'data'=>[],'stats'=>sys_stats()
        ]);
    }

    public function edit($id)
    {
        if ($r = $this->guardAdmin()) return $r;

        $m = new CamerasModel();
        $cam = $m->find((int)$id);
        if (!$cam) return redirect()->to('/cameras');

        if ($this->request->getMethod()==='post') {
            $data = $this->validateAndCollect();
            if ($data['__ok']) {
                unset($data['__ok']);
                if ($m->update((int)$id, $data) !== false) {
                    return redirect()->to('/cameras');
                }
                return view('cameras/form', [
                    'mode'=>'edit','errors'=>$m->errors(),
                    'data'=>array_merge($cam,$this->request->getPost()),
                    'stats'=>sys_stats(),
                ]);
            }
            return view('cameras/form', [
                'mode'=>'edit','errors'=>$data['__errors'],
                'data'=>array_merge($cam,$this->request->getPost()),
                'stats'=>sys_stats(),
            ]);
        }

        return view('cameras/form', [
            'mode'=>'edit','errors'=>[],'data'=>$cam,'stats'=>sys_stats()
        ]);
    }

    public function delete($id)
    {
        if ($r = $this->guardAdmin()) return $r;
        (new CamerasModel())->delete((int)$id);
        return redirect()->to('/cameras');
    }

    // ---------- helpers ----------
    private function validateAndCollect(): array
    {
        $rules = [
            'name'           => 'required|min_length[2]|max_length[120]',
            'ip'             => 'required|valid_ip',
            'username'       => 'permit_empty|max_length[128]',
            'password'       => 'permit_empty|max_length[128]',
            'path'           => 'permit_empty|max_length[255]',
            'port'           => 'permit_empty|integer|greater_than_equal_to[1]|less_than_equal_to[65535]',
            'transport'      => 'required|in_list[tcp,udp]',
            'mode'           => 'required|in_list[recording,watch,disabled]',
            'fps'            => 'permit_empty|integer|greater_than_equal_to[1]|less_than_equal_to[60]',
            'audio'          => 'required|in_list[0,1]',
            'max_days'       => 'permit_empty|integer|greater_than_equal_to[0]',
            'max_storage_mb' => 'permit_empty|integer|greater_than_equal_to[0]',
            'notes'          => 'permit_empty|max_length[2000]',
        ];
        if (!$this->validate($rules)) {
            return ['__ok'=>false,'__errors'=>$this->validator->getErrors()];
        }

        $path = trim((string)$this->request->getPost('path')) ?: '/';
        if ($path[0] !== '/') $path = '/'.$path;

        return [
            '__ok'           => true,
            'name'           => trim($this->request->getPost('name')),
            'ip'             => trim($this->request->getPost('ip')),
            'username'       => $this->emptyToNull(trim((string)$this->request->getPost('username'))),
            'password'       => $this->emptyToNull(trim((string)$this->request->getPost('password'))),
            'path'           => $path,
            'port'           => $this->nullIfEmpty($this->request->getPost('port')),
            'transport'      => $this->request->getPost('transport'),
            'mode'           => $this->request->getPost('mode'),
            'fps'            => $this->nullIfEmpty($this->request->getPost('fps')),
            'audio'          => (int)$this->request->getPost('audio'),
            'max_days'       => $this->nullIfEmpty($this->request->getPost('max_days')),
            'max_storage_mb' => $this->nullIfEmpty($this->request->getPost('max_storage_mb')),
            'notes'          => $this->emptyToNull(trim((string)$this->request->getPost('notes'))),
        ];
    }

    private function nullIfEmpty($v){ return ($v===null || $v==='') ? null : (int)$v; }
    private function emptyToNull($v){ return ($v==='') ? null : $v; }
}
