<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cover_letters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('name')->nullable();
            $table->string('email');
            $table->string('month')->nullable();
            $table->string('date')->nullable();
            $table->longtext('summary')->nullable();
            $table->longtext('address')->nullable();
            $table->longtext('details')->nullable();
            $table->string('phone')->nullable();
            $table->string('managerName')->nullable();
            $table->string('companyName')->nullable();
            $table->longtext('companyAddress')->nullable();
            $table->string('subject')->nullable();
			$table->string('image')->nullable();
			$table->string('designation')->nullable();
			$table->string('tamplate_title')->nullable();
			$table->string('tamplate_image')->nullable();
			$table->text('tamplate_description')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cover_letters');
    }
};
