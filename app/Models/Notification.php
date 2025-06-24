<?php

namespace App\Models;

use App\Models\UsersBase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'titulo',
        'descricao',
        'status',
        'lembrar_em',
        'total_envios',
        'falha_envios',
    ];

    public function user()
    {
        return $this->belongsTo(UsersBase::class, 'user_id');
    }
}

