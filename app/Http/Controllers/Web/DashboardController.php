<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    public function ujiManajer()
    {
        return 'Area Manajer';
    }

    public function ujiKasir()
    {
        return 'Area Kasir';
    }
}
