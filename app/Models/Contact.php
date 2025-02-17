<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'position',
        'company_id', // опциональная связь с компанией
        'crm_id',     // обязательная связь с CRM
    ];

    /**
     * Контакт принадлежит CRM.
     */
    public function crm()
    {
        return $this->belongsTo(CRM::class, 'crm_id');
    }

    /**
     * Контакт может принадлежать компании.
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * Контакт может быть привязан к многим задачам.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class, 'contact_id');
    }

    /**
     * Контакт может иметь множество примечаний.
     */
    public function notes()
    {
        return $this->hasMany(Note::class, 'contact_id');
    }

    /**
     * Контакт может иметь множество сообщений.
     */
    public function messages()
    {
        return $this->hasMany(Message::class, 'contact_id');
    }

    /**
     * Контакт может иметь множество файлов.
     */
    public function files()
    {
        return $this->hasMany(File::class, 'contact_id');
    }

    /**
     * Контакт может участвовать в нескольких сделках.
     * Для этой связи используется pivot-таблица, например, "contact_deal".
     */
    public function deals()
    {
        return $this->belongsToMany(Deal::class, 'contact_deal');
    }
}
