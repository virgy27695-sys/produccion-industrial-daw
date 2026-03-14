<?php

namespace App\Http\Controllers;

use App\Models\Pieza;
use Illuminate\Http\Request;

class PiezaController extends Controller
{
    public function index()
    {
        return Pieza::with('modelo.cliente')->get();
    }
}