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
        Schema::create('mails', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
			$table->integer('template_id')->nullable();
            $table->string('name')->nullable();
            $table->string('email');
            $table->string('date')->nullable();
            $table->string('managerName')->nullable();
            $table->string('subject')->nullable();
            $table->longtext('summary')->nullable();
            $table->longtext('details')->nullable();
			$table->string('type')->nullable();
			$table->string('companyName')->nullable();
			$table->text('companyAddress')->nullable();
			$table->string('website_url')->nullable();
			$table->string('tamplate_title')->nullable();
			$table->string('tamplate_image')->nullable();
			$table->string('tamplate_description')->nullable();
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
        Schema::dropIfExists('mails');
    }
};
