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
    let Tabel = function(url, type) {
        if(type === 'transaction'){
            var total = 'grand_total';
            var name = 'TabelTransaction';
            var tab = 'FormTabel';
            var col = 'data-column';
        }
        else if(type === 'split'){
            var total = 'grand_total_split';
            var name = 'TabelTransactionSplit';
            var tab = 'FormTabelSplit';
            var col = 'data-column-split';
        }
        $('#' + tab).html(createSkeleton(1));
        $.ajax({
            url: url,
            dataType: "json",
            success: function(json) {
                $('#' + tab).html(
                    "<table id='"+ name +"' class='table table-bordered table-striped table-hover'></table>"
                );
                $("#" + total).html(json.property[0].total);
                // console.log(json.property[0]);
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
                    console.log(column);

                    // Toggle the visibility
                    column.visible(!column.visible());
                });
            },
        });
    }
    $(document).ready(function(){
        let type = '';
        Tabel("{{route('api.transaction.get-transaction')}}", 'transaction');
        Tabel("{{route('api.transaction.get-transaction-split')}}", 'split');
        $('#add_transaksi_pulsa').on('click', function(){
            $("#form_add_transaksi_pulsa").show();
            $("#form_add_transaksi_paket").hide();
            var provider = '';
            $.ajax({
                url: '{{$provider}}',
                type: 'GET',
                dataType: 'json',
                success: function(json) {
                    provider += "<label for='provider_select'>Provider</label><select type='text' id='provider_select'><option value=''>Pilih Provider</option>";
                    json.data.map(function(val, index){
                        provider += "<option value='"+ val[2] +"'>"+ val[2] +"</option>";
                    });
                    provider += "</select>";
                    $("#provider").html(provider);
                    $("#provider_select").addClass("form-control");
                }
            });
            type = 'pulsa';
        });
        $("#add_transaksi_paket").on('click', function(){
            $("#form_add_transaksi_pulsa").hide();
            $("#form_add_transaksi_paket").show();
            var paket_data = '';
            $.ajax({
                url: '{{$paket_data}}',
                type: 'GET',
                dataType: 'json',
                success: function(json) {
                    paket_data += "<label for='paket_data_select'>Paket Data & Barang</label><select type='text' id='paket_data_select'><option value=''>Pilih Paket Data & Barang</option>";
                    json.data.map(function(val, index){
                        paket_data += "<option value='"+ val[2] +","+ val[5] +"'>"+ val[2] +"</option>";
                    });
                    paket_data += "</select>";
                    $("#paket_data").html(paket_data);
                    $("#paket_data_select").addClass("form-control");
                    $("#paket_data_select").change(function(){
                        var x = $(this).val().split(",");
                        $("#nominal_paket_data").val(x[1]);
                    });
                }
            });
            type = 'paket_data';
        });
        $("#btn_add_transaction").click(function(){
            Swal.fire({
                title: 'Do you want to save the changes?',
                showCancelButton: true,
                confirmButtonText: 'Save',
            }).then((result) => {
                if (result.isConfirmed) {
                    if(type === 'pulsa'){
                        var name = $("#no_hp").val() + " (" + $("#provider_select").val() + ")";
                        var nominal = $("#nominal").val();
                        var qty = nominal;
                    }
                    else{
                        var name = $("#paket_data_select").val();
                        name = name.split(",");
                        name = name[0];
                        var nominal = $("#nominal_paket_data").val();
                        var qty = $("#qty_paket_data").val();
                    }
                    $.ajax({
                        url: "{{route('api.transaction.post-transaction')}}",
                        type: 'POST',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            "name" : name,
                            "qty" : qty,
                            "sell_price" : nominal,
                            "type" : type,
                        },
                        dataType: 'json',
                        success: function(result){
                            if(result.meta.status === 'failure_stock'){
                                Swal.fire('Unsaved!', 'Please Check Stock and Try Again', 'error');
                            } else {
                                Swal.fire('Saved!', '', 'success');
                                Tabel("{{route('api.transaction.get-transaction')}}", 'transaction');
                            }
                        }
                    });
                }
            });
        });
        $(document).on('click', '#delete_transaction', function(){
            var id = $(this).attr('data-id');
            Swal.fire({
                title: 'Do you want to delete the changes?',
                showCancelButton: true,
                confirmButtonText: 'Save',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{route('api.transaction.delete-transaction')}}",
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
        $(document).on('click', '#split_transaction', function(){
            var id = $(this).attr("data-id");
            var type = $(this).attr("data-type");
            $.ajax({
                url: "{{route('api.transaction.post-split-transaction')}}",
                type: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "id" : id,
                    "type" : type
                },
                dataType: 'json',
                success: function(json){
                    Swal.fire('Splited!', '', 'success');
                    Tabel("{{route('api.transaction.get-transaction-split')}}", 'split');
                    Tabel("{{route('api.transaction.get-transaction')}}", 'transaction');
                }
            });
        });
        $(document).on('click', '#btn_done_transaction', function(){
            Swal.fire({
                title: 'Do you want to complete all the changes?',
                showCancelButton: true,
                confirmButtonText: 'Save',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('api.transaction.post-transaction-detail') }}",
                        type: 'POST',
                        data: {
                            "_token" : "{{ csrf_token() }}",
                        },
                        dataType: 'json',
                        success: function(json){
                            Swal.fire('Done!', '', 'success');
                            Tabel("{{route('api.transaction.get-transaction')}}", 'transaction');
                            Tabel("{{route('api.transaction.get-transaction-split')}}", 'split');
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
    <h2>TRANSAKSI</h2>
</div>
<div class="row clearfix">
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
        <div class="card">
            <div class="header">
                <h2>Input Transaksi</h2>
            </div>
            <div class="body">
                <div class="row clearfix">
                    <div id="alert"></div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        {{-- <button class="btn btn-success btn-block waves-effect" id="btn_transaksi_tersimpan" data-toggle="modal" data-target="#modalTransaksiTersimpan">
                            <i class="material-icons">edit</i> Buka Transaksi Tersimpan
                        </button> --}}
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <a id="add_transaksi_pulsa">
                            <div class="info-box bg-cyan hover-expand-effect" style="cursor: pointer;">
                                <div class="icon">
                                    <i class="material-icons">save</i>
                                </div>
                                <div class="content">
                                    <div class="text">Pulsa</div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <a id="add_transaksi_paket">
                            <div class="info-box bg-cyan hover-expand-effect" style="cursor: pointer;">
                                <div class="icon">
                                    <i class="material-icons">save</i>
                                </div>
                                <div class="content">
                                    <div class="text">Paket Data atau Barang</div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 row" id="form_add_transaksi_pulsa" style="display: none;">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="provider"></div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                             <label for="nominal">Nominal</label>
                            <input type="text" id="nominal" class="form-control" placeholder="Ketik nominal pulsa" />
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <label for="no_hp">No. Handphone</label>
                            <input type="text" id="no_hp" class="form-control" placeholder="Ketik nomer handphone" />
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 row" id="form_add_transaksi_paket" style="display: none;">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="paket_data"></div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <label for="nominal_paket_data">Nominal</label>
                            <input type="text" id="nominal_paket_data" class="form-control" placeholder="Nominal Paket Data" disabled />
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <label for="qty">Qty</label>
                            <input type="text" id="qty_paket_data" class="form-control" placeholder="Qty" />
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <button class="btn btn-primary btn-block waves-effect" type='button' id="btn_add_transaction">
                            <i class="material-icons">add</i> Add Transaksi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
        <div class="card">
            <div class="header">
                <h2>Tabel Transaksi</h2>
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
                        <button class="btn btn-primary btn-block waves-effect" id="btn_done_transaction" data-toggle="modal" data-target="#modalTransaksiTersimpan">
                            <i class="material-icons">done_all</i> Selesai
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="header">
                <h2>Split Transaksi</h2>
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
                        <div id="data-column-split"></div>
                    </div>
                </div>
                <div class="table-responsive" id="FormTabelSplit"></div>
                <table style="width: 30%;">
                    <tr>
                        <td>Grand Total</td>
                        <td>:</td>
                        <td id="grand_total_column"></td>
                    </tr>
                </table>
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
