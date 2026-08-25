<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfPending
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user instanceof User
            && $user->role === UserRole::Pending
            && ! $request->routeIs('pending.approval', 'logout')) {
            return redirect()->route('pending.approval');
        }

        return $next($request);
    }
}
