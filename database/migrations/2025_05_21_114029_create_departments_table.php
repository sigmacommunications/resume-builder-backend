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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('company_id');
            $table->string('department_name')->nullable();
            $table->string('deparment_type')->nullable();
            $table->string('lead_full_name')->nullable();
            $table->string('lead_email_address')->nullable();
            $table->string('lead_contact_number')->nullable();
            $table->integer('number_of_employees_in_depart')->nullable();
            $table->timestamps();
			$table->foreign('company_id')->references('id')->on('company')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('departments');
    }
};
