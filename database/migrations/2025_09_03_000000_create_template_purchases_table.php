<?php 

// database/migrations/2025_09_03_000000_create_template_purchases_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    
    public function up(): void {
        Schema::create('template_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('template_id')->constrained('templates')->cascadeOnDelete();
            $table->unsignedBigInteger('category_id')->nullable(); // optional, helpful for filters
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('currency', 10)->default('PKR');
            $table->enum('status', ['pending','completed','failed','refunded'])->default('pending');
            $table->string('payment_method')->nullable();      // e.g. stripe, jazzcash
            $table->string('transaction_id')->nullable()->index(); // gateway txn id
            $table->json('meta')->nullable();                  // gateway raw payload
            $table->timestamps();

            $table->unique(['user_id','template_id'], 'user_template_unique'); // prevent duplicate buys
        });
    }

    public function down(): void {
        Schema::dropIfExists('template_purchases');
    }
};
