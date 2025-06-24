<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    protected $fillable = [
        'user_id',
        'destinatario',
        'assunto',
        'conteudo',
        'entregue',
        'enviado_em',
    ];

    public function user()
    {
        return $this->belongsTo(UsersBase::class, 'user_id');
    }
}

