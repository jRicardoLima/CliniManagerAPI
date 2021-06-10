<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyTokenApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $valueToken = $request->header('authorization');

        if ($valueToken != null && $valueToken != '') {
            $idToken = strstr(strstr($valueToken, " ", false), "|", true);
            $tokenIsValid = false;

            foreach (auth()->user()->tokens as $token) {
                if (trim($idToken) == $token->id) {
                    $tokenIsValid = true;
                    break;
                }
            }

            if (!$tokenIsValid) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'status' => 'unauthorized',
                        'message' => 'Credenciais invalidas',
                        'data' => ''
                    ], 401);
                } else {
                    return response("<h1><strong>unauthorized</strong></h1>", 401);
                }

            }

        } else {

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'unauthorized',
                    'message' => 'Credenciais invalidas',
                    'data' => ''
                ], 401);
            } else {
                return response("<h1><strong>unauthorized</strong></h1>", 401);
            }

        }

        return $next($request);
    }
}
