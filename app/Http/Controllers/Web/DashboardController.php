<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    public function testManajer()
    {
        return 'Area Manajer';
    }

    public function testKasir()
    {
        return 'Area Kasir';
    }
}