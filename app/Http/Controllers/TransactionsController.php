<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransactionsModel;
use App\Models\TransactionDetailsModel;

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
            "Opsi"
        ];
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }
        $query = TransactionsModel::where('status', '=', 'actived')->orderBy('id', 'asc')->get();
        foreach ($query as $key => $value) {
            $total += $value->sell_price * $value->qty;
            $btn = "
                <button class='btn btn-danger' type='button' id='delete_transaction' data-id='$value->id' data-type='delete'><i class='material-icons'>delete</i></button>
                <button class='btn btn-primary' type='button' id='split_transaction' data-id='$value->id' data-type='split'><i class='material-icons'>cached</i></button>
            ";
            array_push($data_array['data'], [
                $key + 1,
                $value->name,
                $value->qty,
                $value->sell_price,
                $value->sell_price*$value->qty,
                $btn
            ]);
        }
        array_push($data_array['property'], ['total' => $total]);
        return $data_array;
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
                $value->sell_price,
                $value->sell_price*$value->qty,
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
        try {
            $saved_data = [];
            array_push($saved_data, [
                'name' => $name,
                'qty' => $qty,
                'sell_price' => $sell_price,
                'type' => $type,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            TransactionsModel::insert($saved_data);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
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
    }
    public function PostTransactionDetail(Request $request){
        $grand_total = 0;
        $flight = TransactionsModel::where([['invoice_id', '=', null], ['status', '=', 'process']])->get();
        $flight2 = TransactionDetailsModel::where('status', '=', 'process')->orderBy('id', 'desc')->limit(1)->get();
        $no_kwitansi = $this->invoiceNumber(count($flight2), $flight, null);
        foreach ($flight as $key => $value) {
            $grand_total += $value->total;
            $saved_data = [
                'no_kwitansi' => $no_kwitansi,
                'status' => 'done',
                'updated_at' => date('Y-m-d H:i:s')
            ];
            TransactionsModel::where('id', '=', $value->id)->update($saved_data);
        }
        $saved_data = [];
        array_push($saved_data, [
            "no_kwitansi" => $no_kwitansi,
            "grand_total" => $grand_total,
            "created_at" => date('Y-m-d H:i:s'),
            "updated_at" => date("Y-m-d H:i:s"),
        ]);
        TransactionDetailsModel::insert($saved_data);
    }
    public function ChartData(Request $request){
        # code...
    }
}
