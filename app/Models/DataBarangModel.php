<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataBarangModel extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = "m_barang";

    public function Provider(){
        return $this->belongsTo(DataProviderModel::class, "id_provider", "id");
    }
    public function stock(){
        return $this->belongsTo(DataBarangModel::class, "id_barang", "id");
    }
}
