<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();

            // Обязательная связь с CRM
            $table->unsignedBigInteger('crm_id');
            $table->foreign('crm_id')->references('id')->on('crms')->onDelete('cascade');

            // Обязательная связь с автором (User)
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Опциональные связи
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');

            $table->unsignedBigInteger('contact_id')->nullable();
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('set null');

            $table->unsignedBigInteger('deal_id')->nullable();
            $table->foreign('deal_id')->references('id')->on('deals')->onDelete('set null');

            $table->unsignedBigInteger('note_id')->nullable();
            $table->foreign('note_id')->references('id')->on('notes')->onDelete('set null');

            // Информация о файле
            $table->string('file_path'); // Путь или URL к файлу
            $table->string('file_name'); // Имя файла

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('files');
    }
}
