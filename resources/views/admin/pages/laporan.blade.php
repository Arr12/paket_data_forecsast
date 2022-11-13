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
<script>
    let TabelForecast = function(url) {
        $('#FormTabelForecast').html(createSkeleton(1));
        $.ajax({
            url: url,
            dataType: "json",
            success: function(json) {
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
                    destroy: true,
                    searching: true,
                    order: [
                        [1, 'desc']
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
                    console.log(column);

                    // Toggle the visibility
                    column.visible(!column.visible());
                });
            },
        });
    }
    $(document).ready(function() {
        $(document).on('click', '#DoReport', function() {
            if ($("#SDateA").val() != '' && $("#SDateB").val() != '') {
                $(this).attr('disabled', 'disabled');
                $('#FormTabelForecast').html(createSkeleton(1));
                let url_dx = $(this).attr('data-href') + "?firstDate=" + $("#SDateA").val() + "&lastDate=" + $("#SDateB").val();
                $.ajax({
                    url: url_dx,
                    success: function(json) {
                        $('#DoReport').removeAttr('disabled', 'disabled');
                        TabelForecast(url_dx);
                        console.log(json);
                        var label = [];
                        var value = [];
                        var date_start = new Date($("#SDateA").val());
                        var date_end = new Date($("#SDateB").val());
                        for(var d = new Date($("#SDateA").val()); d <= date_end; d.setDate(d.getDate() + 1)){
                            // console.log(new Date(d));
                            label.push(formatDateYMD(new Date(d)));
                        }
                        // console.log(label);
                        // console.log(result.value[i]);
                        // console.log(result.year[i]);
                        for(var j = 0; j < label.length; j++){
                            var x = 0;
                            json.data.map((v, i) => {
                                // console.log(v[6]);
                                if(v[6] === formatDateYMD(label[j])){
                                    var nominal = v[4].replace('Rp ', '');
                                    // var nominal2 = nominal.replace(',', '');
                                    var nominal3 = nominal.replace('.', '');
                                    x += parseInt(nominal3);
                                }
                            });
                            value.push(x);
                        }
                        console.log(value);
                        var ctx = document.getElementById('chart-bars');
                        var myChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: label,
                                datasets: [{
                                    label: 'Pendapatan Terakhir',
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
                                        'rgba(255, 99, 132, 1)',
                                        'rgba(54, 162, 235, 1)',
                                        'rgba(255, 206, 86, 1)',
                                        'rgba(75, 192, 192, 1)',
                                        'rgba(153, 102, 255, 1)',
                                        'rgba(255, 159, 64, 1)'
                                    ],
                                    borderWidth: 1
                                }]
                            },
                        });
                    },
                    error: function() {
                        $('#DoReport').removeAttr('disabled', 'disabled');
                    }
                });
            }
        });
    });
</script>
@endpush
@section('content')
    <div class="block-header">
        <h2>Laporan</h2>
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
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <label for="SDateA">Dari :</label>
                                <input type="date" id="SDateA" class="form-control" />
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <label for="SDateA">Ke :</label>
                                <input type="date" id="SDateB" class="form-control" />
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <button class="btn btn-primary btn-block waves-effect" data-href="{{route('api.laporan.get-laporan')}}"
                                id="DoReport">
                                <i class="material-icons">done</i> Show Report
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="card">
                <div class="header">
                    <h2>Laporan By Date</h2>
                </div>
                <div class="body">
                    <div class="chart">
                        <canvas height="100" id="chart-bars" class="chart-canvas"></canvas>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <p>Hide column:</p>
                            <div id="data-column"></div>
                        </div>
                    </div>
                    <div class="table-responsive" id="FormTabelForecast"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
