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
                var col = "data-column-stock";
                var name = 'TabelStock';
                $('#FormTabelStock').html(
                    "<table id='"+ name +"' class='table table-bordered table-striped table-hover'></table>"
                );
                $('#' + name).DataTable(json.data);
                let arr = [];
                for (let i = 0; i < json.data.columns.length; i++) {
                    let title = json.data.columns[i].title;
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
    let Tabel = function(url) {
        $('#FormTabel').html(createSkeleton(1));
        $.ajax({
            url: url,
            dataType: "json",
            success: function(json) {
                var col = "data-column";
                var name = 'Tabel';
                $('#FormTabel').html(
                    "<table id='"+ name +"' class='table table-bordered table-striped table-hover'></table>"
                );
                var total = 0;
                json.data.map((v, i) => {
                    if(v.type === 'paket_data'){
                        total += v.buy_price * v.qty;
                    } else {
                        total += v.buy_price;
                    }
                });
                $('#grand_total').html(formatRupiah(total.toString(), 2));
                $('#' + name).DataTable(json.data_array);
                let arr = [];
                for (let i = 1; i < json.data_array.columns.length; i++) {
                    let title = json.data_array.columns[i].title;
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
    let Provider = function(type, value) {
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
                if(type === 'edit'){
                    $("#form_add_pemesanan_pulsa #provider_select").val(value).attr('selected', 'selected');
                }
            }
        });
    }
    let Barang = function(type, value) {
        var barang = '';
        $.ajax({
            url: "{{route('api.forecasting.get-barang')}}",
            type: 'GET',
            dataType: 'json',
            success: function(json) {
                barang += "<label for='barang_select'>Barang</label><select type='text' id='barang_select'><option value=''>Pilih Barang</option>";
                json.data.map(function(val, index){
                    barang += "<option value='"+ val[2] + ";" + val[5] + "'>"+ val[2] +"</option>";
                });
                barang += "</select>";
                $("#barang").html(barang);
                $("#barang_select").addClass("form-control");
                if(type === 'edit'){
                    $("#form_add_pemesanan__paket #barang_select").val(value).attr('selected', 'selected');
                }
            }
        });
    }
    $(document).ready(function(){
        Tabel("{{route('api.pemesanan.get-pemesanan')}}");
        TabelStock("{{route('api.stock.get-stock')}}");
        $('#add_pemesanan_pulsa').on('click', function(){
            $("#form_add_pemesanan_pulsa").show();
            $("#form_add_pemesanan__paket").hide();
            $("#btn_add_data").attr('data-id', 'pulsa');
            $("#btn_edit_data").attr('data-id', 'pulsa');
            var provider = '';
            Provider('add', '');
            var type = 'pulsa';
        });
        $("#add_pemesanan__paket").on('click', function(){
            $("#form_add_pemesanan_pulsa").hide();
            $("#form_add_pemesanan__paket").show();
            $("#btn_add_data").attr('data-id', 'paket_data');
            $("#btn_edit_data").attr('data-id', 'paket_data');
            var barang = '';
            Barang('add', '');
            var type = 'paket_data';
        });
        $(document).on('change', '#barang_select', function(){
            var barang = $(this).val();
            barang = barang.split(';');
            $("#harga_paket").val(barang[1]);
        });
        $(document).on('click', '#btn_calculate', function(){
            var cat = $(this).attr('data-id');
            var harga_jual = $("#harga_jual_paket").val();
            var qty = $("#qty_paket").val();
            var total = Math.ceil(harga_jual * qty);
            $("#total").val(total);
        });
        $(document).on('click', "#btn_add_data", function(){
            var type = $(this).attr('data-id');
            Swal.fire({
                title: 'Do you want to save the changes?',
                showCancelButton: true,
                confirmButtonText: 'Save',
            }).then((result) => {
                if (result.isConfirmed) {
                    if(type === 'pulsa'){
                        var name = $("#provider_select").val();
                        var nominal = $("#harga_pulsa").val();
                        var qty = 1;
                        var data = {
                            "_token": "{{ csrf_token() }}",
                            "name" : name,
                            "qty" : nominal,
                            "buy_price" : nominal,
                            "sell_price" : nominal,
                            "type" : type,
                        };
                    }
                    else{
                        var name = $("#barang_select").val();
                        name = name.split(";");
                        var harga_beli = $("#harga_paket").val();
                        var harga_jual = $("#harga_jual_paket").val();
                        var qty = $("#qty_paket").val();
                        var data = {
                            "_token": "{{ csrf_token() }}",
                            "name" : name[0],
                            "qty" : qty,
                            "buy_price" : harga_beli,
                            "sell_price" : harga_jual,
                            "type" : type,
                        };
                    }
                    $.ajax({
                        url: "{{route('api.pemesanan.post-pemesanan')}}",
                        type: 'POST',
                        data: data,
                        dataType: 'json',
                        success: function(result){
                            Swal.fire('Saved!', '', 'success');
                            Tabel("{{route('api.pemesanan.get-pemesanan')}}");
                        }
                    });
                }
            });
        });
        $(document).on('click', '#btn_delete_pemesanan', function(){
            var id = $(this).attr('data-id');
            Swal.fire({
                title: 'Do you want to delete the changes?',
                showCancelButton: true,
                confirmButtonText: 'Save',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{route('api.pemesanan.delete-pemesanan')}}",
                        type: 'POST',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            "id" : id
                        },
                        dataType: 'json',
                        success: function(json){
                            Swal.fire('Deleted!', '', 'success');
                            TabelStock("{{route('api.stock.get-stock')}}");
                            Tabel("{{route('api.pemesanan.get-pemesanan')}}");
                        }
                    })
                }
            });
        });
        $(document).on('click', '#btn_edit_pemesanan', function() {
            var id = $(this).attr('data-id');
            var name = $(this).attr('data-name');
            var buy_price = $(this).attr('data-buy_price');
            var qty = $(this).attr('data-qty');
            var sell_price = $(this).attr('data-sell_price');
            var type = $(this).attr('data-type');
            if(type === 'pulsa'){
                $("#form_add_pemesanan_pulsa").show();
                $("#form_add_pemesanan__paket").hide();
                $("#form_add_pemesanan_pulsa #provider_select").val(name).attr('selected', 'selected');
                $("#form_add_pemesanan_pulsa #harga_pulsa").val(sell_price);
                Provider('edit', name);
                $("#btn_data").html(`
                    <button class="btn btn-primary btn-block waves-effect" data-type="`+ type +`" data-id="`+ id +`" id="btn_edit_data"><i class="material-icons">done_all</i> Edit Data</button>
                    <button class="btn btn-danger btn-block waves-effect" id="btn_cancel_data"><i class="material-icons">cancel</i> Cancel</button>
                `);
                $("#btn_cancel_data").on('click', function(){
                    $("#form_add_pemesanan_pulsa #provider_select").val('').attr('selected', 'selected');
                    $("#form_add_pemesanan_pulsa #harga_pulsa").val('');
                    $("#btn_data").html(`
                        <button class="btn btn-primary btn-block waves-effect" id="btn_add_data" data-id="`+ type +`">
                            <i class="material-icons">add</i> Add Data
                        </button>
                    `);
                });
            } else {
                $("#form_add_pemesanan__paket").show();
                $("#form_add_pemesanan_pulsa").hide();
                Barang('edit', name + ";" + buy_price);
                $("#form_add_pemesanan__paket #qty_paket").val(qty);
                $("#form_add_pemesanan__paket #harga_paket").val(buy_price);
                $("#form_add_pemesanan__paket #harga_jual_paket").val(sell_price);
                $("#btn_data").html(`
                    <button class="btn btn-primary btn-block waves-effect" data-type="`+ type +`" data-id="`+ id +`" id="btn_edit_data"><i class="material-icons">done_all</i> Edit Data</button>
                    <button class="btn btn-danger btn-block waves-effect" id="btn_cancel_data"><i class="material-icons">cancel</i> Cancel</button>
                `);
                $("#btn_cancel_data").on('click', function(){
                    $("#form_add_pemesanan__paket #qty_paket").val('');
                    $("#form_add_pemesanan__paket #barang_select").val('').attr('selected', 'selected');
                    $("#form_add_pemesanan__paket #harga_paket").val('');
                    $("#form_add_pemesanan__paket #harga_jual_paket").val('');
                    $("#btn_data").html(`
                        <button class="btn btn-primary btn-block waves-effect" id="btn_add_data" data-id="`+ type +`">
                            <i class="material-icons">add</i> Add Data
                        </button>
                    `);
                });
            }
        });
        $(document).on('click', "#btn_edit_data", function(){
            var type = $(this).attr('data-type');
            var id = $(this).attr('data-id');
            Swal.fire({
                title: 'Do you want to save the changes?',
                showCancelButton: true,
                confirmButtonText: 'Save',
            }).then((result) => {
                if (result.isConfirmed) {
                    if(type === 'pulsa'){
                        var name = $("#provider_select").val();
                        var nominal = $("#harga_pulsa").val();
                        var qty = 1;
                        var data = {
                            "_token": "{{ csrf_token() }}",
                            "id" : id,
                            "name" : name,
                            "qty" : nominal,
                            "buy_price" : nominal,
                            "sell_price" : nominal,
                            "type" : type,
                        };
                    }
                    else{
                        var name = $("#barang_select").val();
                        name = name.split(";");
                        var harga_beli = $("#harga_paket").val();
                        var harga_jual = $("#harga_jual_paket").val();
                        var qty = $("#qty_paket").val();
                        var data = {
                            "_token": "{{ csrf_token() }}",
                            "id" : id,
                            "name" : name[0],
                            "qty" : qty,
                            "buy_price" : harga_beli,
                            "sell_price" : harga_jual,
                            "type" : type,
                        };
                    }
                    $.ajax({
                        url: "{{route('api.pemesanan.update-pemesanan')}}",
                        type: 'POST',
                        data: data,
                        dataType: 'json',
                        success: function(result){
                            Swal.fire('Saved!', '', 'success');
                            Tabel("{{route('api.pemesanan.get-pemesanan')}}");
                        }
                    });
                }
            });
        });
        $(document).on('click', '#btn_done_transaction', function(){
            var faktur = $("#faktur").val();
            Swal.fire({
                title: 'Do you want to save the changes?',
                showCancelButton: true,
                confirmButtonText: 'Save',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('api.pemesanan.done-pemesanan') }}",
                        type: 'POST',
                        data: {
                            "_token" : "{{ csrf_token() }}",
                            "faktur" : faktur
                        },
                        dataType: 'json',
                        success: function(json){
                            Swal.fire('Success!', '', 'success');
                            Tabel("{{route('api.pemesanan.get-pemesanan')}}");
                            TabelStock("{{route('api.stock.get-stock')}}");
                            $("#faktur").val('');
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
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <a id="add_pemesanan_pulsa">
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
                        <a id="add_pemesanan__paket">
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
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="form_add_pemesanan_pulsa" style="display:none;">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="provider"></div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <label for="harga_pulsa">Harga Beli</label>
                                <input type="text" id="harga_pulsa" class="form-control" placeholder="Ketik Harga" />
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="form_add_pemesanan__paket" style="display:none;">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="barang"></div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <label for="qty">Qty</label>
                                <input type="text" id="qty_paket" class="form-control" placeholder="Ketik Qty" required />
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <label for="harga_paket">Harga Beli</label>
                                <input type="text" id="harga_paket" class="form-control" placeholder="Ketik Harga" />
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <label for="harga_jual_paket">Harga Jual</label>
                                <input type="text" id="harga_jual_paket" class="form-control" placeholder="Ketik Harga Jual" />
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
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="btn_data">
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
