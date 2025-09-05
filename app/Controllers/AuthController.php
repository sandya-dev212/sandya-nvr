<?php

namespace App\Controllers;

use App\Models\UsersModel;

class AuthController extends BaseController
{
    public function login()
    {
        // kalau sudah login, langsung ke dashboard
        if (session()->get('uid')) {
            return redirect()->to('/dashboard');
        }

        $method = strtoupper($this->request->getMethod());
        $data = [
            'stats'  => sys_stats(),
            'error'  => null,
            'method' => $method,   // debug: GET / POST
        ];

        if ($method === 'POST') {
            $username = trim((string)$this->request->getPost('username'));
            $password = (string)$this->request->getPost('password');

            // simple sanity debug
            if ($username === '' || $password === '') {
                $data['error'] = 'Form kosong / tidak terkirim.';
                return view('auth/login', $data);
            }

            $m = new UsersModel();
            $user = $m->where('username', $username)
                      ->where('status', 'active')
                      ->first();

            // LOCAL auth (LDAP nanti)
            if ($user && $user['type'] === 'local' && password_verify($password, $user['password_hash'])) {
                session()->set([
                    'uid'      => (int)$user['id'],
                    'uname'    => $user['username'],
                    'is_admin' => (int)$user['is_admin'],
                ]);
                session()->regenerate(true);
                return redirect()->to('/dashboard');
            }

            $data['error'] = 'Login gagal. Cek username/password.';
        }

        return view('auth/login', $data);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

    // debug: cek session
    public function whoami()
    {
        $s = session();
        return $this->response->setJSON([
            'uid'      => $s->get('uid'),
            'uname'    => $s->get('uname'),
            'is_admin' => $s->get('is_admin'),
        ]);
    }
}
