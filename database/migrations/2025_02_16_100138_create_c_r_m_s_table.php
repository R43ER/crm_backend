<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCrmsTable extends Migration
{
    /**
     * Запуск миграции.
     */
    public function up()
    {
        Schema::create('crms', function (Blueprint $table) {
            $table->id();
            $table->string('name');              // Название CRM
            $table->string('subdomain')->unique(); // URL поддомена (например, mycompany.gavrelets.ru)
            $table->string('avatar')->nullable();  // Аватар CRM (путь или URL)
            $table->string('website')->nullable(); // Ссылка на сайт CRM
            $table->timestamps();                // Дата создания и обновления (created_at и updated_at)
        });
    }

    /**
     * Откат миграции.
     */
    public function down()
    {
        Schema::dropIfExists('crms');
    }
}
