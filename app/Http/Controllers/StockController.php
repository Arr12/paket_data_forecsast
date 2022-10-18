<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\DatabarangModel;
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
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $title = [
            "No.",
            "Name",
            "In",
            "Out",
        ];
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }
        $in_stock = 0;
        $out_stock = 0;
        foreach ($barang as $key => $value) {
            $data = Stock::where('status', 'actived')->where('id_barang', $value->id_barang)->orderBy('id', 'desc')->get();
            foreach ($data as $key => $value) {
                $stock += ($value->sisa - $value->out_stock);
                if($value->in_stock != 0){
                    $in_stock += $value->in_stock;
                } else {
                    $out_stock += $value->out_stock;
                }
            }
            array_push($data_array['data'], [
                $key + 1,
                $value->name,
                $in_stock,
                $out_stock
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
            'data' => $data
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
