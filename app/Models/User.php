<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'crm_id', // Это поле связывает пользователя с CRM (tenant)
        'phone',
        'note',
        'avatar',
    ];

    // Пользователь принадлежит CRM (сущность CRM, где CRM имеет many Users).
    public function crm()
    {
        return $this->belongsTo(CRM::class, 'crm_id');
    }

    // Пользователь является ответственным за многие задания.
    public function tasks()
    {
        return $this->hasMany(Task::class, 'responsible_user_id');
    }

    /**
     * Пользователь является ответственным за многие сделки.
     */
    public function deals()
    {
        return $this->hasMany(Deal::class, 'responsible_user_id');
    }

    // Пользователь — автор заметок.
    public function notes()
    {
        return $this->hasMany(Note::class, 'user_id');
    }

    // Пользователь — автор сообщений.
    public function messages()
    {
        return $this->hasMany(Message::class, 'user_id');
    }

    // Пользователь — автор файлов.
    public function files()
    {
        return $this->hasMany(File::class, 'user_id');
    }

    // Пользователь может быть получателем сообщений.
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_user_id');
    }
}
