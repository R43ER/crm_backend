<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCRMIdToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Если у вас есть таблица companies, можно добавить внешний ключ:
            $table->unsignedBigInteger('crm_id')->nullable()->after('id');

            // Если требуется внешний ключ:
            // $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Если добавляли внешний ключ, сначала его нужно сбросить:
            // $table->dropForeign(['company_id']);

            $table->dropColumn('crm_id');
        });
    }
}
