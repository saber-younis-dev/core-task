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
     * @param Request $request
     * @param Closure $next
     * @param string|array ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles): mixed
    {
        if (!$request->user()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Convert pipe-separated roles to array
        $rolesArray = [];
        foreach ($roles as $role) {
            $rolesArray = array_merge($rolesArray, explode('|', $role));
        }

        foreach ($rolesArray as $role) {
            if ($request->user()->hasRole($role)) {
                return $next($request);
            }
        }

        return response()->json(['message' => 'Unauthorized. Required role not found.'], 403);
    }
}
