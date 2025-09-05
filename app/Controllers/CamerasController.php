<?php
namespace App\Controllers;

use App\Models\CamerasModel;
use App\Models\SettingsModel;
use Config\Database;

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

        $cams  = $m->orderBy('name','asc')->paginate($pageSize);
        $pager = $m->pager;

        return view('cameras/index', [
            'cameras' => $cams,
            'pager'   => $pager,
            'stats'   => sys_stats(),
            'msg'     => session()->getFlashdata('msg'),
            'err'     => session()->getFlashdata('err'),
        ]);
    }

    public function create()
    {
        if ($r = $this->guardAdmin()) return $r;

        if ($this->request->getMethod() === 'post') {
            $data = $this->validateAndCollect();
            if ($data['__ok']) {
                unset($data['__ok']);

                $db = Database::connect();
                $m  = new CamerasModel();

                try {
                    $ok = $m->insert($data);
                    if ($ok === false) {
                        // error dari Model/validation
                        $errors = $m->errors();
                        session()->setFlashdata('err', 'Save failed: '.json_encode($errors));
                        return redirect()->back()->withInput();
                    }
                    session()->setFlashdata('msg', 'New camera <b>'.esc($data['name']).'</b> added.');
                    return redirect()->to('/cameras');
                } catch (\Throwable $e) {
                    $dberr = $db->error();
                    $msg = $dberr['message'] ?? $e->getMessage();
                    session()->setFlashdata('err', 'DB error: '.$msg);
                    return redirect()->back()->withInput();
                }
            } else {
                session()->setFlashdata('err', 'Validation failed: '.json_encode($data['__errors']));
                return redirect()->back()->withInput();
            }
        }

        return view('cameras/form', [
            'mode'  => 'create',
            'errors'=> [],
            'data'  => old() ?: [],
            'stats' => sys_stats(),
            'msg'   => session()->getFlashdata('msg'),
            'err'   => session()->getFlashdata('err'),
        ]);
    }

    public function edit($id)
    {
        if ($r = $this->guardAdmin()) return $r;

        $m   = new CamerasModel();
        $cam = $m->find((int)$id);
        if (!$cam) {
            session()->setFlashdata('err', 'Camera not found.');
            return redirect()->to('/cameras');
        }

        if ($this->request->getMethod() === 'post') {
            $data = $this->validateAndCollect();
            if ($data['__ok']) {
                unset($data['__ok']);
                try {
                    if ($m->update((int)$id, $data) === false) {
                        session()->setFlashdata('err', 'Update failed: '.json_encode($m->errors()));
                        return redirect()->back()->withInput();
                    }
                    session()->setFlashdata('msg', 'Camera <b>'.esc($data['name']).'</b> updated.');
                    return redirect()->to('/cameras');
                } catch (\Throwable $e) {
                    $dberr = Database::connect()->error();
                    $msg   = $dberr['message'] ?? $e->getMessage();
                    session()->setFlashdata('err', 'DB error: '.$msg);
                    return redirect()->back()->withInput();
                }
            } else {
                session()->setFlashdata('err', 'Validation failed: '.json_encode($data['__errors']));
                return redirect()->back()->withInput();
            }
        }

        return view('cameras/form', [
            'mode'  => 'edit',
            'errors'=> [],
            'data'  => array_merge($cam, old()?:[]),
            'stats' => sys_stats(),
            'msg'   => session()->getFlashdata('msg'),
            'err'   => session()->getFlashdata('err'),
        ]);
    }

    public function delete($id)
    {
        if ($r = $this->guardAdmin()) return $r;
        (new CamerasModel())->delete((int)$id);
        session()->setFlashdata('msg', 'Camera deleted.');
        return redirect()->to('/cameras');
    }

    // =========================

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
