@extends('layouts.admin')

@section('title')
    <title>Daftar Jadwal Tugas</title>
@endsection

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    {{-- <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Lokasi</h1>
                    </div> --}}
                    <div class="col-sm-12">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Beranda</a></li>
                            <li class="breadcrumb-item active">Kelola Jadwal Tugas</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <a href="{{ route('schedule.create') }}" class="btn btn-sm btn-primary float-right">Tambah <i class="fas fa-plus ml-1"></i></a>
                            </div>
                            <div class="card-body loader-area">
                                <div class="table-responsive">
                                    <table id="scheduleTable" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th style="padding: 10px 10px;">No.</th>
                                                <th style="padding: 10px 10px;">Nama Satpam</th>
                                                <th style="padding: 10px 10px;">Tanggal Tugas</th>
                                                <th style="padding: 10px 10px;">Waktu Mulai</th>
                                                <th style="padding: 10px 10px;">Waktu Selesai</th>
                                                <th style="padding: 10px 10px;" class="text-left">Shift</th>
                                                <th style="padding: 10px 10px;">Opsi</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <!-- /.content-wrapper -->

    <!-- IMPORTANT LINK -->
    <a href="{{ route('schedule.getDatatables') }}" id="scheduleGetDatatables"></a>
    <!-- /IMPORTANT LINK -->
@endsection

@section('js')
    <script>
        $(document).ready(function() {

            $.extend($.fn.dataTable.defaults, {
                autoWidth: false,
                autoLength: false,
                dom: '<"datatable-header d-flex justify-content-between align-items-center"lf><t><"datatable-footer"ip>',
                language: {
                    search: '<span>Pencarian:</span> _INPUT_',
                    searchPlaceholder: 'Cari Lokasi...',
                    lengthMenu: '<span class="mr-2">Tampil:</span> _MENU_',
                    paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' },
                    emptyTable: 'Tidak ada data'
                },
                initComplete: function() {
                    var $searchInput = $('#scheduleTable_filter input').addClass('form-control form-control-sm').attr('placeholder', 'Cari Shift...');
                    $searchInput.parent().addClass('d-flex align-items-center');

                    var $lengthMenu = $('#scheduleTable_length select').addClass('form-control form-control-sm');

                    $lengthMenu.parent().addClass('d-flex align-items-center');
                    
                    $('#scheduleTable_length').addClass('d-flex align-items-center');
                }
            });

            var url = $('#scheduleGetDatatables').attr('href');
            var table = $('#scheduleTable').DataTable({
                ajax: {
                    url: url,
                    beforeSend: function() {
                        $('.loader-area').block({ 
                            message: '<i class="fa fa-spinner fa-spin"></i> Loading...', 
                            overlayCSS: {
                                backgroundColor: '#fff',
                                opacity: 0.8,
                                cursor: 'wait'
                            },
                            css: {
                                border: 0,
                                padding: 0,
                                backgroundColor: 'none'
                            }
                        }); // Show loader before request
                    },
                    complete: function() {
                        $('.loader-area').unblock(); // Hide loader after request complete
                    }
                },
                processing: true,
                serverSide: true,
                fnCreatedRow: function(row, data, index) {
                    var info = table.page.info();
                    var value = index + 1 + info.start + '.';
                    $('td', row).eq(0).html(value);
                },
                columns: [
                    {data: null, sortable: false, orderable: false, searchable: false},
                    {data: 'name', name: 'name'},
                    {data: 'shiftDate', name: 'shiftDate'},
                    {data: 'startTime', name: 'startTime'},
                    {data: 'endTime', name: 'endTime'},
                    {data: 'shift', name: 'shift', className: 'text-center font-weight-bold'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                error: function(xhr, errorType, exception) {
                    console.log('Ajax error: ' + xhr.status + ' ' + xhr.statusText);
                }
            });

            // delete schedule
            $('#scheduleTable').on('click', '.delete-schedule', function(e) {
                console.log('it work');
                e.preventDefault();

                var scheduleId = $(this).data('schedule-id');
                console.log('ini id jadwal : ' + scheduleId);
                var deleteUrl = '{{ route("schedule.destroy", ":id") }}'.replace(':id', scheduleId);

                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin menghapus jadwal ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: 'green',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: deleteUrl,
                            type: 'DELETE',
                            data: {
                                "_token": "{{ csrf_token() }}"
                            },
                            beforeSend: function() {
                                $('.loader-area').block({ 
                                    message: '<i class="fa fa-spinner fa-spin"></i> Loading...', 
                                    overlayCSS: {
                                        backgroundColor: '#fff',
                                        opacity: 0.8,
                                        cursor: 'wait'
                                    },
                                    css: {
                                        border: 0,
                                        padding: 0,
                                        backgroundColor: 'none'
                                    }
                                }); // Show loader before request
                            },
                            success: function(response) {
                                $('.loader-area').unblock(); // Hide loader after request complete
                                if (response.success == true) {
                                    Swal.fire({
                                        title: 'Berhasil',
                                        text: response.message,
                                        icon: 'success',
                                        timer: 2000, 
                                        showCancelButton: false,
                                        showConfirmButton: false,
                                        willClose: () => {
                                            table.ajax.reload();
                                        }
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Gagal',
                                        text: response.message,
                                        icon: 'error'
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
                                $('.loader-area').unblock();
                                var response = JSON.parse(xhr.responseText);
                                if (response.error) {
                                    errorMessage = xhr.status + ' ' + xhr.statusText + ': ' + response.error;
                                }
                                Swal.fire({
                                    title: 'Gagal',
                                    text: errorMessage,
                                    icon: 'error',
                                    timer: 3000,
                                    showCancelButton: false,
                                    showConfirmButton: false,
                                    willClose: () => {
                                        table.ajax.reload();
                                    }
                                });;
                            }
                        });
                    }
                });
            });

        });
    </script>
@endsection