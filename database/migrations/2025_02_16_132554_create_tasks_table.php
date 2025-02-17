<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            // Связь с CRM (обязательная)
            $table->unsignedBigInteger('crm_id');
            $table->foreign('crm_id')->references('id')->on('crms')->onDelete('cascade');

            // Ответственный пользователь (обязательный)
            $table->unsignedBigInteger('responsible_user_id');
            $table->foreign('responsible_user_id')->references('id')->on('users')->onDelete('cascade');

            // Привязка к контакту (необязательная)
            $table->unsignedBigInteger('contact_id')->nullable();
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('set null');

            // Привязка к компании (необязательная)
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');

            // Привязка к сделке (необязательная)
            $table->unsignedBigInteger('deal_id')->nullable();
            $table->foreign('deal_id')->references('id')->on('deals')->onDelete('set null');

            // Поля задания
            $table->text('task_text');             // Текст задачи
            $table->text('result')->nullable();    // Результат выполнения
            $table->string('type');                // Тип задачи
            // Поля для хранения периода исполнения
            $table->dateTime('execution_start')->nullable(); // Начало периода
            $table->dateTime('execution_end')->nullable();   // Конец периода

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
