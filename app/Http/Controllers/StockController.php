<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\DatabarangModel;
use App\Models\DataProviderModel;
use Illuminate\Http\Request;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data_array2 = [
            'id_barang' => $request->id_barang,
            'in_stock' => $request->in_stock,
            'out_stock' => $request->out_stock,
            'sisa' => $request->sisa,
            'type' => $request->type,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $data_stock = Stock::create($data_array2);

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

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Stock  $stock
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $barang = DataBarangModel::where('status', 'actived')->orderBy('id', 'desc')->get();
        $provider = DataProviderModel::where('status', 'actived')->orderBy('id', 'desc')->get();
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $title = [
            "No.",
            "Name",
            "In",
            "Out",
            "Type",
            "Total",
        ];
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }
        foreach ($barang as $key => $value) {
            $in_stock = 0;
            $out_stock = 0;
            $total = 0;
            $data = Stock::where('status', 'actived')->where('type', 'paket_data')->where('id_barang', $value->id)->orderBy('id', 'desc')->get();
            if(count($data) != 0){
                foreach ($data as $key2 => $value2) {
                    // $stock += ($value->sisa - $value->out_stock);
                    if($value2->in_stock != 0){
                        $in_stock += $value2->in_stock;
                    } else {
                        $out_stock += $value2->out_stock;
                    }
                    $total = $in_stock - $out_stock;
                }
                array_push($data_array['data'], [
                    $key + 1,
                    $value->name,
                    $in_stock,
                    $out_stock,
                    'Paket Data',
                    $total
                ]);
            }
        }
        foreach ($provider as $key => $value) {
            $data = Stock::where('status', 'actived')->where('type', 'pulsa')->where('id_barang', $value->id)->orderBy('id', 'desc')->get();
            if(count($data) != 0){
                foreach ($data as $key2 => $value2) {
                    // $stock += ($value->sisa - $value->out_stock);
                    if($value2->in_stock != 0){
                        $in_stock += $value2->in_stock;
                    } else {
                        $out_stock += $value2->out_stock;
                    }
                    $total = $in_stock - $out_stock;
                }
                array_push($data_array['data'], [
                    $key + 1,
                    $value->name,
                    $in_stock,
                    $out_stock,
                    'Pulsa',
                    $total
                ]);
            }
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
            'data' => $data_array
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Stock  $stock
     * @return \Illuminate\Http\Response
     */
    public function edit(Stock $stock)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Stock  $stock
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Stock $stock)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Stock  $stock
     * @return \Illuminate\Http\Response
     */
    public function destroy(Stock $stock)
    {
        //
    }
}
