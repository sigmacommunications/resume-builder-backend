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
        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
			$table->integer('user_id')->nullable();
			$table->json('question')->nullable();
            $table->json('options')->nullable();
			$table->string('type')->nullable();
			$table->string('company_name')->nullable();
			$table->string('company_logo')->nullable();
			$table->text('website_url')->nullable();
			$table->string('tamplate_title')->nullable();
			$table->string('tamplate_image')->nullable();
			$table->text('tamplate_description')->nullable();
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
        Schema::dropIfExists('surveys');
    }
};
