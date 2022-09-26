<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MBarang extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_barang', function (Blueprint $table) {
            $table->id();
            $table->integer('id_provider');
            $table->string('name');
            $table->double('buy_price', 15, 8);
            $table->double('sell_price', 15, 8);
            $table->enum('status', ['actived', 'deleted'])->default('actived');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
