<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->session()->get('user_role') !== 'admin') {
            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Solo el administrador puede realizar esta acción.',
                ], 403);
            }

            return redirect()->route('pos.index')->with('error', 'Solo el administrador puede realizar esta acción.');
        }

        return $next($request);
    }
}
