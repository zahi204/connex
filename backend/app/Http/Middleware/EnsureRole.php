<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        if (! $request->user() || ! in_array($request->user()->role?->value, $roles)) {
            abort(403, 'Unauthorized role.');
        }

        return $next($request);
    }
}
