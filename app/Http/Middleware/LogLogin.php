<?php
namespace App\Http\Middleware;

use Closure;
use App\Models\LoginHistory;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Agent;

class LogLogin
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        if (Auth::check() && $request->isMethod('post') && $request->routeIs('login')) {
            $agent = new Agent();
            $device = $agent->device() . ' - ' . $agent->platform() . ' ' . $agent->version($agent->platform());
            LoginHistory::create([
                'user_id' => Auth::id(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'device' => $device,
                'successful' => true,
                'login_at' => now(),
            ]);
        }
        return $response;
    }
}