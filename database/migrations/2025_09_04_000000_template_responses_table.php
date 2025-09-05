<?php 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    
    public function up(): void {
        Schema::create('template_responses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('template_assign_id');
            $table->enum('response_type', ['text', 'image', 'document', 'signature'])->nullable();
            $table->text('response_value')->nullable(); // text ya file path
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('template_assign_id')->references('id')->on('template_assigns')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('template_responses');
    }
};
