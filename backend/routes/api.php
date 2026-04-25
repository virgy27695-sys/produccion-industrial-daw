<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// IMPORTACIÓN DE CONTROLADORES
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ModeloController;
use App\Http\Controllers\PiezaController;
use App\Http\Controllers\MoldeController;
use App\Http\Controllers\ProgramaNecesidadController;
use App\Http\Controllers\ProgramaDetalleController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ProduccionController; 
use App\Http\Controllers\SituacionController;


/*
|--------------------------------------------------------------------------
| API ROUTES
|--------------------------------------------------------------------------
| Aquí se definen todas las rutas de la API del sistema.
| Cada recurso sigue una estructura REST (index, store, show, update, destroy).
|--------------------------------------------------------------------------
*/


// CLIENTES
// Gestión de clientes del sistema
Route::apiResource('clientes', ClienteController::class);


// MODELOS
// Cada modelo pertenece a un cliente
Route::apiResource('modelos', ModeloController::class);


// PIEZAS
// Piezas fabricadas asociadas a modelos y moldes
Route::apiResource('piezas', PiezaController::class);


// MOLDES
// Moldes de producción (clave en entorno industrial)
Route::apiResource('moldes', MoldeController::class);


// PROGRAMAS DE NECESIDADES
// Programas enviados por clientes con necesidades semanales
Route::apiResource('programas', ProgramaNecesidadController::class);


// DETALLE DE PROGRAMAS
// Líneas de cada programa (pieza, semana, cantidad)
Route::apiResource('programa-detalles', ProgramaDetalleController::class);


// PEDIDOS
// Pedidos generados a partir de programas
Route::apiResource('pedidos', PedidoController::class);


// PRODUCCIÓN
// Endpoint de planificación industrial
// Agrupa necesidades por molde y semana
// Devuelve carga de producción real
Route::get('/produccion/resumen', [ProduccionController::class, 'resumen']);



// USUARIO AUTENTICADO 
// Devuelve datos del usuario autenticado
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// SITUACIÓN REAL DE PRODUCCIÓN
// Endpoint para obtener resumen de situación real por pieza
// Incluye necesidades, producción, entregas, stock y estado
Route::get('/situacion', [SituacionController::class, 'resumen']);
