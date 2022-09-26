<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TPemesananDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_pemesanan_detail', function (Blueprint $table) {
            $table->id();
            $table->string('faktur_number');
            $table->string('supplier_name');
            $table->double('grand_total', 15, 8);
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
