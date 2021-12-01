<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->increments('id');
            $table->string('phone');
            $table->integer('pin')->nullable();
            $table->integer('otp')->nullable();
            $table->dateTime('otp_expired', $precision = 0)->nullable();
            $table->boolean('otp_is_valid')->nullable();
            $table->string('question_id')->nullable();
            $table->string('question_answer')->nullable();
            $table->string('secret_key', 100)->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('clients');
    }
}
