<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PastikanPeran
{
    //...$peran buat nampung semua paramenter dari rute, misal 'peran:kasir,manajer' jadi ['kasir', 'manajer']
    public function handle(Request $request, Closure $next, string  ...$peran): Response
    {   
        $user = $request->user();

        //kalo belum login/perannya gaada bakal di tolak: (403)
        if (! $user || !in_array($user->peran, $peran, true)) {
            abort(403, 'Anda tidak dapat mengakses halaman ini.');
        }
        
        return $next($request); 
    }
}
