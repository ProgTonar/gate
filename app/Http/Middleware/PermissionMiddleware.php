<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Exceptions\RoleDeniedException;

class PermissionMiddleware
{
    public function handle($request, Closure $next, $permission, $guard = null)
    {
        $authGuard = Auth::guard($guard);

        $user = $authGuard->user();

        if (!$user) {
            return response()->json(['message' => 'Пользователь не авторизован']);
        }

        $permissions = is_array($permission)
            ? $permission
            : explode('|', $permission);

        if (!$user->canAny($permissions)) {
            return response()->json(['message' => 'У пользователя не хватает прав доступа']);
        }

        return $next($request);
    }
}
