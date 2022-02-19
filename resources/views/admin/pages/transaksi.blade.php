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
@section('content')
<div class="block-header">
    <h2>TRANSAKSI</h2>
</div>
<div class="row clearfix">
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
        <div class="card">
            <div class="header">
                <h2>Input Transaksi</h2>
                <ul class="header-dropdown m-r--5">
                    <li class="dropdown">
                        <button style="display:none;" id='setDataDailyB' class="btn waves-effect btn-primary" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="material-icons">save</i> Export Periode Report
                        </button>
                    </li>
                </ul>
            </div>
            <div class="body">
                <div class="row clearfix">
                    <div id="alert"></div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <a href="#" id="add_transaksi_pulsa">
                            <div class="info-box bg-cyan hover-expand-effect">
                                <div class="icon">
                                    <i class="material-icons">save</i>
                                </div>
                                <div class="content">
                                    <div class="text">+ Transaksi Pulsa</div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <a href="#" id="add_transaksi_pulsa">
                            <div class="info-box bg-cyan hover-expand-effect">
                                <div class="icon">
                                    <i class="material-icons">save</i>
                                </div>
                                <div class="content">
                                    <div class="text">+ Transaksi Paket Data</div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="SDateForm" style="display:none;">
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <input type="date" id="SDateA" class="form-control" />
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <input type="date" id="SDateB" class="form-control" />
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="SMonthForm" style="display:none;">
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <input type="month" id="SMonth" class="form-control" />
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" id="SWeekForm">
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <button class="btn btn-primary btn-block waves-effect" id="ShowData">
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
            </div>
        </div>
    </div>
</div>
@endsection