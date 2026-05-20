<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->two_factor_enabled && !$request->session()->get('two_factor_verified')) {
            if (
                !$request->routeIs('two-factor.verify') &&
                !$request->routeIs('two-factor.verify.post') &&
                !$request->routeIs('two-factor.backup') &&        // ← agregado
                !$request->routeIs('two-factor.backup.post')      // ← agregado
            ) {
                return redirect()->route('two-factor.verify');
            }
        }

        return $next($request);
    }
}