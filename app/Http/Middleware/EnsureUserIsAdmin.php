<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, ['admin_organizador', 'super_admin'], true)) {
            if ($request->expectsJson()) {
                abort(403, 'Acesso restrito à administração.');
            }

            return redirect()
                ->guest(route('login'))
                ->withErrors(['email' => 'Faça login com uma conta administrativa para continuar.']);
        }

        return $next($request);
    }
}
