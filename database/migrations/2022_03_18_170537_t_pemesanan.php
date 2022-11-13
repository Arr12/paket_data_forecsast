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
            $table->double('qty', 20, 2);
            $table->double('buy_price', 20, 2);
            $table->double('sell_price', 20, 2);
            $table->integer('details_id');
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
