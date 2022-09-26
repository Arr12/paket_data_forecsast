<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TTransaksiDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_transaksi_detail', function (Blueprint $table) {
            $table->id();
            $table->string('no_kwitansi');
            $table->double('grand_total', 15, 8);
            $table->enum('status', ['actived', 'deleted', 'done'])->default('actived');
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
