<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ClearanceMiddleware {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {     
    
        if (Auth::user()->hasPermissionTo('Admin')) {
            return $next($request);
        }
        if ($request->is('posts/create')) {
            if (!Auth::user()->hasPermissionTo('Edit Admin')) {
                abort('401');
            } else {
                return $next($request);
            }
        }
            
        if ($request->is('posts/*/edit')) {
            if (!Auth::user()->hasPermissionTo('Dueño Clan')) {
                abort('401');
            } else {
                return $next($request);
            }
        }

        if ($request->isMethod('Delete')) {
            if (!Auth::user()->hasPermissionTo('Integrante Clan')) {
                abort('401');
            } else {
                return $next($request);
            }
        }

        return $next($request);
    }
}