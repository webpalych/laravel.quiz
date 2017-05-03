<?php

namespace App\Http\Middleware;

use Closure;
use Gate;

class AdminAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if(Gate::denies('admin_access')) {
            return response('Unauthorized.', 403);
        }

        return $next($request);
    }
}
