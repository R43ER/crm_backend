<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{
    use HasFactory;

    protected $fillable = [
        'crm_id',
        'responsible_user_id',
        'company_id',
        'budget',
        'title',
        'status',
    ];

    /**
     * Сделка принадлежит CRM.
     */
    public function crm()
    {
        return $this->belongsTo(CRM::class, 'crm_id');
    }

    /**
     * Сделка имеет ответственного пользователя.
     */
    public function responsibleUser()
    {
        return $this->belongsTo(User::class, 'responsible_user_id');
    }

    /**
     * Сделка может принадлежать компании (опционально).
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * Сделка связана с контактами (многие ко многим).
     */
    public function contacts()
    {
        return $this->belongsToMany(Contact::class, 'contact_deal');
    }

    /**
     * Сделка имеет множество примечаний.
     */
    public function notes()
    {
        return $this->hasMany(Note::class, 'deal_id');
    }

    /**
     * Сделка имеет множество сообщений.
     */
    public function messages()
    {
        return $this->hasMany(Message::class, 'deal_id');
    }

    /**
     * Сделка имеет множество файлов.
     */
    public function files()
    {
        return $this->hasMany(File::class, 'deal_id');
    }
}
