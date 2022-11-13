<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TTransaksi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_transaksi', function (Blueprint $table) {
            $table->id();
            $table->string('no_kwitansi', 30)->nullable();
            $table->string('name');
            $table->double('qty', 20, 2);
            $table->double('sell_price', 20, 2);
            $table->enum('type', ['pulsa', 'paket_data'])->default('pulsa');
            $table->enum('status', ['actived', 'deleted', 'split', 'done'])->default('actived');
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
