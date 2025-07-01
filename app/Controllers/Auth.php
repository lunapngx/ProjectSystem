<?php

// app/Controllers/Auth.php
namespace App\Controllers;
use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        $data['redirect'] = $this->request->getGet('redirect') ?? '/products';
        if ($this->request->getMethod() === 'post') {
            $u = (new UserModel())
                ->where('email',$this->request->getPost('email'))
                ->first();
            if ($u && password_verify($this->request->getPost('password'), $u['password_hash'])) {
                session()->set('user_id',$u['id']);
                return redirect()->to($data['redirect']);
            }
            $data['error'] = 'Invalid credentials.';
        }
        return view('auth/login', $data);
    }

    public function register()
    {
        if ($this->request->getMethod() === 'post') {
            $email = $this->request->getPost('email');
            $pass  = $this->request->getPost('password');
            // basic validation…
            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $data['error']='Invalid email.';
            }
            elseif (strlen($pass)<6) {
                $data['error']='Password too short.';
            } else {
                $um = new UserModel();
                try {
                    $um->insert([
                        'email'         => $email,
                        'password_hash' => password_hash($pass,PASSWORD_DEFAULT)
                    ]);
                    session()->set('user_id',$um->getInsertID());
                    return redirect()->to('/products');
                } catch (\Exception $e) {
                    $data['error']='Email already used.';
                }
            }
        }
        return view('auth/register', $data ?? []);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}

{

}