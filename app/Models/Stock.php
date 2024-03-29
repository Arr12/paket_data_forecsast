<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 't_stok';

    public function barang(Type $var = null)
    {
        $this->belongsTo(DataBarangModel::class, 'id_barang');
    }
}
