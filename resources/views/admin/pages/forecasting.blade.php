@extends('admin.layouts.app')

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
        let TabelForecast = function(url, btn) {
            $('#FormTabelForecast').html(createSkeleton(1));
            $.ajax({
                url: url,
                dataType: "json",
                success: function(json) {
                    btn.removeAttr('disabled', 'disabled');
                    $('#FormTabelForecast').html(
                        "<table id='TabelForecast' class='table table-bordered table-striped table-hover'></table>"
                    );
                    $('#TabelForecast').DataTable(json);
                    let arr = [];
                    for (let i = 1; i < json.columns.length; i++) {
                        let title = json.columns[i].title;
                        arr.push("<a class='btn btn-primary waves-effect toggle-vis' data-column='" + i +
                            "'>" + title + "</a>");
                    }
                    let combine = arr.join();
                    let fix = combine.replace(/,/g, '');
                    $("#data-column").html(fix);

                    /* ------------------------------
                    / DATATABLES SEARCH BY COLUMN
                    ------------------------------ */
                    let table = $('#TabelForecast').DataTable({
                        dom: 'Bfrtip',
                        responsive: true,
                        buttons: ['copy'],
                        scrollX: 2000,
                        scrollY: 300,
                        destroy: true,
                        searching: true,
                        autoWidth: true,
                        order: [
                            [1, 'asc']
                        ]
                    });
                    $('a.toggle-vis').on('click', function(e) {
                        e.preventDefault();
                        if ($(this).hasClass('btn-warning')) {
                            $(this).addClass('btn-primary');
                            $(this).removeClass('btn-warning');
                        } else {
                            $(this).addClass('btn-warning');
                            $(this).removeClass('btn-primary');
                        }
                        // Get the column API object
                        let column = table.column($(this).attr('data-column'));

                        // Toggle the visibility
                        column.visible(!column.visible());
                    });
                    var label = [];
                    var value = [];
                    json.columns.map((v, i) => {
                        if(i > 2 && i < 7){
                            label.push(v.title);
                        }
                        else if(i === 11){
                            label.push('Forecast');
                        }
                    });
                    v3 = 0;
                    v4 = 0;
                    v5 = 0;
                    v6 = 0;
                    v11 = 0;
                    json.data.map((v, i) => {
                        v3 += v[3];
                        v4 += v[4];
                        v5 += v[5];
                        v6 += v[6];
                        v11 += v[11];
                    });
                    value.push(v3);
                    value.push(v4);
                    value.push(v5);
                    value.push(v6);
                    value.push(v11);
                    // console.log(value);
                    const ctx = document.getElementById('myChart').getContext('2d');
                    const myChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: label,
                            datasets: [{
                                label: 'Data Penjualan',
                                backgroundColor: 'rgba(0, 255, 0, 0.3)',
                                borderColor: 'rgba(255, 0, 0, 0.8)',
                                data: value
                            }]
                        },
                    });
                },
                error: function() {
                    btn.removeAttr('disabled', 'disabled');
                }
            });
        }
        $(document).ready(function() {
            // jika menggunakan fitur H + 1
            // var today = new Date();
            // var next_day = today.getDay() + 1;
            // var next_date = today.getDate() + 1;
            // var next_month = today.getMonth() + 1;
            // var date = today.getFullYear() + '-' + (today.getMonth() + 1) + '-' + (today.getDate());
            var weekday = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
            var month = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus',
                'September', 'Oktober', 'November', 'Desember'
            ];
            $("#typeForecast").change(function(){
                var v_type = $(this).val();
                if(v_type === 'day'){
                    $("#show_date").show();
                    $("#show_week").hide();
                    $("#show_month").hide();
                }
                else if(v_type === 'week'){
                    $("#show_week").show();
                    $("#show_month").show();
                    $("#show_date").hide();
                    $("#show_year").show();
                }
                else{
                    $("#show_month").show();
                    $("#show_week").hide();
                    $("#show_date").hide();
                    $("#show_year").show();
                }
            });
            $(document).on('click', '#DoForecast', function() {
                // console.log($("#typeForecast").val());
                let date = '';
                if($("#typeForecast").val() === "day"){
                    date = $("#dateDay").val();
                    $("#title_forecast").html(date);
                }
                else if($("#typeForecast").val() === "week"){
                    date = $("#week").val();
                    $("#title_forecast").html(date);
                    date = date + "&month=" + $("#month").val() + "&year=" + $("#yearForecast").val();
                }
                else{
                    date = $("#month").val()  + "&year=" + $("#yearForecast").val();
                    $("#title_forecast").html(date);
                }
                // console.log(date);
                $(this).attr('disabled', 'disabled');
                let url_dx = $(this).attr('data-href') + "?date=" + date + "&type=" + $("#typeForecast").val();
                TabelForecast(url_dx, $('#DoForecast'));
            });
        });
    </script>
@endpush
@section('content')
    @php
    $data_forecasting = route('api.forecasting.get');
    @endphp
    <div class="block-header">
        <h2>Forecasting Pembelian</h2>
    </div>
    <div class="row clearfix">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="card">
                <div class="header">
                    <h2>Periode</h2>
                </div>
                <div class="body">
                    <div class="row clearfix">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="SDateForm">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <label for="type">Type Forecasting</label>
                                <select id="typeForecast" class="form-control">
                                    <option value="">Pilih Tipe Peramalan</option>
                                    <option value="day">Harian</option>
                                    <option value="week">Mingguan</option>
                                    <option value="month">Bulanan</option>
                                </select>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="YearForm">
                                <div class="mt-5" id="show_year" style="display: none;">
                                    <label for="type">Tahun</label>
                                    <select id="yearForecast" class="form-control">
                                        <option value="">Pilih Tahun</option>
                                        @for ($i = 5; $i >= 0; $i--)
                                            <option value="{{ date('Y') - $i }}">{{ date('Y') - $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="show_type">
                                <div class="form-group" id="show_date" style="display:none;">
                                    <label class="form-control-label" for="date">Input Tanggal</label>
                                    <input type="date" id="dateDay" class="form-control" />
                                </div>
                                <div class="form-group" id="show_month" style="display:none;">
                                    <label class="form-control-label" for="date">Input Bulanan</label>
                                    <select id="month" class="form-control">
                                        <option value="">Pilih Bulan</option>
                                        <option value="1">Januari</option>
                                        <option value="2">Februari</option>
                                        <option value="3">Maret</option>
                                        <option value="4">April</option>
                                        <option value="5">Mei</option>
                                        <option value="6">Juni</option>
                                        <option value="7">July</option>
                                        <option value="8">Agustus</option>
                                        <option value="9">September</option>
                                        <option value="10">Oktober</option>
                                        <option value="11">November</option>
                                        <option value="12">Desember</option>
                                    </select>
                                </div>
                                <div class="form-group" id="show_week" style="display:none;">
                                    <label class="form-control-label" for="date">Input Mingguan</label>
                                    <select id="week" class="form-control">
                                        <option value="">Pilih Minggu</option>
                                        <option value="Week 1">Minggu ke I</option>
                                        <option value="Week 2">Minggu ke II</option>
                                        <option value="Week 3">Minggu ke III</option>
                                        <option value="Week 4">Minggu ke IV</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <button class="btn btn-primary btn-block waves-effect" data-href="{{ $data_forecasting }}"
                                id="DoForecast">
                                <i class="material-icons">done</i> Do
                            </button>
                        </div>
                    </div>
                    <p class="text-sm mb-4">
                        Note *)<br />
                        1. PMK = Pemakaian<br />
                        2. PMS = Pemasukan<br />
                        3. Sisa = Sisa Stok<br />
                        4. M-I s/d M-V = Minggu ke-1 s/d 5<br />
                    </p>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="card">
                <div class="header">
                    <h2>Forecasting Untuk <span id="title_forecast"></span></h2>
                </div>
                <div class="body">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <p>Hide column:</p>
                            <div id="data-column"></div>
                        </div>
                    </div>
                    <div class="table-responsive" id="FormTabelForecast"></div>
                    <div class="chart">
                        <canvas height="100" id="myChart" class="chart-canvas"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
