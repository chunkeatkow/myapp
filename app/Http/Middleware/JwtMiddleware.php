<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->header('Authorization')){
            try {
                $jwt = $request->header('Authorization');
                //validate token
                $request->headers->set('Authorization', 'Bearer '.$request->header('Authorization'));
                $user = JWTAuth::parseToken()->authenticate();

                //decode token
                $token = JWTAuth::setToken($jwt);
                $apy = JWTAuth::getPayload($token)->toArray();
                $request->merge(['username' => $apy['uname']]);
                $request->merge(['lang' => $user->preferred_language_code]);
                $request->merge(['user_id' => $user->id]);
                $request->merge(['mobile_no' => $user->mobile_no]);
            } catch (Exception $e) {
                if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                    return response()->json([
                        'rst' => "0",
                        'msg'  => "token_invalid"
                    ], 401);
                }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                    return response()->json([
                        'rst' => "0",
                        'msg'  => "token_expired"
                    ], 401);
                }else{
                    return response()->json([
                        'rst' => "0",
                        'msg'  => "authorization_not_provided"
                    ], 401);
                }
            }
        }else{
            return response()->json([
                'rst' => "0",
                'msg'  => "authorization_not_provided"
            ], 401);
        }

        return $next($request);
    }
}
