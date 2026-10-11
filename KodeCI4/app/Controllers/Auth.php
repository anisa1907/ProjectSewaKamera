<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Auth extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function login()
    {
        if (session()->get('logged_in')) {
            return $this->redirectByRole(session()->get('role'));
        }

        return view('auth/login', [
            'title' => 'Login - Sistem Sewa Kamera & Drone',
        ]);
    }

    public function processLogin()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        if (empty($username) || empty($password)) {
            return redirect()->back()->withInput()->with('error', 'Username dan password wajib diisi.');
        }

        $user = $this->userModel->where('username', $username)->first();

        if (!$user) {
            return redirect()->back()->withInput()->with('error', 'Pengguna tidak ditemukan.');
        }

        // Cek hash password atau plaintext fallback
        $passwordValid = password_verify($password, $user['password']) || ($password === $user['password']);

        if (!$passwordValid) {
            return redirect()->back()->withInput()->with('error', 'Password salah.');
        }

        // Set session
        session()->set([
            'id_user'   => $user['id_user'],
            'nama_user' => $user['nama_user'],
            'username'  => $user['username'],
            'role'      => $user['role'],
            'logged_in' => true,
        ]);

        return $this->redirectByRole($user['role']);
    }

    public function register()
    {
        if (session()->get('logged_in')) {
            return $this->redirectByRole(session()->get('role'));
        }

        return view('auth/register', [
            'title' => 'Daftar Akun Pelanggan - Sewa Kamera',
        ]);
    }

    public function processRegister()
    {
        $rules = [
            'nama_user' => 'required|min_length[3]|max_length[100]',
            'username'  => 'required|min_length[3]|max_length[50]|is_unique[user.username]',
            'password'  => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->userModel->insert([
            'nama_user' => $this->request->getPost('nama_user'),
            'username'  => $this->request->getPost('username'),
            'password'  => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'role'      => 'pelanggan',
        ]);

        return redirect()->to(base_url('auth/login'))->with('success', 'Pendaftaran berhasil! Silakan login.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('auth/login'))->with('success', 'Anda telah berhasil keluar.');
    }

    private function redirectByRole($role)
    {
        switch ($role) {
            case 'owner':
                return redirect()->to(base_url('owner/dashboard'));
            case 'kasir':
                return redirect()->to(base_url('kasir/dashboard'));
            case 'pelanggan':
            default:
                return redirect()->to(base_url('pelanggan/katalog'));
        }
    }
}
