<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware untuk memvalidasi Role / Hak Akses pengguna.
 * Mencegah pengguna yang tidak memiliki role yang sesuai mengakses rute-rute khusus (seperti admin dashboard).
 */
class CheckRole
{
    /**
     * Menangani request HTTP masuk untuk pengecekan role pengguna.
     *
     * @param  Request  $request  Request HTTP masuk
     * @param  Closure(Request): (Response)  $next  Callback untuk melanjutkan request jika sukses
     * @param  string  $role  Nama role yang diwajibkan (misal: 'admin')
     * @return Response Response HTTP hasil penanganan
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Redirect ke halaman login jika user belum login
        if (! auth()->check()) {
            return redirect('/login');
        }

        // Jika role user tidak sama dengan role yang diwajibkan, alihkan ke POS masuk dengan flash error
        if (auth()->user()->role !== $role) {
            return redirect('/pos/entry')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
