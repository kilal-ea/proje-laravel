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
        Schema::create('bt_p', function (Blueprint $table) {
            $table->id();  
            $table->unsignedBigInteger('idbt');
            $table->unsignedBigInteger('idp');
            $table->Integer('quantity_piece');
            $table->timestamps();
            
            $table->foreign('idbt')->references('id')->on('bons_transaction');
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
        Schema::dropIfExists('bt_p');
    }
};
