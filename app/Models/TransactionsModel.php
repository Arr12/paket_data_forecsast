<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionsModel extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 't_transaksi';

    public function Provider(){
        return $this->belongsTo(TransactionDetailsModel::class, "no_kwitansi", "id");
    }
}
