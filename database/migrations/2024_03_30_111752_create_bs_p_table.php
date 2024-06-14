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
        Schema::create('bs_p', function (Blueprint $table) {
            $table->id();  
            $table->unsignedBigInteger('idbs');
            $table->unsignedBigInteger('idp');
            $table->Integer('quantity_piece');
            $table->timestamps();
            
            $table->foreign('idbs')->references('id')->on('bons_sale');
            $table->foreign('idp')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bs_p');
    }
};
