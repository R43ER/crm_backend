<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'crm_id',
        'name',
        'phone',
        'email',
        'web',
        'address',
    ];

    /**
     * Компания принадлежит CRM.
     */
    public function crm()
    {
        return $this->belongsTo(CRM::class, 'crm_id');
    }

    /**
     * Компания имеет множество контактов.
     */
    public function contacts()
    {
        return $this->hasMany(Contact::class, 'company_id');
    }

    /**
     * Компания имеет множество заданий.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class, 'company_id');
    }

    /**
     * Компания имеет множество сделок.
     */
    public function deals()
    {
        return $this->hasMany(Deal::class, 'company_id');
    }

    /**
     * Компания имеет множество примечаний.
     */
    public function notes()
    {
        return $this->hasMany(Note::class, 'company_id');
    }

    /**
     * Компания имеет множество сообщений.
     */
    public function messages()
    {
        return $this->hasMany(Message::class, 'company_id');
    }

    /**
     * Компания имеет множество файлов.
     */
    public function files()
    {
        return $this->hasMany(File::class, 'company_id');
    }
}
