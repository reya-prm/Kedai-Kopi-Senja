<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    // ...$roles nampung semua parameter dari rute: 'role:kasir,manajer' jadi ['kasir', 'manajer']
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // belum login, atau perannya nggak ada di daftar yang boleh -> tolak (403).
        // ini yang bikin orang nggak bisa nyelonong lewat ngetik URL langsung
        if (! $user || ! in_array($user->role, $roles, true)) {
            abort(403, 'Anda tidak berhak mengakses halaman ini.');
        }

        return $next($request);
    }
}