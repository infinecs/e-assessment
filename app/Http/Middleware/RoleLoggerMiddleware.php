<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;

class RoleLoggerMiddleware
{
    public function handle($request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        // Save every request to database
        ActivityLog::create([
            'user_id' => $user->id ?? null,
            'email' => $user->email ?? 'guest',
            'role' => $user->roles ?? 'guest',
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
        ]);

        // Role restrictions
        if (!empty($roles)) {
            if (!$user) {
                return redirect('/login');
            }

            if (!in_array($user->roles, $roles)) {
                ActivityLog::create([
                    'user_id' => $user->id ?? null,
                    'email' => $user->email ?? 'guest',
                    'role' => $user->roles ?? 'guest',
                    'method' => $request->method(),
                    'url' => $request->fullUrl(),
                    'ip' => $request->ip(),
                ]);

                abort(403, 'Unauthorized');
            }
        }

        return $next($request);
    }
}
