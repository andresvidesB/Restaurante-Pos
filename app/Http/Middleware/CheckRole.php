<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
 public function handle(Request $request, Closure $next, ...$roles): Response
{
    // Si el usuario no está logueado o su rol no está en la lista permitida
    if (! $request->user() || ! in_array($request->user()->role, $roles)) {
        // En lugar de un 403 seco, podemos redirigir al home
        return redirect('/')->with('error', 'No tienes permiso para acceder a esta sección.');
    }

    return $next($request);
}
}
