<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleManager
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Check if the user's role is in the list of allowed roles
        if (!in_array($request->user()->role, $roles)) {
            return redirect('dashboard')->with('error', 'You do not have access to this page.');
        }

        return $next($request);
    }
}
