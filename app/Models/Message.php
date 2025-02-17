<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'crm_id',
        'user_id',
        'company_id',
        'contact_id',
        'deal_id',
        'receiver_user_id',
        'content',
    ];

    /**
     * Сообщение принадлежит CRM.
     */
    public function crm()
    {
        return $this->belongsTo(CRM::class, 'crm_id');
    }

    /**
     * Сообщение принадлежит автору (User).
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Если сообщение адресовано другому пользователю.
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_user_id');
    }

    /**
     * Опциональная связь с компанией.
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * Опциональная связь с контактом.
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    /**
     * Опциональная связь со сделкой.
     */
    public function deal()
    {
        return $this->belongsTo(Deal::class, 'deal_id');
    }
}
