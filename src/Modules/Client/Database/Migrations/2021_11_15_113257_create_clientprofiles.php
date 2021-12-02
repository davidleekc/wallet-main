<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientprofiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clientprofiles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('client_id');

            $table->string('first_name');
            $table->string('last_name');
            $table->string('country')->nullable();
            $table->string('identity_type')->nullable();
            $table->string('identity_no')->nullable();
            $table->string('email')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clientprofiles');
    }
}
