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
        Schema::create('company', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('company_name')->nullable();
            $table->string('business_type')->nullable();
            $table->string('industry')->nullable();
            $table->string('company_number')->nullable();
            $table->string('business_email_address')->nullable();
            $table->string('business_phone_number')->nullable();
            $table->string('website_url')->nullable();
            $table->text('company_address')->nullable();
            $table->string('company_logo')->nullable();
            $table->integer('number_of_employees')->nullable();
            $table->string('tax_identification_number')->nullable();
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
        Schema::dropIfExists('company');
    }
};
