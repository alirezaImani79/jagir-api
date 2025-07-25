<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! auth('sanctum')->user()?->hasAnyOfRoles($roles)) {
            return response()->json([
                'status' => 'UNAUTHORIZED'
            ], 403);
        }

        return $next($request);
    }
}
