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
        // pagination size dari settings (default 10)
        $pageSize = 10;
        $s = new SettingsModel();
        $row = $s->find('dashboard_pagination');
        if ($row && is_numeric($row['value'])) $pageSize = (int)$row['value'];

        $cameras = $m->orderBy('name','asc')->paginate($pageSize);
        $pager   = $m->pager;

        return view('cameras/index', [
            'cameras' => $cameras,
            'pager'   => $pager,
            'stats'   => sys_stats(),
        ]);
    }

    public function create()
    {
        if ($r = $this->guardAdmin()) return $r;

        if ($this->request->getMethod() === 'post') {
            $data = $this->validateAndCollect();
            if ($data['__ok']) {
                unset($data['__ok']);
                $m = new CamerasModel();
                if (!$m->insert($data)) {
                    return view('cameras/form', [
                        'mode' => 'create',
                        'errors' => $m->errors(),
                        'data' => $this->request->getPost(),
                        'stats'=> sys_stats(),
                    ]);
                }
                return redirect()->to('/cameras');
            }
            return view('cameras/form', [
                'mode' => 'create',
                'errors' => $data['__errors'],
                'data' => $this->request->getPost(),
                'stats'=> sys_stats(),
            ]);
        }

        return view('cameras/form', [
            'mode' => 'create',
            'errors' => [],
            'data' => [],
            'stats'=> sys_stats(),
        ]);
    }

    public function edit($id)
    {
        if ($r = $this->guardAdmin()) return $r;

        $m = new CamerasModel();
        $cam = $m->find($id);
        if (!$cam) return redirect()->to('/cameras');

        if ($this->request->getMethod() === 'post') {
            $data = $this->validateAndCollect();
            if ($data['__ok']) {
                unset($data['__ok']);
                if (!$m->update($id, $data)) {
                    return view('cameras/form', [
                        'mode' => 'edit',
                        'errors' => $m->errors(),
                        'data' => array_merge($cam, $this->request->getPost()),
                        'stats'=> sys_stats(),
                    ]);
                }
                return redirect()->to('/cameras');
            }
            return view('cameras/form', [
                'mode' => 'edit',
                'errors' => $data['__errors'],
                'data' => array_merge($cam, $this->request->getPost()),
                'stats'=> sys_stats(),
            ]);
        }

        return view('cameras/form', [
            'mode' => 'edit',
            'errors' => [],
            'data' => $cam,
            'stats'=> sys_stats(),
        ]);
    }

    public function delete($id)
    {
        if ($r = $this->guardAdmin()) return $r;
        $m = new CamerasModel();
        $m->delete((int)$id);
        return redirect()->to('/cameras');
    }

    // --- helpers ---
    private function validateAndCollect(): array
    {
        $rules = [
            'name'           => 'required|min_length[2]|max_length[120]',
            'rtsp_url'       => 'required|min_length[6]',
            'mode'           => 'required|in_list[recording,watch,disabled]',
            'fps'            => 'permit_empty|integer|greater_than_equal_to[1]|less_than_equal_to[60]',
            'audio'          => 'permit_empty|in_list[0,1]',
            'transport'      => 'required|in_list[tcp,udp]',
            'port'           => 'permit_empty|integer|greater_than_equal_to[1]|less_than_equal_to[65535]',
            'max_days'       => 'permit_empty|integer|greater_than_equal_to[0]',
            'max_storage_mb' => 'permit_empty|integer|greater_than_equal_to[0]',
            'storage_path'   => 'permit_empty|max_length[255]',
            'notes'          => 'permit_empty|max_length[2000]',
        ];

        if (!$this->validate($rules)) {
            return ['__ok'=>false, '__errors'=>$this->validator->getErrors()];
        }

        return [
            '__ok' => true,
            'name'           => trim($this->request->getPost('name')),
            'rtsp_url'       => trim($this->request->getPost('rtsp_url')),
            'mode'           => $this->request->getPost('mode'),
            'fps'            => $this->nullIfEmpty($this->request->getPost('fps')),
            'audio'          => (int)($this->request->getPost('audio') ?? 1),
            'transport'      => $this->request->getPost('transport'),
            'port'           => $this->nullIfEmpty($this->request->getPost('port')),
            'max_days'       => $this->nullIfEmpty($this->request->getPost('max_days')),
            'max_storage_mb' => $this->nullIfEmpty($this->request->getPost('max_storage_mb')),
            'storage_path'   => $this->emptyToNull(trim((string)$this->request->getPost('storage_path'))),
            'notes'          => $this->emptyToNull(trim((string)$this->request->getPost('notes'))),
        ];
    }

    private function nullIfEmpty($v) { return ($v === '' || $v === null) ? null : (int)$v; }
    private function emptyToNull($v) { return ($v === '') ? null : $v; }
}
