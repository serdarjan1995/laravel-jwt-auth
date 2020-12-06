<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWTAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $tokenError = true;
        $tokenStatus = null;
        $errorMessage = null;
        try {
            JWTAuth::parseToken()->authenticate();
            $tokenError = false;
        }
        catch (TokenBlacklistedException $e){
            $tokenStatus = "Blacklisted";
            $errorMessage = $e->getMessage();
        }
        catch (TokenExpiredException $e){
            if(url()->current() === url()->route('refresh')){
                return $next($request);
            }
            else{
                $tokenStatus = "Expired";
                $errorMessage = $e->getMessage();
            }
        }
        catch (TokenInvalidException $e){
            $tokenStatus = "Invalid";
            $errorMessage = $e->getMessage();
        }
        catch (JWTException $e){
            $tokenStatus = "JWTException";
            $errorMessage = $e->getMessage();
        }
        catch (AuthenticationException $e){
            $tokenStatus = "AuthenticationException";
            $errorMessage = $e->getMessage();
        } finally {
            if($tokenError){
                return response()->json([
                    "status" => JsonResponse::$statusTexts[JsonResponse::HTTP_UNAUTHORIZED],
                    "error" => $tokenError,
                    "tokenStatus" => $tokenStatus,
                    "message" => $errorMessage,
                ], JsonResponse::HTTP_UNAUTHORIZED)
                    ->header("Locale", app()->getLocale());
            }
        }

        return $next($request);
    }
}
