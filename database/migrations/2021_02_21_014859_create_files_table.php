<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            $table->string('filename');
            $table->string('location');
            $table->enum('status', ['En espera', 'Procesando', 'Fallido', 'Terminado'])->default('En espera');
            $table->string('field_name')->default('name');
            $table->string('field_birthday')->default('birthday');
            $table->string('field_phone')->default('phone');
            $table->string('field_address')->default('address');
            $table->string('field_credit_card_number')->default('credit_card_number');
            $table->string('field_email')->default('email');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
}
