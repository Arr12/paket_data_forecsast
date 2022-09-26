<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionDetailsModel extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 't_transaksi_detail';
}
