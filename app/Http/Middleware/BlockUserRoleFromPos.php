<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockUserRoleFromPos
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && ($request->user()->role === 'user' || $request->user()->isTamu())) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin mengakses Terminal Kasir.');
        }

        return $next($request);
    }
}
