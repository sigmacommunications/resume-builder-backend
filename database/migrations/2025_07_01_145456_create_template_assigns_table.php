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
        Schema::create('template_assigns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
			$table->unsignedBigInteger('assignable_id');
            $table->boolean('form')->default(false);
			$table->string('assignable_type'); 
            $table->timestamps();
			
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('template_assigns');
    }
};
