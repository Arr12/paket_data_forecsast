<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransactionsModel;
use App\Models\TransactionDetailsModel;
use App\Models\DataBarangModel;
use App\Models\Stock;
use DB;

class TransactionsController extends Controller
{
    public function DataTransaction(Request $request){
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $data_array['property'] = [];
        $total = 0;
        $title = [
            "No.",
            "Nama",
            "Qty",
            "Harga",
            "Sub Total",
            "Type",
            "Opsi"
        ];
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }
        $query = TransactionsModel::where('status', '=', 'actived')->orderBy('id', 'asc')->get();
        foreach ($query as $key => $value) {
            if($value->type === 'pulsa'){
                $total += $value->sell_price;
                $sub_total = $value->sell_price;
            } else {
                $total += $value->sell_price * $value->qty;
                $sub_total = $value->sell_price * $value->qty;
            }
            $btn = "
                <button class='btn btn-danger' type='button' id='delete_transaction' data-id='$value->id' data-type='delete'><i class='material-icons'>delete</i></button>
                <button class='btn btn-primary' type='button' id='split_transaction' data-id='$value->id' data-type='split'><i class='material-icons'>cached</i></button>
            ";
            array_push($data_array['data'], [
                $key + 1,
                $value->name,
                $value->qty,
                $this->rupiah($value->sell_price),
                $this->rupiah($sub_total),
                $value->type,
                $btn
            ]);
        }
        array_push($data_array['property'], ['total' => $this->rupiah($total)]);
        return $data_array;
    }
    public function DataTransactionYear()
    {
        $query = TransactionsModel::where('status', '=', 'actived')->where(DB::raw('YEAR(created_at)'), date('Y'))->orderBy('id', 'asc')->get();
        $total = 0;
        foreach ($query as $key => $value) {
            $total += $value->sell_price * $value->qty;
        }
        return $total;
    }
    public function DataTransactionMonth()
    {
        $query = TransactionsModel::where('status', '=', 'actived')->where(DB::raw('MONTH(created_at)'), date('m'))->orderBy('id', 'asc')->get();
        $total = 0;
        foreach ($query as $key => $value) {
            $total += $value->sell_price * $value->qty;
        }
        return $total;
    }
    public function DataTransactionDay()
    {
        $query = TransactionsModel::where('status', '=', 'actived')->where(DB::raw('created_at'), date('Y-m-d'))->orderBy('id', 'asc')->get();
        $total = 0;
        foreach ($query as $key => $value) {
            $total += $value->sell_price * $value->qty;
        }
        return $total;
    }
    public function DataTransactionPerYear()
    {
        $query = TransactionsModel::where('status', '=', 'actived')->where(DB::raw('YEAR(created_at)'), date('Y'))->orderBy('id', 'asc')->get();
        $total = [
            'year' => [],
            'value' => []
        ];
        foreach ($query as $key => $value) {
            array_push($total['year'], date('Y', strtotime($value->created_at)));
            array_push($total['value'], $value->qty * $value->sell_price);
        }
        return json_encode($total);
    }
    public function DataTransactionSplit(Request $request){
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $data_array['property'] = [];
        $total = 0;
        $title = [
            "No.",
            "Nama",
            "Qty",
            "Harga",
            "Sub Total",
            "Type",
            "Opsi"
        ];
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }
        $query = TransactionsModel::where('status', '=', 'split')->orderBy('id', 'asc')->get();
        foreach ($query as $key => $value) {
            $total += $value->sell_price * $value->qty;
            $btn = "
                <button class='btn btn-danger' type='button' id='delete_transaction' data-id='$value->id' data-type='delete_back'><i class='material-icons'>delete</i></button>
                <button class='btn btn-primary' type='button' id='split_transaction' data-id='$value->id' data-type='split_back'><i class='material-icons'>cached</i></button>
            ";
            array_push($data_array['data'], [
                $key + 1,
                $value->name,
                $value->qty,
                $this->rupiah($value->sell_price),
                $this->rupiah($value->sell_price*$value->qty),
                $value->type,
                $btn
            ]);
        }
        array_push($data_array['property'], ['total' => $total]);
        return $data_array;
    }
    public function PostTransaction(Request $request){
        $type = $request->type;
        $qty = $request->qty;
        $sell_price = $request->sell_price;
        $name = $request->name;
        $barang = DataBarangModel::where('name', $name)->where('status', 'actived')->get();
        foreach ($barang as $key => $valuebarang) {
            $id_barang = $valuebarang->id;
        }
        $stock = Stock::where('id_barang', $id_barang)->where('status', 'actived')->where('type', $type)->orderBy('id', 'desc')->limit(1)->get();
        // dd($stock);
        // jika jumlah stock adalah tersedia maka ambil nilai sisa
        $sisa = 0;
        if(count($stock) != 0){
            foreach ($stock as $key => $valuestock) {
                $sisa = $valuestock->sisa;
            }
        }
        if($sisa <= 0){
            return response()->json(['meta' => [
                'status' => 'failure_stock',
                'message' => null
            ]], 200);
        }
        $sisa = $sisa - $qty;
        $saved_data = [];
        array_push($saved_data, [
            'name' => $name,
            'qty' => $qty,
            'sell_price' => $sell_price,
            'type' => $type,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        $data_array2 = [
            'id_barang' => $id_barang,
            'in_stock' => 0,
            'out_stock' => $qty,
            'sisa' => $sisa,
            'type' => $type,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        TransactionsModel::insert($saved_data);
        Stock::create($data_array2);
        return response()->json(['meta' => [
            'status' => 'success',
            'message' => null
        ]], 200);
    }
    public function DeleteTransaction(Request $request){
        $id = $request->id;
        try {
            $saved_data = [
                'status' => 'deleted',
                'updated_at' => date('Y-m-d H:i:s')
            ];
            TransactionsModel::where('id', '=', $id)->update($saved_data);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
    public function UpdateTransaction(Request $request){
        $id = $request->id;
        if($request->type === 'split'){
            $split = 'split';
        }
        else{
            $split = 'actived';
        }
        try {
            $saved_data = [
                'status' => $split,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            TransactionsModel::where('id', '=', $id)->update($saved_data);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
    public function DataReport(Request $request){
        $firstDate = $request->input('firstDate');
        $lastDate = $request->input('lastDate');
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $title = [
            "No.",
            "Nama",
            "Qty",
            "Harga",
            "Sub Total",
            "Type",
            "Tanggal Buat",
        ];
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }
        $query = TransactionsModel::whereBetween('created_at', [$firstDate, $lastDate])->where('status', 'done')->get();
        foreach ($query as $key => $value) {
            if($value->type === 'pulsa'){
                $total = $value->sell_price;
            } else {
                $total = $value->sell_price * $value->qty;
            }
            array_push($data_array['data'], [
                $key + 1,
                $value->name,
                $value->qty,
                $this->rupiah($value->sell_price),
                $this->rupiah($total),
                $value->type,
                date('Y-m-d', strtotime($value->created_at)),
            ]);
        }
        return $data_array;
    }
    public function PostTransactionDetail(Request $request){
        $grand_total = 0;
        $flight = TransactionsModel::where([['no_kwitansi', '=', null], ['status', '=', 'actived']])->get();
        $flight2 = TransactionDetailsModel::where('status', '=', 'actived')->orderBy('id', 'desc')->limit(1)->get();
        $no_kwitansi = $this->invoiceNumber(count($flight2), $flight2, null);
        // dd($flight, $flight2, $no_kwitansi);
        foreach ($flight as $key => $value) {
            $grand_total += $value->sell_price * $value->qty;
            $saved_data = [
                'no_kwitansi' => 'CELL-' . $no_kwitansi,
                'status' => 'done',
                'updated_at' => date('Y-m-d H:i:s')
            ];
            TransactionsModel::where('name', '=', $value->name)->where('status', 'actived')->update($saved_data);
        }
        // dd($grand_total, $saved_data);
        $saved_data = [];
        array_push($saved_data, [
            "no_kwitansi" => 'CELL-'.$no_kwitansi,
            "grand_total" => $grand_total,
            "created_at" => date('Y-m-d H:i:s'),
            "updated_at" => date("Y-m-d H:i:s"),
        ]);
        $data = TransactionDetailsModel::insert($saved_data);
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
    public function ChartData(Request $request){
        # code...
    }
}
