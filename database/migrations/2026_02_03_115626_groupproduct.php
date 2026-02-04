<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
    {
    public function up(): void
    {
    Schema::create("group_products", function (Blueprint $table) {
        $table->id(); 
        $table->string("title");
        $table->boolean("status")->default(true);
        $table->unsignedBigInteger("parent_id")->nullable(); 
        $table->timestamps();
        
        $table->foreign('parent_id')->references('id')->on('group_products')->onDelete('cascade');
    });
}
    
    public function down(): void
    {
        Schema::dropIfExists("group_products");
    }
};
    