<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\LoginHistory;
use Illuminate\Support\Facades\Auth;

class LogLogin
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if (Auth::check() && $request->isMethod('post') && $request->routeIs('login')) {
            $userAgent = $request->userAgent();
            $device = $this->parseDevice($userAgent);

            LoginHistory::create([
                'user_id'     => Auth::id(),
                'ip_address'  => $request->ip(),
                'user_agent'  => $userAgent,
                'device'      => $device,
                'successful'  => true,
                'login_at'    => now(),
            ]);
        }

        return $response;
    }

    /**
     * Simple device parser – no external library needed.
     */
    private function parseDevice($userAgent)
    {
        if (str_contains($userAgent, 'Windows')) return 'Windows';
        if (str_contains($userAgent, 'Mac')) return 'Mac';
        if (str_contains($userAgent, 'Linux')) return 'Linux';
        if (str_contains($userAgent, 'Android')) return 'Android';
        if (str_contains($userAgent, 'iPhone') || str_contains($userAgent, 'iPad')) return 'iOS';
        return 'Unknown';
    }
}
