<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Http;

class EnviarNotificacaoExpo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $token;
    public $titulo;
    public $descricao;

    public function __construct($token, $titulo, $descricao)
    {
        $this->token = $token;
        $this->titulo = $titulo;
        $this->descricao = $descricao;
    }

    public function handle(): void
    {
        Http::post('https://exp.host/--/api/v2/push/send', [
            'to' => $this->token,
            'title' => $this->titulo,
            'body' => $this->descricao,
        ]);
    }
}
