<?php

namespace IP\Http\Middleware;

use Closure;
use IP\Modules\Users\Models\User;

class AuthenticateAPI
{
    /**
     * Run the request filter.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $key = $request->input('key');

        if ($request->getPathInfo() !== $request->input('endpoint')) {
            return response()->json(['Unauthorized.'], 401);
        }

        $user = User::where('api_public_key', $key)->first();

        if (!$user) {
            return response()->json(['Unauthorized.'], 401);
        }

        $signature = $request->input('signature');
        $content = $request->except('key', 'signature', 'page', 'status');
        $content['timestamp'] = $content['timestamp'];

        $serverSignature = hash_hmac('sha256', json_encode($content), $user->api_secret_key);

        if ($signature !== $serverSignature) {
            return response()->json(['Unauthorized.'], 401);
        }

        return $next($request);
    }
}