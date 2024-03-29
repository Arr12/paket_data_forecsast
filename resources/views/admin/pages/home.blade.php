@extends('admin.layouts.app')
@php
    $ygt = $ygt ?? 1000000;
    $mgt = $mgt ?? 300000;
    $dgt = $dgt ?? 60000;
    $month = $month ?? ["Labels"];
    $month_total = $month_total ?? [1000000, 5000000, 6000000 ,8000000];
@endphp
@push('before-style')
@endpush
@push('after-style')
<!-- Morris Chart Css-->
<link href="/plugins/morrisjs/morris.css" rel="stylesheet" />
@endpush
@push('before-script')
@endpush
@push('after-script')
<!-- ChartJs -->
<script src="/plugins/chartjs/Chart.bundle.js"></script>

<!-- Flot Charts Plugin Js -->
<script src="/plugins/flot-charts/jquery.flot.js"></script>
<script src="/plugins/flot-charts/jquery.flot.resize.js"></script>
<script src="/plugins/flot-charts/jquery.flot.pie.js"></script>
<script src="/plugins/flot-charts/jquery.flot.categories.js"></script>
<script src="/plugins/flot-charts/jquery.flot.time.js"></script>

<!-- Sparkline Chart Plugin Js -->
<script src="/plugins/jquery-sparkline/jquery.sparkline.js"></script>
<script>
    $.ajax({
        url: "{{route('api.transaction.transaction-peryear')}}",
        type: "GET",
        cache: false,
        dataType: "json",
        success: function(result) {
            var label = [];
            var value = [];
            for(var i = 6; i >= 0; i--){
                var date = new Date();
                var full_date_last = new Date(date.getFullYear() + (0 - i), 0);
                label.push(full_date_last.getFullYear());
            }
            // console.log(result);
            // console.log(result.value[i]);
            // console.log(result.year[i]);
            for(var j = 0; j < label.length; j++){
                var x = 0;
                result.value.map((v, i) => {
                    if(parseInt(result.year[i]) === label[j]){
                        x += result.value[i];
                    }
                });
                value.push(x);
            }
            // console.log(value);
            var ctx = $("#myChart");
            var myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: label,
                    datasets: [{
                        label: 'Data Pendapatan Tahun ini',
                        data: value,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255,99,132,1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero:true,
                                max: 100000000
                            }
                        }]
                    }
                }
            });
        }
    });
    $(document).ready(function() {
        $.ajax({
            url: "{{route('api.transaction.transaction-year')}}",
            method: "GET",
            success: function(result){
                $("#transaction-year").html(formatRupiah(result, 2));
            }
        });
        $.ajax({
            url: "{{route('api.transaction.transaction-month')}}",
            method: "GET",
            success: function(result){
                $("#transaction-month").html(formatRupiah(result, 2));
            }
        });
        $.ajax({
            url: "{{route('api.transaction.transaction-day')}}",
            method: "GET",
            success: function(result){
                $("#transaction-day").html(formatRupiah(result, 2));
            }
        });
    });
</script>
@endpush

@section('content')
<div class="block-header">
    <h2>HOME</h2>
</div>
<div class="container">
    <div class="col-sm-12 col-md-12 col-lg-12">
        <div class="card" style="display: grid; padding: 20px;">
            <div class="row">
                <div class="col-sm-12 col-md-4 col-lg-4">
                    <div class="d-flex w-100 card" style="height: 120px; background-color: #bdb1ff;">
                        <div style="width: 30%; background-color: #8479c29c; height: 100%; text-align: center;">
                            <i style="font-size: 46px; font-weight: bold !important; margin-top: 35px; color: #fff;" class="material-icons">money</i>
                        </div>
                        <div style="background-color: #bdb1ff;">
                            <h2 style="margin-left: 20px; font-size: 24px; margin-top:20px; font-weight: bold !important; color: #fff;">Tahunan</h2>
                            <p id="transaction-year" style="margin-left: 20px; margin-top: 20px; font-size: 20px; font-weight: bold !important; color: #fff;">{{formatRupiah($ygt)}}</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-4 col-lg-4">
                    <div class="d-flex w-100 card" style="height: 120px; background-color: #bdb1ff;">
                        <div style="width: 30%; background-color: #8479c29c; height: 100%; text-align: center;">
                            <i style="font-size: 46px; font-weight: bold !important; margin-top: 35px; color: #fff;" class="material-icons">money</i>
                        </div>
                        <div style="background-color: #bdb1ff;">
                            <h2 style="margin-left: 20px; font-size: 24px; margin-top:20px; font-weight: bold !important; color: #fff;">Bulanan</h2>
                            <p id="transaction-month" style="margin-left: 20px; margin-top: 20px; font-size: 20px; font-weight: bold !important; color: #fff;">{{formatRupiah($mgt)}}</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-4 col-lg-4">
                    <div class="d-flex w-100 card" style="height: 120px; background-color: #bdb1ff;">
                        <div style="width: 30%; background-color: #8479c29c; height: 100%; text-align: center;">
                            <i style="font-size: 46px; font-weight: bold !important; margin-top: 35px; color: #fff;" class="material-icons">money</i>
                        </div>
                        <div style="background-color: #bdb1ff;">
                            <h2 style="margin-left: 20px; font-size: 24px; margin-top:20px; font-weight: bold !important; color: #fff;">Harian</h2>
                            <p id="transaction-day" style="margin-left: 20px; margin-top: 20px; font-size: 20px; font-weight: bold !important; color: #fff;">{{formatRupiah($dgt)}}</p>
                        </div>
                    </div>
                </div>
            </div>
            <canvas id="myChart"></canvas>
        </div>
    </div>
</div>
@endsection
