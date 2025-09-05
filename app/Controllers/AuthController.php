<?php
namespace App\Controllers;
use App\Models\UsersModel;

class AuthController extends BaseController
{
    public function login()
    {
        if ($this->request->getMethod()==='post') {
            $username = trim($this->request->getPost('username'));
            $password = (string)$this->request->getPost('password');
            $m = new UsersModel();
            $user = $m->where('username',$username)->first();

            if ($user && $user['type']==='local' && password_verify($password, $user['password_hash'])) {
                session()->set(['uid'=>$user['id'],'uname'=>$user['username'],'is_admin'=>(int)$user['is_admin']]);
                return redirect()->to('/dashboard');
            }
            // TODO: LDAP branch nanti
            return redirect()->back()->with('error','Login gagal');
        }
        return view('auth/login');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

    public function profile()
    {
        // placeholder
        return view('profile/index');
    }
}
