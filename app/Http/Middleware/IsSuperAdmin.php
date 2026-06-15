<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
        public function handle($request, Closure $next)
    {
        if (!Auth::check() || !Auth::user()->isGlobalSuperAdmin()) {
            abort(403, 'Unauthorized – Super Admin only.');
        }
        return $next($request);
    }
}
