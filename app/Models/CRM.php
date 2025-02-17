<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CRM extends Model
{
    use HasFactory;

    protected $table = 'crms';

    protected $fillable = [
        'name',
        'subdomain',
        'avatar',
        'website',
    ];

    /**
     * CRM имеет множество пользователей.
     * В нашей системе у пользователя поле `company_id` ссылается на CRM.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'company_id');
    }

    /**
     * CRM имеет множество компаний.
     * В модели Company внешний ключ – `crm_id`.
     */
    public function companies()
    {
        return $this->hasMany(Company::class, 'crm_id');
    }

    /**
     * CRM имеет множество контактов.
     * В модели Contact внешний ключ – `crm_id`.
     */
    public function contacts()
    {
        return $this->hasMany(Contact::class, 'crm_id');
    }

    /**
     * CRM имеет множество заданий.
     * В модели Task внешний ключ – `crm_id`.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class, 'crm_id');
    }

    /**
     * CRM имеет множество сделок.
     * В модели Deal внешний ключ – `crm_id`.
     */
    public function deals()
    {
        return $this->hasMany(Deal::class, 'crm_id');
    }

    /**
     * CRM имеет множество примечаний.
     * В модели Note внешний ключ – `crm_id`.
     */
    public function notes()
    {
        return $this->hasMany(Note::class, 'crm_id');
    }

    /**
     * CRM имеет множество сообщений.
     * В модели Message внешний ключ – `crm_id`.
     */
    public function messages()
    {
        return $this->hasMany(Message::class, 'crm_id');
    }

    /**
     * CRM имеет множество файлов.
     * В модели File внешний ключ – `crm_id`.
     */
    public function files()
    {
        return $this->hasMany(File::class, 'crm_id');
    }
}
