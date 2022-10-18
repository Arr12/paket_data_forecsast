<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemesanan extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 't_pemesanan';

    public function details()
    {
        return $this->belongsTo(DetailPemesanan::class, 'details_id');
    }
}
