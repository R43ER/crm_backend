<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactsTable extends Migration
{
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();

            // Обязательный внешний ключ для связи с CRM
            $table->unsignedBigInteger('crm_id');
            $table->foreign('crm_id')->references('id')->on('crms')->onDelete('cascade');

            // Необязательный внешний ключ для связи с Company
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');

            $table->string('first_name'); // Имя
            $table->string('last_name');  // Фамилия
            $table->string('phone')->nullable();   // Телефон
            $table->string('email')->nullable();     // Email
            $table->string('position')->nullable();  // Должность

            $table->timestamps();  // created_at и updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('contacts');
    }
}
