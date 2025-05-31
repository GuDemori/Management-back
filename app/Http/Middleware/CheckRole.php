<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();
    
        // dd([
        //     'user_id' => $user?->id,
        //     'user_role' => $user?->role,
        //     'roles_permitidas' => $roles
        // ]);
    
        if (!$user || !in_array($user->role, $roles)) {
            return response()->json(['message' => 'Acesso não autorizado.'], 403);
        }
    
        return $next($request);
    }
    
}
