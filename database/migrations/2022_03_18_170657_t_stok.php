<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TStok extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_stok', function (Blueprint $table) {
            $table->id();
            $table->string('id_barang');
            $table->double('in_stock', 15, 8);
            $table->double('out_stock', 15, 8);
            $table->double('sisa', 15, 8);
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
