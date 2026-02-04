<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('image');
            $table->text('description');
            $table->json('size')->nullable();   // Changed to json for array
            $table->json('color')->nullable();  // Changed to json for array
            $table->decimal('price', 10, 2);
            $table->string('category');
            $table->json('rating')->nullable(); // For rate and count
            $table->json('group_product_id')->nullable(); // Changed to json for array
            $table->timestamps();

            // Note: In MongoDB, foreign keys are mostly symbolic unless handled by the driver
            // but we keep it for consistency with the user's migration style.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
