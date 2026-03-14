<?php

namespace App\Http\Controllers;

use App\Models\ProgramaNecesidad;
use Illuminate\Http\Request;

class ProgramaNecesidadController extends Controller
{
    public function index()
    {
        return ProgramaNecesidad::with('cliente')->get();
    }
}