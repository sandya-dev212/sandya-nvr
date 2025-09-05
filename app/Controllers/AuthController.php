<?php

namespace App\Controllers;

use App\Models\UsersModel;

class AuthController extends BaseController
{
    public function login()
    {
        // kalau sudah login, langsung lempar ke dashboard
        if (session()->get('uid')) {
            return redirect()->to('/dashboard');
        }

        if ($this->request->getMethod() === 'post') {
            $username = trim((string)$this->request->getPost('username'));
            $password = (string)$this->request->getPost('password');

            $m = new UsersModel();
            $user = $m->where('username', $username)
                      ->where('status', 'active')
                      ->first();

            // LOCAL auth (LDAP nanti)
            if ($user && $user['type'] === 'local' && password_verify($password, $user['password_hash'])) {
                // set session
                session()->set([
                    'uid'      => (int)$user['id'],
                    'uname'    => $user['username'],
                    'is_admin' => (int)$user['is_admin'],
                ]);
                session()->regenerate(true);

                return redirect()->to('/dashboard');
            }

            // TODO: LDAP branch di sini (nanti)
            return redirect()->back()->with('error', 'Login gagal');
        }

        return view('auth/login', ['stats' => sys_stats()]);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

    // debug helper
    public function whoami()
    {
        $s = session();
        return $this->response->setJSON([
            'uid'   => $s->get('uid'),
            'uname' => $s->get('uname'),
            'is_admin' => $s->get('is_admin'),
        ]);
    }
}
