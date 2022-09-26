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
        $('#FormTabel').html(createSkeleton(1));
        $.ajax({
            url: url,
            dataType: "json",
            success: function(json) {
                $('#FormTabel').html(
                    "<table id='Tabel' class='table table-bordered table-striped table-hover'></table>"
                );
                $('#Tabel').DataTable(json);
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
                let table = $('#Tabel').DataTable({
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
        $('#FormTabel').html(createSkeleton(1));
        TabelForecast('{{$get_data}}');
        $(document).on('click', '#btn_add_data', function() {
            if ($("#name").val() != '') {
                $(this).attr('disabled', 'disabled');
                $('#FormTabel').html(createSkeleton(1));
                let url_dx = $(this).attr('data-href');
                $.ajax({
                    url: url_dx,
                    success: function(json) {
                        $('#btn_add_data').removeAttr('disabled', 'disabled');
                        TabelForecast(url_dx);
                    },
                    error: function() {
                        $('#btn_add_data').removeAttr('disabled', 'disabled');
                    }
                });
            }
        });
        $(document).on('click', '.btn_delete', function(){
            var id = $(this).attr('data-id');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{$delete_data}}'+'?id='+id,
                        type: 'POST',
                        success: function(){
                            Swal.fire(
                                'Terhapus!',
                                'Data telah dihapus.',
                                'success'
                            );
                        },
                        error: function(){
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Ada yang salah',
                                footer: 'Pastikan periksa koneksi Anda'
                            })
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
@section('content')
<div class="block-header">
    <h2>Master Barang</h2>
</div>
<div class="row clearfix">
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
        <div class="card">
            <div class="header">
                <h2>Input Data</h2>
            </div>
            <div class="body">
                <div class="row clearfix">
                    <div id="alert"></div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <label for="name">Nama Barang</label>
                        <input type="text" id="name" class="form-control" placeholder="Ketik nama provider" />
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <label for="provider">Provider</label>
                        <select id="provider" class="form-control">
                            <option value="">Pilih Provider</option>
                        </select>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <label for="buy_price">Harga Beli</label>
                        <input type="text" id="buy_price" class="form-control" placeholder="Harga Beli" required />
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <label for="sell_price">Harga Jual</label>
                        <input type="text" id="sell_price" class="form-control" placeholder="Harga Jual" required />
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <button class="btn btn-primary btn-block waves-effect" id="btn_add_data">
                            <i class="material-icons">add</i> Add Data
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
        <div class="card">
            <div class="header">
                <h2>List Barang</h2>
            </div>
            <div class="body">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <p>Hide column:</p>
                        <div id="data-column"></div>
                    </div>
                </div>
                <div class="table-responsive" id="FormTabel"></div>
            </div>
        </div>
    </div>
</div>
@endsection
