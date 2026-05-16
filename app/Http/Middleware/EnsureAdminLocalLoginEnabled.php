<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminLocalLoginEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('admin/login*')) {
            $isLocal = app()->environment('local');
            $isEnabled = (bool) config('auth.admin_local_login_enabled', false);

            if (! $isLocal || ! $isEnabled) {
                abort(404);
            }
        }

        return $next($request);
    }
}
