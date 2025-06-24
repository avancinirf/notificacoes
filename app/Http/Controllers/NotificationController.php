<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use App\Jobs\EnviarNotificacaoExpo;
use App\Models\UsersBase;
use App\Helpers\Utils;

class NotificationController extends Controller
{

    public function porCliente(Request $request)
    {
        ['email' => $email] = Utils::getTokenData($request->get('auth'));

        $cliente = UsersBase::where('email', $email)->firstOrFail();

        return response()->json($cliente->notifications()->latest()->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => 'required|string',
            'descricao' => 'required|string',
            'lembrar_em' => 'nullable|integer',
        ]);

        ['email' => $email, 'expo_push_token' => $token] = Utils::getTokenData($request->get('auth'));
        $cliente = UsersBase::where('email', $email)->firstOrFail();

        $notificacao = Notification::create([
            'user_id' => $cliente->id,
            'titulo' => $data['titulo'],
            'descricao' => $data['descricao'],
            'lembrar_em' => $data['lembrar_em'] ?? 0,
            'status' => 1,
            'total_envios' => 0,
            'falha_envios' => 0,
        ]);

        EnviarNotificacaoExpo::dispatch($token, $data['titulo'], $data['descricao']);

        $notificacao->increment('total_envios');

        return response()->json(['message' => 'Notificação criada com sucesso']);
    }

    public function update(Request $request, Notification $notification)
    {
        ['email' => $email] = Utils::getTokenData($request->get('auth'));

        if ($notification->user->email !== $email) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        $data = $request->validate([
            'titulo' => 'sometimes|string',
            'descricao' => 'sometimes|string',
            'status' => 'sometimes|in:0,1',
            'lembrar_em' => 'sometimes|integer',
        ]);

        $notification->update($data);

        return response()->json(['message' => 'Notificação atualizada com sucesso']);
    }

    public function destroy(Request $request, Notification $notification)
    {
        ['email' => $email] = Utils::getTokenData($request->get('auth'));

        if ($notification->user->email !== $email) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        $notification->delete();

        return response()->json(['message' => 'Notificação removida com sucesso']);
    }
    
}
