<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;

class JwtAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $authorization = $request->bearerToken();

        if (!$authorization) {
            return response()->json(['error' => 'Token ausente'], 401);
        }

        try {
            $payload = JWT::decode($authorization, new Key(config('app.jwt_secret'), 'HS256'));
        } catch (ExpiredException $e) {
            return response()->json(['error' => 'Token expirado'], 401);
        } catch (SignatureInvalidException|BeforeValidException|\UnexpectedValueException $e) {
            return response()->json(['error' => 'Token inválido'], 401);
        }

        // Compartilhar payload com a request
        $request->merge(['auth' => (array) $payload]);

        return $next($request);
    }
}
