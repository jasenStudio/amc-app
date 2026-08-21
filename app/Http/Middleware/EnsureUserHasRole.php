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
     * Deny access to authenticated users that have no role assigned.
     * A user without role (e.g., freshly registered through the starter
     * kit) must not reach the admin area.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user !== null && $user->role === null) {
            abort(403);
        }

        return $next($request);
    }
}
