<?php

namespace FI\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Session\Store;

class AuthenticateAdmin
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * The Store implementation.
     *
     * @var Store
     */
    protected $session;

    /**
     * Create a new filter instance.
     *
     * @param  Guard $auth
     * @return void
     */
    public function __construct(Guard $auth, Store $session)
    {
        $this->auth    = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->guest() or session()->has('thirdpartyauth') or ($this->auth->check() and $this->auth->user()->client_id != 0))
        {
            if ($request->ajax())
            {
                return response('Unauthorized.', 401);
            }
            else
            {
                session()->flush();
                
                return redirect()->route('session.login');
            }
        }

        return $next($request);
    }
}
