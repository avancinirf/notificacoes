<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\UsersBase;

class EnviarEmailCodigo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $email;
    public $codigo;

    public function __construct(string $email, string $codigo)
    {
        $this->email = $email;
        $this->codigo = $codigo;
    }

    public function handle(): void
    {
        try {
            Mail::raw("Seu código de liberação é: {$this->codigo}", function ($msg) {
                $msg->to($this->email)->subject('Código de Liberação');
            });

            $entregue = true;
        } catch (\Throwable $e) {
            $entregue = false;
        }

        try {
            $user = UsersBase::where('email', $this->email)->first();

            if (!$user) {
                Log::warning("Usuário não encontrado para email: {$this->email}");
                return;
            }

            DB::table('email_logs')->insert([
                'user_id'      => $user->id,
                'destinatario' => $this->email,
                'assunto'      => 'Código de Liberação',
                'conteudo'     => "Seu código de liberação é: {$this->codigo}",
                'entregue'     => $entregue,
                'enviado_em'   => now(),
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            Log::info("EmailLog gravado com sucesso para: {$this->email}");
        } catch (\Throwable $e) {
            file_put_contents(
                storage_path('logs/email_log_erro.log'),
                '[' . now() . '] ' . $e->getMessage() . "\n",
                FILE_APPEND
            );
        }
    }
}
