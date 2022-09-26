<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataBarangModel;
use App\Models\Stock;

class ForecastingController extends Controller
{
    public function weekFromDate($date)
    {
        // $textdt = date($date.'-01');
        $thisday = $date . '-d';
        $textdt = date($thisday, strtotime('first Week'));
        $textdt = date('Y-m-01', strtotime($textdt));
        // dd($textdt);
        $dt = strtotime($textdt);
        $currdt = $dt;
        $prevdt = date('Y-m-01', strtotime(date($thisday) . '-1 month'));
        $textprevdt = date($prevdt, strtotime('first Week'));
        $textprevdt = date('Y-m-d', strtotime($textprevdt));
        $prevdt = strtotime($textprevdt);
        $currmonth = strtotime($textprevdt . "+1 month");
        $nextmonth = strtotime($textdt . "+1 month");
        $i = 0;
        $date = [
            'c_week' => [],
            'startdate' => [],
            'startdate_prev' => [],
            'daystart' => [],
            'enddate' => [],
            'enddate_prev' => [],
            'dayend' => [],
        ];

        $endarr[$i] = strtotime(date("Y-m-d", $prevdt));
        do {
            $weekday = date("w", $prevdt);
            $endday = abs($weekday - 7);
            $startarr[$i] = $prevdt;
            $endarr[$i] = strtotime(date("Y-m-d", $prevdt) . "+$endday day");
            $prevdt = strtotime(date("Y-m-d", $endarr[$i]) . "+1 day");
            array_push($date["startdate_prev"], date("Y-m-d", $startarr[$i]));
            array_push($date["enddate_prev"], date("Y-m-d", $endarr[$i]));
            $i++;
        } while ($endarr[$i - 1] < $currmonth);

        $i = 0;
        $j = 0;
        $currdt = strtotime($date['startdate_prev'][count($date['startdate_prev']) - 1]);
        $endarr[$i] = strtotime(date("Y-m-d", $currdt));
        do {
            $weekday = date("w", $currdt);
            $endday = abs($weekday - 7);
            $startarr[$i] = $currdt;
            $endarr[$i] = strtotime(date("Y-m-d", $currdt) . "+$endday day");
            // dump($endarr[$i]);
            $currdt = strtotime(date("Y-m-d", $endarr[$i]) . "+1 day");
            if ($i == 0) {
                array_push($date['c_week'], "Week " . ($j + 1));
                array_push($date["startdate"], $date['startdate_prev'][count($date['startdate_prev']) - 1]);
                array_push($date["daystart"], date("D", strtotime($date['startdate_prev'][count($date['startdate_prev']) - 1])));
                array_push($date["enddate"], $date['enddate_prev'][count($date['enddate_prev']) - 1]);
                array_push($date["dayend"], date("D", strtotime($date['enddate_prev'][count($date['enddate_prev']) - 1])));
                $j++;
            } else {
                array_push($date['c_week'], "Week " . ($j + 1));
                array_push($date["startdate"], date("Y-m-d", $startarr[$i]));
                array_push($date["daystart"], date("D", $startarr[$i]));
                array_push($date["enddate"], date("Y-m-d", $endarr[$i]));
                array_push($date["dayend"], date("D", $endarr[$i]));
                $j++;
            }
            $i++;
        } while ($endarr[$i - 1] < $nextmonth);
        $tail = count($date['startdate']) - 1;
        unset($date['c_week'][$tail]);
        unset($date['startdate'][$tail]);
        unset($date['daystart'][$tail]);
        unset($date['enddate'][$tail]);
        unset($date['dayend'][$tail]);
        $date['c_week'] = array_values($date['c_week']);
        $date['startdate'] = array_values($date['startdate']);
        $date['daystart'] = array_values($date['daystart']);
        $date['enddate'] = array_values($date['enddate']);
        $date['dayend'] = array_values($date['dayend']);
        return $date;
    }
    public function DataForecasting(Request $request){
        $date = $request->input('date');
        $type = $request->input('type');
        // hari pengambilan
        $date_back = 4;
        $forecast_date_arr = [
            'types' => [],
            'date' => [],
            'enddate' => [],
            'day' => []
        ];
        array_push($forecast_date_arr['types'], $type);

        /* --------------
        / HEAD TABEL DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $data_array['chart']['labels'] = [];
        $title = [
            "No.",
            "Nama Barang",
            "Nama Provider",
        ];
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }

        //jika tipe = hari, mundur 3 hari untuk mengambil data d + 1 dan masukkan kedalam array
        if($type === 'day'){
            for($i = 1; $i <= $date_back; $i++){
                array_push($forecast_date_arr['date'], date('Y-m-d', strtotime("-".$i." day", strtotime($date))));
                array_push($forecast_date_arr['day'], date('D, d-m-Y', strtotime("-".$i." day", strtotime($date))));
            }
            foreach ($forecast_date_arr['day'] as $value){
                array_push($data_array['columns'], ["title" => $value]);
            }
        }

        //tapi jika tipe = minggu, mundur 3 minggu untuk mengambil data w + 1 dan masukkan kedalam array
        else if ($type === 'week') {
            $month = $request->input('month');
            $newDate = date('Y-'.$month.'-d');
            $weeklyDate = $this->weekFromDate($newDate);
            $weekDay = collect($weeklyDate['c_week']);
            $weekDay = $weekDay->intersect($date)->toArray();
            $startDate = $weeklyDate['startdate'][array_keys($weekDay)[0]];
            $endDate = $weeklyDate['enddate'][array_keys($weekDay)[0]];
            for ($i = 1; $i <= $date_back; $i++){
                array_push($forecast_date_arr['date'], date('Y-m-d', strtotime("-".$i." week", strtotime($startDate))));
                array_push($forecast_date_arr['enddate'], date('Y-m-d', strtotime("-".$i." week", strtotime($endDate))));
                // array_push($forecast_date_arr['day'], date('D, d-m-Y', strtotime("-".$i." week", strtotime($endDate))));
                array_push($forecast_date_arr['day'], "Week ".$i);
            }
            foreach ($forecast_date_arr['day'] as $value) {
                array_push($data_array['columns'], ["title" => $value]);
            }
        }
        //jika tipe adalah bulan, mundur 3 bulan untuk mengambil data m + 1 dan masukkan kedalam array
        else{
            $newDate = date('Y-m-d', strtotime(date('Y-'.$date.'-1')));
            for($i = 1; $i <= $date_back; $i++){
                array_push($forecast_date_arr['date'], date('Y-m-d', strtotime('-'.$i." month", strtotime($newDate))));
                array_push($forecast_date_arr['enddate'], date('Y-m-d', strtotime("-".($i - 1)." month", strtotime($newDate))));
                array_push($forecast_date_arr['day'], date('M', strtotime('-'.$i." month", strtotime($newDate))));
            }
            foreach ($forecast_date_arr['day'] as $value){
                array_push($data_array['columns'], ["title" => $value]);
            }
        }

        $title3 = [
            "Pmk Min",
            "Pmk Max",
            "Mean",
            "Median",
            "Dibeli",
        ];
        foreach ($title3 as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }

        //ambil data barang sbg deskripsi
        $barangs = DataBarangModel::with(['Provider'])->where('status','=','actived')->orderBy('id', 'ASC')->get();
        foreach ($barangs as $key => $barang){
            $dummy = [];
            //masukkan kedalam dummy data list
            array_push($dummy, $barang->id);
            array_push($dummy, $barang->name);
            array_push($dummy, $barang->Provider->name);
            $val_out = [];
            foreach ($forecast_date_arr['types'] as $key2 => $type){
                foreach ($forecast_date_arr['date'] as $key3 => $value) {
                    if($type === 'day'){
                        $date = $value;
                        $stocks = Stock::where('id_barang', '=', $barang->id)->where('created_at', $date)->get();
                    }
                    else {
                        $date = [$value, $forecast_date_arr['enddate'][$key3]];
                        $stocks = Stock::where('id_barang', '=', $barang->id)->whereBetween('created_at', $date)->get();
                    }
                    //ambil data stock lalu jumlahkan total masuk dan keluar
                    $masuk = 0;
                    $keluar = 0;
                    foreach ($stocks as $key => $stock) {
                        $masuk += $stock->in_stock;
                        $keluar += $stock->out_stock;
                    }
                    array_push($dummy, $keluar);
                    array_push($val_out, $keluar);
                }
            }

            // menentukan minimum maximum dan mean
            $pem_min = min($val_out);
            array_push($dummy, $pem_min);
            $pem_max = max($val_out);
            array_push($dummy, $pem_max);
            $jangkauan = (max($val_out) - min($val_out));
            $mean = array_sum($val_out)/count($val_out);
            array_push($dummy, $mean);

            // menentukan median
            $length = count($val_out);
            $half_length = $length / 2;
            $median_index = (int) $half_length;
            $median = $val_out[$median_index];
            array_push($dummy, $median);

            // menentukan interval min max, panjang interval
            $interval_min = 0;
            $interval_max = $mean;
            $min_U = $pem_min + $interval_min;
            $max_U = $pem_max + $interval_max;
            $jangkauan_U = $max_U - $min_U;
            $panjang_interval = $mean/2;
            $jumlah_kelas = $panjang_interval === 0 ? $panjang_interval : round($max_U/$panjang_interval);

            // dump($val_out);

            //proses fuzzifikasi
            $sub_interval = [];
            array_push($sub_interval, [$min_U, $min_U + $panjang_interval, "A1", ($min_U + ($min_U + $panjang_interval) )/2]);
            for($i=2; $i<=$jumlah_kelas; $i++){
                array_push($sub_interval, [$sub_interval[count($sub_interval) - 1][1], ($sub_interval[count($sub_interval) - 1][1] + $panjang_interval),
                "A".$i, ($sub_interval[count($sub_interval) - 1][1] + ($sub_interval[count($sub_interval) - 1][1] + $panjang_interval))/2]);
            }
            // dump($sub_interval);

            //klasifikasi data
            $data_class = [];
            for ($i=0; $i < count($val_out); $i++) {
                for ($j=0; $j < count($sub_interval); $j++) {
                    if($val_out[$i] >= $sub_interval[$j][0] && $val_out[$i] <= $sub_interval[$j][1]){
                        array_push($data_class, $sub_interval[$j][2]);
                    }
                }
            }
            // dump($data_class);

            //fuzzy relationship
            $FLR = [];
            array_push($FLR, [$data_class[0], $data_class[1]]);
            for ($i=2; $i < count($data_class); $i++) {
                array_push($FLR, [$FLR[count($FLR) - 1][1], $data_class[$i]]);
            }
            // dump($FLR);

            //fuzzy relationship group
            $FLRG = [];
            for ($i=0; $i < count($sub_interval); $i++) {
                $tampung = [];
                for ($j=0; $j < count($FLR) ; $j++) {
                    if($sub_interval[$i][2] === $FLR[$j][0]){
                        array_push($tampung, $FLR[$j][0]);
                    }
                    // else{
                    //     array_push($FLRG, [$sub_interval[$i][2], 1]);
                    // }
                }
                array_push($FLRG, [$sub_interval[$i][2], count($tampung)]);
            }
            // dump($FLRG);

            //Hasil Defuzzifikasi
            $Defuzzifikasi = [];
            for($i=0; $i < count($sub_interval); $i++){
                for($j=0;$j<count($FLRG);$j++){
                    if($sub_interval[$i][2] === $FLRG[$j][0]){
                        if($FLRG[$i][1] > 1){
                            array_push($Defuzzifikasi, [$sub_interval[$i][2], $sub_interval[$i][3]/2]);
                        }
                        else if($FLRG[$i][1] === 1){
                            array_push($Defuzzifikasi, [$sub_interval[$i][2], $sub_interval[$i][3]]);
                        }
                        else{
                            array_push($Defuzzifikasi, [$sub_interval[$i][2], $sub_interval[$i][3]]);
                        }
                    }
                }
            }
            // dump($Defuzzifikasi);

            // Hasil Akhir
            $output = [];
            $summary = [];
            for($i=0; $i < count($forecast_date_arr['day']); $i++){
                if($i === 0){
                    $result = $val_out[$i];
                }
                else{
                    for($j=0; $j<count($Defuzzifikasi); $j++){
                        if($data_class[$i] === $Defuzzifikasi[$j][0]){
                            $result = $Defuzzifikasi[$j][1];
                        }
                    }
                }
                array_push($output, [$forecast_date_arr['day'][$i], $val_out[$i], $data_class[$i], $result]);
                array_push($summary, $result);
            }
            $sum = ceil(array_sum($summary)/count($summary));
            array_push($output, [date('D, d-m-Y', strtotime('+1 day', strtotime($forecast_date_arr['day'][count($forecast_date_arr['day'])-1]))), '', '', $sum]);
            // dump($output);

            array_push($dummy, $sum);
            array_push($data_array['data'], $dummy);
        }
        // dump($data_array);
        // dd($dummy);
        return $data_array;
    }
}
