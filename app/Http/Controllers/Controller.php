<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\DataProviderModel;
use App\Models\DataBarangModel;
use App\Models\User;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function invoiceNumber($counter, $data, $status){
        $today = date("Ymd");
        $format = $today.'00001';
        if($counter != 0){
            foreach ($data as $key => $value) {
                $lastInvoice = $value->id;
                $lastDate = substr($lastInvoice, 0, 8);
                $lastNoUrut = substr($lastInvoice, 8, 5);
                $nextNoUrut = $lastNoUrut;
                $format = $lastDate.$nextNoUrut;
                if($status != 'before'){
                    $nextNoUrut = $lastNoUrut + 1;
                    $format = $today.sprintf('%04s', $nextNoUrut);
                }
            }
        }
        return $format;
    }
    public function ForecastingIndex(){
        $months = [
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember',
        ];
        $class = new ForecastingController;
        $now = date('Y-m-d');
        $weekly = $class->weekFromDate($now);
        foreach ($weekly as $key => $value) {
            if($key === 'startdate'){
                strtotime($now);
            }
            // dump($key);
        }
        // dd($weekly);
        return view('admin.pages.forecasting', compact('months','weekly'));
    }
    public function LaporanIndex(){
        return view('admin.pages.laporan');
    }

    public function MasterProviderIndex(){
        $get_data = route('api.forecasting.get-provider');
        $post_data = route('api.forecasting.post-provider');
        $delete_data = route('api.forecasting.delete-provider');
        return view('admin.pages.data-master.provider', compact('get_data', 'post_data', 'delete_data'));
    }
    public function DataProvider(Request $request){
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $title = [
            "No.",
            "ID",
            "Nama Provider",
            "Opsi"
        ];

        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }

        $data = DataProviderModel::where('status','=','actived')->get();
        $no = 1;
        foreach ($data as $key => $value) {
            array_push($data_array['data'], [
                $no++,
                $value->id,
                $value->name,
                "<button class='btn waves-effect btn-danger btn_delete' data-id='".$value->id."'><i class='material-icons'>delete</i></button>",
            ]);
        }
        return $data_array;
    }
    public function PostProvider(Request $request){
        $name = $request->input('name');
        $full_name = $request->input('full_name');
        $email = $request->input('email');
        $saved_data = [];
        array_push($saved_data, [
            'name' => $name,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        DataProviderModel::insert($saved_data);
        return ResponseFormater::success(null, "Success", 200);
    }
    public function DeleteProvider(Request $request){
        $id = $request->input('id');
        $saved_data = [];
        array_push($saved_data, [
            'status' => 'deleted',
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        DataProviderModel::update($saved_data)->where('id', '=', $id);
        return ResponseFormater::success(null, "Success", 200);
    }

    public function MasterBarangIndex(){
        $get_data = route('api.forecasting.get-barang');
        $post_data = route('api.forecasting.post-barang');
        $delete_data = route('api.forecasting.delete-barang');
        return view('admin.pages.data-master.barang', compact('get_data','post_data','delete_data'));
    }
    public function DataBarang(Request $request){
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $title = [
            "No.",
            "ID",
            "Nama Barang",
            "Provider",
            "Harga Beli",
            "Harga Jual",
            "Status",
            "Opsi"
        ];
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }

        $data = DataBarangModel::with(['Provider'])->where('status','=','actived')->get();
        $no = 1;
        foreach ($data as $key => $value) {
            array_push($data_array['data'], [
                $no++,
                $value->id,
                $value->name,
                $value->Provider->name,
                $value->buy_price,
                $value->sell_price,
                $value->status,
                "<button class='btn waves-effect btn-danger btn_delete' data-id='".$value->id."'><i class='material-icons'>delete</i></button>",
            ]);
        }
        return $data_array;
    }
    public function PostBarang(Request $request){
        $name = $request->input('name');
        $saved_data = [];
        array_push($saved_data, [
            'name' => $name,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        DataBarangModel::insert($saved_data);
        return ResponseFormater::success(null, "Success", 200);
    }
    public function DeleteBarang(Request $request){
        $id = $request->input('id');
        $status = 'deleted';
        DataBarangModel::update('status',$status)->where('id',$id);
        return ResponseFormater::success(null, "Success", 200);
    }

    public function MasterUserIndex(){
        return view('admin.pages.data-master.user');
    }
}
