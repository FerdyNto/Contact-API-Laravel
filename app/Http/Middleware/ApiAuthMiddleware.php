<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // cek token apakah ada di header response
        $token = $request->header('Authorization');
        $authenticate = true;

        // cek apakah ada token yg diterima
        if (!$token) {
            $authenticate = false;
        }

        // cek token user dari database
        $user = User::where('token', $token)->first();
        if (!$user) {
            $authenticate = false;
        } else {
            Auth::login($user);
        }



        if ($authenticate) {
            return $next($request);
        } else {
            return response()->json([
                'errors' => [
                    'message' => [
                        'unauthorized'
                    ]
                ]
            ])->setStatusCode(401);
        }
    }
}
