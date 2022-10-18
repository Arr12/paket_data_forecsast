@extends('admin.layouts.app')

@php
    $provider = route('api.forecasting.get-provider');
    $paket_data = route('api.forecasting.get-barang');
@endphp
@push('before-style')
@endpush
@push('after-style')
<!-- Morris Chart Css-->
<link href="/plugins/morrisjs/morris.css" rel="stylesheet" />
<style>
    .list-kwitansi:hover{
        background-color: rgb(255, 221, 221);
    }
</style>
@endpush
@push('before-script')
@endpush
@push('after-script')
<script>
    let TabelStock = function(url) {
        $('#FormTabelStock').html(createSkeleton(1));
        $.ajax({
            url: url,
            dataType: "json",
            success: function(json) {
                $('#FormTabelStock').html(
                    "<table id='"+ name +"' class='table table-bordered table-striped table-hover'></table>"
                );
                $("#" + total).html(json.property[0].total);
                $('#' + name).DataTable(json);
                let arr = [];
                for (let i = 1; i < json.columns.length; i++) {
                    let title = json.columns[i].title;
                    arr.push("<a class='btn btn-primary waves-effect toggle-vis' data-column='" + i +
                        "'>" + title + "</a>");
                }
                let combine = arr.join();
                let fix = combine.replace(/,/g, '');
                $("#" + col).html(fix);

                /* ------------------------------
                / DATATABLES SEARCH BY COLUMN
                ------------------------------ */
                let table = $('#' + name).DataTable({
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
                    let column = table.column($(this).attr(col));

                    // Toggle the visibility
                    column.visible(!column.visible());
                });
            },
        });
    }
    $(document).ready(function(){
        TabelStock("{{route('api.stock.get-stock')}}");
        var barang = '';
        $.ajax({
            url: "{{route('api.forecasting.get-barang')}}",
            type: 'GET',
            dataType: 'json',
            success: function(json) {
                barang += "<label for='barang_select'>Barang</label><select type='text' id='barang_select'><option value=''>Pilih Barang</option>";
                json.data.map(function(val, index){
                    barang += "<option value='"+ val[1] + "," + val[5] + "'>"+ val[2] +"</option>";
                });
                barang += "</select>";
                $("#barang").html(barang);
                $("#barang_select").addClass("form-control");
            }
        });
        $(document).change('#barang_select', function(){
            var barang = $(this).val();
            $("#harga").val(barang[1]);
        });
        $(document).on('click', '#btn_calculate', function(){
            var qty = $("#qty").val();
            var harga = $("#harga").val();
            var total = qty * harga;
            $("#total").val(formatRupiah(total, undefined));
        });
        $("#btn_add").click(function(){
            Swal.fire({
                title: 'Do you want to save the changes?',
                showCancelButton: true,
                confirmButtonText: 'Save',
            }).then((result) => {
                if (result.isConfirmed) {
                    if(type === 'pulsa'){
                        var name = $("#no_hp").val() + " (" + $("#barang_select").val() + ")";
                        var nominal = $("#nominal").val();
                        var qty = 1;
                    }
                    else{
                        var name = $("#paket_data_select").val();
                        name = name.split(",");
                        var nominal = $("#nominal_paket_data").val();
                        var qty = $("#qty_paket_data").val();
                    }
                    $.ajax({
                        url: "{{route('api.transaction.post-transaction')}}",
                        type: 'POST',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            "name" : name[0],
                            "qty" : qty,
                            "sell_price" : nominal,
                            "type" : type,
                        },
                        dataType: 'json',
                        success: function(result){
                            Swal.fire('Saved!', '', 'success');
                            Tabel("{{route('api.transaction.get-transaction')}}", 'transaction');
                        }
                    });
                }
            });
        });
        $(document).on('click', '#delete_pemesanan', function(){
            var id = $(this).attr('data-id');
            Swal.fire({
                title: 'Do you want to delete the changes?',
                showCancelButton: true,
                confirmButtonText: 'Save',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "",
                        type: 'POST',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            "id" : id
                        },
                        dataType: 'json',
                        success: function(json){
                            Swal.fire('Deleted!', '', 'success');
                            Tabel("{{route('api.transaction.get-transaction')}}", 'transaction');
                        }
                    })
                }
            });
        });
        $(document).on('click', '#btn_done_transaction', function(){
            $.ajax({
                url: "{{ route('api.transaction.post-transaction-detail') }}",
                type: 'POST',
                data: {
                    "_token" : "{{ csrf_token() }}",
                    "id" : id,
                },
                dataType: 'json',
                success: function(json){
                    Swal.fire('Splited!', '', 'success');
                    Tabel("{{route('api.transaction.get-transaction')}}", 'transaction');
                }
            });
        });
    });
</script>
@endpush
@section('content')
<div class="block-header">
    <h2>PEMESANAN DAN STOK</h2>
</div>
<div class="row clearfix">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="card">
            <div class="header">
                <h2>Tabel Stok</h2>
                <ul class="header-dropdown m-r--5">
                    <li class="dropdown">
                        <button style="display:none;" id='setDataDailyB' class="btn waves-effect btn-primary" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="material-icons">save</i> Export Periode Report
                        </button>
                    </li>
                </ul>
            </div>
            <div class="body">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <p>Hide column:</p>
                        <div id="data-column-stock"></div>
                    </div>
                </div>
                <div class="table-responsive" id="FormTabelStock"></div>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
        <div class="card">
            <div class="header">
                <h2>Input Stok</h2>
            </div>
            <div class="body">
                <div class="row clearfix">
                    <div id="alert"></div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="barang"></div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <label for="qty">Qty</label>
                        <input type="text" id="qty" class="form-control" placeholder="Ketik Qty" required />
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <label for="harga">Harga Beli</label>
                        <input type="text" id="harga" class="form-control" placeholder="Ketik Harga" />
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <label for="harga_jual">Harga Jual</label>
                        <input type="text" id="harga_jual" class="form-control" placeholder="Ketik Harga Jual" />
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <button class="btn btn-danger btn-block waves-effect" id="btn_calculate">
                            <i class="material-icons">sync</i> Calculate
                        </button>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <label for="total">Total</label>
                        <input type="text" id="total" class="form-control" placeholder="Ketik Total" disabled />
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
                <h2>Tabel Pemesanan</h2>
                <ul class="header-dropdown m-r--5">
                    <li class="dropdown">
                        <button style="display:none;" id='setDataDailyB' class="btn waves-effect btn-primary" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="material-icons">save</i> Export Periode Report
                        </button>
                    </li>
                </ul>
            </div>
            <div class="body">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <p>Hide column:</p>
                        <div id="data-column"></div>
                    </div>
                </div>
                <div class="table-responsive" id="FormTabel"></div>
                <table style="width: 30%;">
                    <tr>
                        <td>Grand Total</td>
                        <td>:</td>
                        <td id="grand_total"></td>
                    </tr>
                </table>
                <div class="row" style="margin-top: 24px;">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <label for="faktur">No. Faktur</label>
                        <input type="text" id="faktur" class="form-control" placeholder="Ketik Faktur" />
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <button class="btn btn-primary btn-block waves-effect" id="btn_done_transaction" data-toggle="modal" data-target="#modalPemesananTersimpan">
                            <i class="material-icons">done_all</i> Selesai
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    {{-- <div class="modal fade" id="modalTransaksiTersimpan" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Transaksi Tersimpan</h4>
            </div>
            <div class="modal-body" style="height: 50vh; overflow-y: auto;">
                <div class="d-flex cursor-pointer w-100 list-kwitansi py-2">
                    <svg width="100" height="50" viewBox="0 0 512 512" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M416 221.25V416C416 428.73 410.943 440.939 401.941 449.941C392.939 458.943 380.73 464 368 464H144C131.27 464 119.061 458.943 110.059 449.941C101.057 440.939 96 428.73 96 416V96C96 83.2696 101.057 71.0606 110.059 62.0589C119.061 53.0571 131.27 48 144 48H242.75C251.234 48.0013 259.37 51.3716 265.37 57.37L406.63 198.63C412.628 204.63 415.999 212.766 416 221.25Z" stroke="black" stroke-width="32" stroke-linejoin="round"/>
                        <path d="M256 56V176C256 184.487 259.371 192.626 265.373 198.627C271.374 204.629 279.513 208 288 208H408" stroke="black" stroke-width="32" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <div class="d-flex flex-column">
                        <p class="font-weight-bold">PNJ-01293847</p>
                        <small>082934757578</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
            </div>
        </div>
    </div> --}}

</div>
@endsection
