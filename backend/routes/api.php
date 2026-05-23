<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// CONTROLADORES
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EntregaController;
use App\Http\Controllers\FabricacionController;
use App\Http\Controllers\MoldeController;
use App\Http\Controllers\ModeloController;
use App\Http\Controllers\ParteProduccionController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\PiezaController;
use App\Http\Controllers\PlanningController;
use App\Http\Controllers\ProduccionController;
use App\Http\Controllers\ProgramaDetalleController;
use App\Http\Controllers\ProgramaNecesidadController;
use App\Http\Controllers\SituacionController;
use App\Http\Controllers\UserController;


// LOGIN
Route::post('/login', [
    AuthController::class,
    'login',
]);


// RUTAS PROTEGIDAS
Route::middleware('auth:sanctum')->group(function () {

    // LOGOUT
    Route::post('/logout', [
        AuthController::class,
        'logout',
    ]);


    // USUARIO ACTUAL
    Route::get('/user', function (Request $request) {
        return $request->user();
    });


    // DASHBOARD GENERAL
    Route::middleware(
        'role:encargado,planificador,almacen,admin'
    )->get(
        '/dashboard/resumen',
        [DashboardController::class, 'resumen']
    );


    // ENCARGADO / PLANIFICADOR / ADMIN
    Route::middleware(
        'role:encargado,planificador,admin'
    )->group(function () {

        Route::patch(
            '/partes-produccion/{partes_produccion}/validar',
            [ParteProduccionController::class, 'validar']
        );

        Route::patch(
            '/partes-produccion/{partes_produccion}/corregir',
            [ParteProduccionController::class, 'corregir']
        );

        Route::apiResource(
            'partes-produccion',
            ParteProduccionController::class
        );

        Route::apiResource(
            'fabricaciones',
            FabricacionController::class
        );

        Route::apiResource(
            'piezas',
            PiezaController::class
        );

        Route::apiResource(
            'moldes',
            MoldeController::class
        );
    });


    // PLANIFICADOR / ADMIN
    Route::middleware(
        'role:planificador,admin'
    )->group(function () {

        Route::apiResource(
            'clientes',
            ClienteController::class
        );

        Route::apiResource(
            'modelos',
            ModeloController::class
        );

        Route::apiResource(
            'programas',
            ProgramaNecesidadController::class
        );

        Route::apiResource(
            'programa-detalles',
            ProgramaDetalleController::class
        );

        Route::apiResource(
            'pedidos',
            PedidoController::class
        );

        Route::get(
            '/produccion/resumen',
            [ProduccionController::class, 'resumen']
        );

        Route::get(
            '/situacion',
            [SituacionController::class, 'resumen']
        );

        Route::get(
            '/planning/semanal',
            [PlanningController::class, 'semanal']
        );

        Route::post(
            '/programas/{id}/importar',
            [ProgramaNecesidadController::class, 'importar']
        );
    });


    // ALMACÉN / PLANIFICADOR / ADMIN
    Route::middleware(
        'role:almacen,planificador,admin'
    )->group(function () {

        Route::apiResource(
            'entregas',
            EntregaController::class
        );
    });


    // ADMINISTRADOR
    Route::middleware(
        'role:admin'
    )->group(function () {

        Route::apiResource(
            'users',
            UserController::class
        );
    });
});
