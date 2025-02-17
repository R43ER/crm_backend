<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'crm_id',
        'responsible_user_id',
        'contact_id',
        'company_id',
        'deal_id',
        'task_text',
        'result',
        'type',
        'execution_start', // добавлено
        'execution_end',   // добавлено
    ];

    // Остальные связи остаются без изменений

    public function crm()
    {
        return $this->belongsTo(CRM::class, 'crm_id');
    }

    public function responsibleUser()
    {
        return $this->belongsTo(User::class, 'responsible_user_id');
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function deal()
    {
        return $this->belongsTo(Deal::class, 'deal_id');
    }
}
