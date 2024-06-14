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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('code');  
            $table->unsignedBigInteger('ids'); 
            $table->unsignedBigInteger('idc');
            $table->decimal('priceTotal');  
            $table->timestamps();
            
            $table->foreign('ids')->references('id')->on('users');
            $table->foreign('idc')->references('id')->on('clients');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales');
    }
};
