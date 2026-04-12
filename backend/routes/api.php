<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ModeloController;
use App\Http\Controllers\PiezaController;
use App\Http\Controllers\ProgramaNecesidadController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\DetallePedidoController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::apiResource('clientes', ClienteController::class);
Route::apiResource('modelos', ModeloController::class);
Route::apiResource('piezas', PiezaController::class);
Route::apiResource('programas', ProgramaNecesidadController::class);
Route::apiResource('pedidos', PedidoController::class);
Route::apiResource('detalle-pedidos', DetallePedidoController::class);
