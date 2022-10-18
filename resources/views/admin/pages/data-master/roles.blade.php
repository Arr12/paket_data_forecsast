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
    let TabelUser = function(url) {
        $('#FormTabel').html(createSkeleton(1));
        $.ajax({
            url: url,
            type: 'GET',
            dataType: "json",
            success: function(json) {
                json = json.data_array;
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
                    // console.log(column);

                    // Toggle the visibility
                    column.visible(!column.visible());
                });
            },
        });
    }
    $(document).ready(function() {
        $('#FormTabel').html(createSkeleton(1));
        TabelUser("{{route('api.forecasting.get-roles')}}");
        $(document).on('click', '#btn_add_data', function() {
            var name = $("#name").val();
            var slug = $("#slug").val();
            var desc = $("#desc").val();
            $(this).attr('disabled', 'disabled');
            $('#FormTabel').html(createSkeleton(1));
            $.ajax({
                url: "{{route('api.forecasting.post-roles')}}",
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'name' : name,
                    'slug' : slug,
                    'desc' : desc,
                },
                type: 'POST',
                success: function(json) {
                    $('#btn_add_data').removeAttr('disabled', 'disabled');
                    TabelUser("{{route('api.forecasting.get-roles')}}");
                    $("#name").val('');
                    $("#desc").val('');
                    $("#slug").val('');
                },
                error: function() {
                    $('#btn_add_data').removeAttr('disabled', 'disabled');
                }
            });
        });
        $(document).on('click', '#btn_edit', function(){
            var id = $(this).attr('data-id');
            var slug = $(this).attr('data-slug');
            var name = $(this).attr('data-name');
            var desc = $(this).attr('data-desc');
            $("#name").val(name);
            $("#slug").val(slug);
            $("#desc").val(desc);
            $("#form_btn").html(`
                <button class="btn btn-danger btn-block waves-effect" id="btn_cancel">
                    <i class="material-icons">cancel</i> Cancel
                </button>
                <button class="btn btn-primary btn-block waves-effect" id="btn_edit_data" data-id='`+id+`'>
                    <i class="material-icons">done_all</i> Edit Data
                </button>
            `);
        });
        $(document).on('click', '#btn_edit_data', function() {
            var id = $(this).attr('data-id');
            var name = $("#name").val();
            var slug = $("#slug").val();
            var desc = $("#desc").val();
            $(this).attr('disabled', 'disabled');
            $('#FormTabel').html(createSkeleton(1));
            $.ajax({
                url: "{{route('api.forecasting.update-roles')}}",
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'id' : id,
                    'name' : name,
                    'slug' : slug,
                    'desc' : desc,
                },
                type: 'POST',
                success: function(json) {
                    $("#form_btn").html(`
                        <button class="btn btn-primary btn-block waves-effect" id="btn_add_data">
                            <i class="material-icons">add</i> Add Data
                        </button>
                    `);
                    TabelUser("{{route('api.forecasting.get-roles')}}");
                    $("#name").val('');
                    $("#desc").val('');
                    $("#slug").val('');
                },
                error: function() {
                    $('#btn_add_data').removeAttr('disabled', 'disabled');
                }
            });
        });
        $(document).on('click', '#btn_cancel', function(){
            $("#name").val('');
            $("#slug").val('');
            $("#desc").val('');
            $("#form_btn").html(`
                <button class="btn btn-primary btn-block waves-effect" id="btn_add_data">
                    <i class="material-icons">add</i> Add Data
                </button>
            `);
        });
        $(document).on('click', '#btn_delete', function(){
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
                        url: "{{route('api.forecasting.delete-roles')}}",
                        data: {
                            '_token': $('meta[name="csrf-token"]').attr('content'),
                            'id' : id
                        },
                        type: 'POST',
                        success: function(){
                            Swal.fire(
                                'Terhapus!',
                                'Data telah dihapus.',
                                'success'
                            );
                            TabelUser("{{route('api.forecasting.get-roles')}}");
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
    <h2>Master Roles</h2>
</div>
<div class="row clearfix">
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
        <div class="card">
            <div class="header">
                <h2>Input Data Roles</h2>
            </div>
            <div class="body">
                <div class="row clearfix">
                    <div id="alert"></div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <label for="name">Nama</label>
                        <input type="text" id="name" class="form-control" placeholder="Ketik Nama Roles" />
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <label for="slug">Slug</label>
                        <input type="text" id="slug" class="form-control" placeholder="Slug" required />
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <label for="desc">Description</label>
                        <input type="text" id="desc" class="form-control" placeholder="Description" required />
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="roles"></div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="form_btn">
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
                <h2>List Roles</h2>
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
