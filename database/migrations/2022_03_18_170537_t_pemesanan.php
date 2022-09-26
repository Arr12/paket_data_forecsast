<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TPemesanan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_pemesanan', function (Blueprint $table) {
            $table->id();
            $table->string('id_barang');
            $table->double('qty', 15, 8);
            $table->double('buy_price', 15, 8);
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
