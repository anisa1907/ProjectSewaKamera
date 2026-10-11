<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        if (!$session->get('logged_in')) {
            return redirect()->to(base_url('auth/login'));
        }

        $userRole = $session->get('role');

        if (!empty($arguments) && !in_array($userRole, $arguments, true)) {
            // Arahkan ke dashboard sesuai role yang sah
            if ($userRole === 'owner') {
                return redirect()->to(base_url('owner/dashboard'))->with('error', 'Akses ditolak untuk halaman tersebut.');
            }
            if ($userRole === 'kasir') {
                return redirect()->to(base_url('kasir/dashboard'))->with('error', 'Akses ditolak untuk halaman tersebut.');
            }
            return redirect()->to(base_url('pelanggan/katalog'))->with('error', 'Akses ditolak untuk halaman tersebut.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed
    }
}
