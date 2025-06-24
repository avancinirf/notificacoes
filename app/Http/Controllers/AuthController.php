<?php

namespace App\Http\Controllers;

use App\Models\UsersBase;
use Illuminate\Http\Request;
use App\Helpers\Utils;
use Firebase\JWT\JWT;
use App\Jobs\EnviarEmailCodigo;
use Illuminate\Support\Carbon;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'codigo_liberacao' => 'required|digits:6',
            'expo_push_token' => 'required|string',
        ]);

        $cliente = UsersBase::where('email', $data['email'])->first();

        if (!$cliente || $cliente->codigo_liberacao !== (string) $data['codigo_liberacao']) {
            return response()->json(['error' => 'Código de liberação inválido'], 401);
        }

        // Atualiza token e novo código (uso único)
        $cliente->update([
            'expo_push_token' => $data['expo_push_token'],
            'codigo_liberacao' => Utils::gerarCodigoLiberacao(),
            'codigo_criado_em' => now(),
        ]);

        $payload = [
            'email' => $cliente->email,
            'expo_push_token' => $cliente->expo_push_token,
            'iat' => time(),
            'exp' => time() + (config('app.jwt_ttl') * 60),
        ];

        $jwt = JWT::encode($payload, config('app.jwt_secret'), 'HS256');

        return response()->json([
            'token' => $jwt,
            'cliente' => $cliente,
        ]);
    }

    public function enviarCodigo(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
        ]);

        $cliente = UsersBase::where('email', $data['email'])->first();

        if ($cliente && $cliente->ultimo_codigo_liberacao) {
            $ultimoEnvio = Carbon::parse($cliente->ultimo_codigo_liberacao);
            $proximoEnvio = $ultimoEnvio->addHour();

            if (now()->lessThan($proximoEnvio)) {
                return response()->json([
                    'message' => 'Aguarde 1 hora para solicitar um novo código de liberação.'
                ], 429);
            }
        }

        $codigo = Utils::gerarCodigoLiberacao();

        if (!$cliente) {
            $cliente = UsersBase::create([
                'email' => $data['email'],
                'codigo_liberacao' => $codigo,
                'codigo_criado_em' => now(),
                'ultimo_codigo_liberacao' => now(),
            ]);
        } else {
            $cliente->update([
                'codigo_liberacao' => $codigo,
                'codigo_criado_em' => now(),
                'ultimo_codigo_liberacao' => now(),
            ]);
        }

        EnviarEmailCodigo::dispatch($cliente->email, $codigo);

        return response()->json(['message' => 'Código enviado com sucesso.']);
    }

}

