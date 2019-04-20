<?php

namespace App\Http\Middleware;

use Closure;

use App\Tools\TokenTools;

class TokenMiddleware
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
        $request->users_id = TokenTools::getTokenUserId($request->header('token'));
        if($request->users_id == null) {
            return response([
                'code'      => 1005,
                'message'   => '用户未登录'
            ]);
        }

        return $next($request);
    }
}
