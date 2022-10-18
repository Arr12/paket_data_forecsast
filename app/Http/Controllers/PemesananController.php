<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pemesanan;
use App\Models\DetailPemesanan;
use App\Models\DatabarangModel;
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
        $pemesanan = Pemesanan::where('id_barang', $request->id_barang)->where('faktur', null)->get();
        if(count($pemesanan) != 0){
            foreach ($pemesanan as $key => $value) {
                $qty = $value->qty + $request->qty;
                $buy_price = $request->buy_price * $qty;
            }
            $data_array = [
                'id_barang' => $request->id_barang,
                'faktur' => null,
                'qty' => $qty,
                'buy_price' => $buy_price,
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $data = Pemesanan::where('id_barang', $request->id_barang)->where('faktur', null)->update($data_array);
        }else{
            $data_array = [
                'id_barang' => $request->id_barang,
                'faktur' => null,
                'qty' => $request->qty,
                'buy_price' => $request->buy_price,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
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
        $data = Pemesanan::orderBy('created_at', 'desc')->get();
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $total = 0;
        $title = [
            "No.",
            "Name",
            "Slug",
            "Desc",
            "Opsi"
        ];
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }
        foreach ($data as $key => $value) {
            $btn = "
                <button class='btn btn-danger' type='button' id='btn_delete' data-id='$value->id'><i class='material-icons'>delete</i></button>
                <button class='btn btn-primary' type='button' id='btn_edit' data-id='$value->id' data-name='$value->name' data-slug='$value->slug' data-desc='$value->desc'><i class='material-icons'>edit</i></button>
            ";
            array_push($data_array['data'], [
                $key + 1,
                $value->name,
                $value->slug,
                $value->desc,
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
        $pemesanan_list = Pemesanan::where('status', 'actived')->where('faktur', null)->get();
        foreach ($pemesanan_list as $key => $value) {
            $buy_price = $value->buy_price/$value->qty;
            $barang = DataBarangModel::where('id', $value->id_barang)->get();
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
            'slug' => $request->slug,
            'desc' => $request->desc,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $data = Pemesanan::find($request->id);
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
        $data = Pemesanan::where('id', $request->id)->update(['status', 'deleted']);
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
            $stok = Stok::where('id_barang', $value->id_barang)->where('status', 'actived')->orderBy('id', 'desc')->get()->limit(1);
            foreach ($stok as $key => $value2) {
                $sisa = $value2->sisa;
            }
            $barang = DataBarangModel::where('id_barang', $value->id_barang)->update([
                'buy_price', $value->buy_price,
                'sell_price', $value->sell_price
            ]);
            $data_array2 = [
                'id_barang' => $value->id_barang,
                'in_stock' => $value->in_stock,
                'out_stock' => 0,
                'sisa' => $sisa,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $data_stock = Stock::create($data_array2);
            $data_array = [
                'faktur' => $request->faktur
            ];
            $data = Pemesanan::where('id_barang', $value->id_barang)->where('faktur', null)->update($data_array);
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
