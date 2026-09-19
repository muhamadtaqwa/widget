<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        if (session()->get('user_id')) {
            return redirect()->to('/dashboard');
        }

        if ($this->request->getMethod() === 'POST') {
            $email    = $this->request->getPost('email');
            $password = $this->request->getPost('password');

            $userModel = new UserModel();
            $user = $userModel->where('email', $email)->first();

            if ($user && password_verify($password, $user['password_hash'])) {
                if ($user['status'] !== 'active') {
                    return redirect()->back()->with('error', 'Akun belum aktif.');
                }

                session()->set([
                    'user_id' => $user['id'],
                    'email'   => $user['email'],
                    'role'    => $user['role'],
                ]);

                return redirect()->to('/dashboard');
            }

            return redirect()->back()->with('error', 'Email atau password salah.');
        }

        return view('auth/login', ['title' => 'Login']);
    }

    public function register()
    {
        if (session()->get('user_id')) {
            return redirect()->to('/dashboard');
        }

        if ($this->request->getMethod() === 'POST') {
            $email    = $this->request->getPost('email');
            $password = $this->request->getPost('password');

            $userModel = new UserModel();

            if ($userModel->where('email', $email)->first()) {
                return redirect()->back()->with('error', 'Email sudah terdaftar.');
            }

            $userModel->insert([
                'email'         => $email,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'role'          => 'user',
                'status'        => 'pending',
                'journal_limit' => 0,
            ]);

            return redirect()->to('/login')->with('success', 'Registrasi berhasil. Tunggu approval admin.');
        }

        return view('auth/register', ['title' => 'Register']);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
