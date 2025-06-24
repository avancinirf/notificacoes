<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\EmailLog;
use App\Models\Notification;

class UsersBase extends Model
{
    protected $table = 'users_base';

    protected $fillable = [
        'email',
        'codigo_liberacao',
        'codigo_criado_em',
        'expo_push_token',
        'ultimo_codigo_liberacao',
    ];

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function emailLogs()
    {
        return $this->hasMany(EmailLog::class, 'user_id');
    }
}

