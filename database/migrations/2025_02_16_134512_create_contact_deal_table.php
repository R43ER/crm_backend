<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactDealTable extends Migration
{
    public function up()
    {
        Schema::create('contact_deal', function (Blueprint $table) {
            $table->unsignedBigInteger('contact_id');
            $table->unsignedBigInteger('deal_id');

            // Определяем внешний ключ для contact_id:
            $table->foreign('contact_id')
                  ->references('id')->on('contacts')
                  ->onDelete('cascade');

            // Определяем внешний ключ для deal_id:
            $table->foreign('deal_id')
                  ->references('id')->on('deals')
                  ->onDelete('cascade');

            // Можно установить составной первичный ключ:
            $table->primary(['contact_id', 'deal_id']);

            // Если нужно отслеживать время создания записи, можно добавить:
            // $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('contact_deal');
    }
}
