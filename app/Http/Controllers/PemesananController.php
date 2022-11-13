<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pemesanan;
use App\Models\DetailPemesanan;
use App\Models\DatabarangModel;
use App\Models\DataProviderModel;
use App\Models\Stock;

class PemesananController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return false;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = Pemesanan::pluck('name', 'name')->all();
        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Not found'
            ], 400);
        }
        return response()->json(['meta' => [
            'status' => 'success',
            'message' => null
        ], 'data' => $data], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $type = $request->type;
        $buy_price = $request->buy_price;
        $pemesanan = Pemesanan::where('name', $request->name)->where('faktur', null)->where('status', 'actived')->get();
        // jika pemesanan berjumlah tidak sama dengan 0
        if(count($pemesanan) != 0){
            foreach ($pemesanan as $key => $value) {
                $qty = $value->qty + $request->qty;
                //jika harga beli baru > dari harga beli lama
                if($request->buy_price > $value->buy_price){
                    $buy_price = $request->buy_price * $qty;
                }
            }
            $data_array = [
                'name' => $request->name,
                'faktur' => null,
                'qty' => $qty,
                'buy_price' => $buy_price,
                'sell_price' => $request->sell_price,
                'updated_at' => date('Y-m-d H:i:s'),
                'type' => $type
            ];
            $data = Pemesanan::where('name', $request->name)->where('faktur', null)->update($data_array);
        }else{
            $data_array = [
                'name' => $request->name,
                'faktur' => null,
                'qty' => $request->qty,
                'buy_price' => $request->buy_price,
                'sell_price' => $request->sell_price,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'type' => $type
            ];
            $data = Pemesanan::create($data_array);
        }


        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Not found'
            ], 400);
        }

        return response()->json(['meta' => [
            'status' => 'success',
            'message' => null
        ]], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $data = Pemesanan::where('faktur', null)->where('status', 'actived')->orderBy('created_at', 'desc')->get();
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $total = 0;
        $title = [
            "No.",
            "Name",
            "Qty",
            "Harga Beli",
            "Harga Jual",
            "Type",
            "Opsi"
        ];
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }
        foreach ($data as $key => $value) {
            $btn = "
                <button class='btn btn-danger' type='button' id='btn_delete_pemesanan' data-id='$value->id'><i class='material-icons'>delete</i></button>
                <button class='btn btn-primary' type='button' id='btn_edit_pemesanan' data-id='$value->id' data-qty='$value->qty' data-name='$value->name' data-buy_price='$value->buy_price' data-sell_price='$value->sell_price' data-type='$value->type'><i class='material-icons'>edit</i></button>
            ";
            array_push($data_array['data'], [
                $key + 1,
                $value->name,
                $value->qty,
                $value->buy_price,
                $value->sell_price,
                $value->type,
                $btn
            ]);
        }
        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Not found'
            ], 400);
        }
        return response()->json([
            'meta' => [
                'status' => 'success',
                'message' => null
            ],
            'data' => $data,
            'data_array' => $data_array
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Pemesanan::where(['id', '=', $id])->get();
        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Not found'
            ], 400);
        }
        return response()->json(['meta' => [
            'status' => 'success',
            'message' => null
        ], 'data' => $data], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $type = $request->type;
        $pemesanan_list = Pemesanan::where('status', 'actived')->where('faktur', null)->get();
        foreach ($pemesanan_list as $key => $value) {
            $buy_price = $value->buy_price/$value->qty;
            $barang = DataBarangModel::where('name', $value->name)->where('status', 'actived')->get();
            foreach ($barang as $key => $value2) {
                if($buy_price > $value2->sell_price){
                    $barang->update([
                        'sell_price' => $buy_price
                    ]);
                }
            }
        }
        $input = [
            'name' => $request->name,
            'faktur' => null,
            'qty' => $request->qty,
            'buy_price' => $request->buy_price,
            'sell_price' => $request->sell_price,
            'updated_at' => date('Y-m-d H:i:s'),
            'type' => $type
        ];
        $data = Pemesanan::where('id', $request->id);
        $data->update($input);
        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Not found'
            ], 400);
        }
        return response()->json(['meta' => [
            'status' => 'success',
            'message' => null
        ], 'data' => $data], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $data = Pemesanan::where('id', $request->id);
        $input = [
            'status' => 'deleted',
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $data->update($input);
        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Not found'
            ], 400);
        }
        return response()->json(['meta' => [
            'status' => 'success',
            'message' => null
        ]], 200);
    }

    public function done(Request $request)
    {
        $pemesanan = Pemesanan::where('faktur', null)->get();
        foreach ($pemesanan as $key => $value) {
            if($value->type === 'pulsa'){
                $barang = DataProviderModel::where('name', $value->name)->where('status', 'actived')->orderBy('id', 'desc')->get();
                foreach ($barang as $key => $value2) {
                    $id_barang = $value2->id;
                    $buy_price = $value2->buy_price;
                    $sell_price = $value2->sell_price;
                }
            } else {
                $barang = DataBarangModel::with('stock')->where('name', $value->name)->where('status', 'actived')->orderBy('id', 'desc')->get();
                foreach ($barang as $key => $value2) {
                    $id_barang = $value2->id;
                    $buy_price = $value2->buy_price;
                    $sell_price = $value2->sell_price;
                }
                $barang = DataBarangModel::where('id', $id_barang)->where('status', 'actived');
                $input = [
                    'buy_price' => $buy_price,
                    'sell_price' => $sell_price
                ];
                $barang->update($input);
            }
            $sisa = 0;
            $stock = Stock::where('id_barang', $id_barang)->where('status', 'actived')->where('type', $value->type)->orderBy('id', 'desc')->limit(1)->get();
            foreach ($stock as $key => $value2) {
                $sisa = $value2->sisa;
            }
            $sisa = $sisa + $value->qty;
            $data_array2 = [
                'id_barang' => $id_barang,
                'in_stock' => $value->qty,
                'out_stock' => 0,
                'sisa' => $sisa,
                'type' => $value->type,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $data_stock = Stock::create($data_array2);
            $data_array = [
                'faktur' => $request->faktur
            ];
            $data = Pemesanan::where('name', $value->name)->where('faktur', null)->where('status', 'actived')->update($data_array);
        }
        if (!$data && !$data_stock) {
            return response()->json([
                'success' => false,
                'message' => 'Not found'
            ], 400);
        }

        return response()->json(['meta' => [
            'status' => 'success',
            'message' => null
        ]], 200);
    }
}
