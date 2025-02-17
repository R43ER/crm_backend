<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDealsTable extends Migration
{
    public function up()
    {
        Schema::create('deals', function (Blueprint $table) {
            $table->id();

            // Связь с CRM (обязательная)
            $table->unsignedBigInteger('crm_id');
            $table->foreign('crm_id')
                  ->references('id')->on('crms')
                  ->onDelete('cascade');

            // Ответственный пользователь (обязательный)
            $table->unsignedBigInteger('responsible_user_id');
            $table->foreign('responsible_user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');

            // Связь с компанией (необязательная)
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')
                  ->references('id')->on('companies')
                  ->onDelete('set null');

            // Поле бюджета (числовое значение, например, с точностью до 2 знаков)
            $table->decimal('budget', 15, 2)->nullable();

            // Название сделки
            $table->string('title');

            // Статус сделки (например, "Новая", "В работе", "Закрыта" и т.д.)
            $table->string('status');

            $table->timestamps(); // created_at и updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('deals');
    }
}
