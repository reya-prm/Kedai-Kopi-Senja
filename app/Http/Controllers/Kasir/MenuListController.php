<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuListController extends Controller
{
    public function index()
    {   
        // with('resep.bahan') dibutuhin buat ngitung status Habis, tapi resepnya nggak ditampilin ke kasir
        return view('kasir.menu.index', [
            'items' => Menu::with('resep.bahan')->orderBy('nama')->get(),
        ]);
    }
}
