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
        Schema::create('resumes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('name');
            $table->string('email');
            $table->string('date')->nullable();
            $table->string('month')->nullable();
            $table->text('summary')->nullable();
            $table->string('address')->nullable();
            $table->text('details')->nullable();
            $table->text('skills')->nullable();
            $table->string('phone')->nullable();
            $table->string('education')->nullable();
            $table->string('degreeName')->nullable();
            $table->string('degreePlaceName')->nullable();
            $table->string('degreeYear')->nullable();
            $table->string('CertificateName')->nullable();
            $table->string('CertificatePlaceName')->nullable();
            $table->string('CertificatYear')->nullable();
            $table->string('positonName')->nullable();
            $table->string('experiencePlaceName')->nullable();
            $table->string('DateofJoining')->nullable();
            $table->string('DateofEnding')->nullable();
			$table->string('tamplate_title')->nullable();
			$table->string('tamplate_image')->nullable();
			$table->text('tamplate_description')->nullable();
            $table->text('summaryDetails')->nullable();
			$table->text('projects')->nullable();
			$table->string('type')->nullable();
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
        Schema::dropIfExists('resumes');
    }
};
