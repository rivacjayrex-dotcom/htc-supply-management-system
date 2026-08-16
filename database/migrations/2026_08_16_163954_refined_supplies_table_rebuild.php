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
        Schema::create('supplies', function (Blueprint $table) {
            $table->id();
            $table->string('item_name')->unique();
            $table->string('brand');
            $table->string('model_number')->nullable();
            $table->string('category'); // Office, IT, Lab, Furniture, Janitorial
            $table->text('specifications'); // Technical details
            $table->integer('quantity')->default(0);
            $table->string('unit'); // Ream, Box, Piece, etc.
            $table->decimal('unit_price', 15, 2);
            $table->integer('min_stock_level')->default(5);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
