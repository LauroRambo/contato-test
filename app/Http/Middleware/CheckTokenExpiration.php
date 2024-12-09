<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\Auth\LoginService;
use Illuminate\Auth\AuthenticationException;

class CheckTokenExpiration
{
    protected $loginService;

    public function __construct(LoginService $loginService)
    {
        $this->loginService = $loginService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()) {
            $token = $request->bearerToken();

            if ($this->loginService->isTokenExpired($token)) {
                throw new AuthenticationException('Token expirado.');
            }
        }

        return $next($request);
    }
}
