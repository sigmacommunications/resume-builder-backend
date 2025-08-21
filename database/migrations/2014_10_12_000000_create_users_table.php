<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->integer('department_id')->nullable();
			$table->string('name')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('email_code')->nullable();
            $table->string('password');
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
            // $table->string('dob')->nullable();
            // $table->string('gender')->nullable();
            $table->string('photo')->nullable();
			$table->string('designation')->nullable();
            $table->text('device_token')->nullable();
			$table->string('joining_date')->nullable();
			$table->string('salary')->nullable();
//            $table->enum('role',['admin','user'])->default('user');
            $table->enum('status',['active','inactive'])->default('active');
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
        Schema::dropIfExists('users');
    }
};
