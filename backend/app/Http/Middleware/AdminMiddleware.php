<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * HANDLE
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // NO AUTENTICADO
        if (!$user) {
            return response()->json([
                'message' => 'No autenticado',
            ], 401);
        }

        // NO ADMIN
        if ($user->role !== 'admin') {
            return response()->json([
                'message' => 'Acceso denegado',
            ], 403);
        }

        return $next($request);
    }
}
