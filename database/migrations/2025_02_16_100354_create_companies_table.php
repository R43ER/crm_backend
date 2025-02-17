<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            // Внешний ключ для связи с CRM (таблица crms)
            $table->unsignedBigInteger('crm_id');
            $table->foreign('crm_id')->references('id')->on('crms')->onDelete('cascade');

            $table->string('name');         // Название компании
            $table->string('phone')->nullable();   // Телефон компании
            $table->string('email')->unique(); // Email компании (уникальный)
            $table->string('web')->nullable();       // Веб-сайт
            $table->string('address')->nullable();   // Адрес
            $table->timestamps();          // created_at и updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('companies');
    }
}
