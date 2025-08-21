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
        Schema::create('career_blogs', function (Blueprint $table) {
            $table->id();
			$table->integer('user_id')->nullable();
			$table->string('logo')->nullable();
            $table->string('image')->nullable();
			$table->string('tamplate_image')->nullable();
            $table->string('company_name')->nullable();
            $table->string('designation')->nullable();
            $table->text('description')->nullable();
			$table->text('tamplate_description')->nullable();
            $table->string('heading')->nullable();
            $table->string('name')->nullable();
			$table->string('tamplate_title')->nullable();
			$table->string('type')->nullable();
			$table->string('website_url')->nullable();
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
        Schema::dropIfExists('career_blogs');
    }
};
