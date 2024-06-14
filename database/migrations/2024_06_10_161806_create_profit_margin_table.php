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
        Schema::create('profit_margin', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idp');
            $table->integer('Quantity_before_sell')->nullable();
            $table->integer('Quantity_sell')->nullable();
            $table->integer('Total_purchase_price')->nullable();
            $table->integer('Total_sell_price')->nullable();
            $table->integer('profit_margin')->nullable();
        
            $table->foreign('idp')->references('id')->on('products');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profit_margin');
    }
};
