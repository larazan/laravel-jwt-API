<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('persons', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('rand_id');
            $table->integer('gender_id');
            $table->text('bio');
            $table->dateTime('birth_date');
            $table->string('birth_location');
            $table->string('nationality');
            $table->string('facebook');
            $table->string('instagram');
            $table->string('twitter');
            $table->string('original')->nullable();
            $table->string('large')->nullable();
            $table->string('medium')->nullable();
            $table->string('small')->nullable();
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
        Schema::dropIfExists('persons');
    }
}
