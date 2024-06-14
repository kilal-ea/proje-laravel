<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->decimal('price', 8, 2);
        $table->unsignedBigInteger('supplier_id'); 
        $table->unsignedBigInteger('idstock');
        $table->integer('quantity');  
        $table->integer('qtt_piece_in_carton');

        $table->foreign('idstock')->references('id')->on('stocks');
        $table->foreign('supplier_id')->references('id')->on('suppliers');
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('products');
}

};
