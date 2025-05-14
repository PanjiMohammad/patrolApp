@extends('layouts.admin')

@section('title')
    <title>Detail Laporan Kejadian</title>
@endsection

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        {{-- <h1 class="m-0 text-dark">Konsumen</h1> --}}
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('absence.index') }}">Absensi</a></li>
                            <li class="breadcrumb-item active">Laporan Kejadian</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

    <!-- Main content -->
        <section class="content">
            <div class="container">
                <div class="row">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('absence.index') }}" style="color: black;"><span class="fa-solid fa-arrow-left"></span> <span class="ml-1">Kembali</span></a>
                                    <span class="font-weight-bold">Tanggal: {{ \Carbon\Carbon::parse($data->shift_date)->locale('id')->translatedFormat('l, d M Y') }}</span>
                                </div>
                            </div>
                            <form id="incidentForm" action="{{ route('absence.incidentReportStore') }}" method="post" enctype="multipart/form-data">
                                @csrf

                                <!-- schedule id -->
                                <input type="hidden" name="schedule_id" value="{{ $data->id }}">
                                <input type="hidden" name="schedule_date" value="{{ $data->shift_date }}">
                                <div class="card-body loader-area">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="shift_date">Tanggal Tugas</label>
                                                <input type="text" class="form-control" name="shift_date" id="shift_date" value="{{ \Carbon\Carbon::parse($data->shift_date)->locale('id')->translatedFormat('l, d M Y') }}" readonly>
                                                <span class="text-danger" id="shift_date_error"></span>
                                            </div>
                                            <div class="form-group">
                                                <label for="location_point">Titik Lokasi</label>
                                                <select name="location_point" id="location_point" class="form-control">
                                                    <option value="">Pilih Lokasi</option>
                                                    @foreach($location as $l)
                                                        <option value="{{ $l->id }}">{{ $l->name }}</option>
                                                    @endforeach
                                                </select>
                                                <span class="text-danger" id="location_point_error"></span>
                                            </div>
                                            <div class="form-group">
                                                <label for="image">Foto Pendukung</label>
                                                <input type="file" name="image" id="image" class="form-control">
                                                <span class="text-danger" id="image_error"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="urgency">Prioritas</label>
                                                <select name="urgency" id="urgency" class="form-control">
                                                    <option value="">-- Pilih Prioritas --</option>
                                                    <option value="Rendah">Rendah</option>
                                                    <option value="Sedang">Sedang</option>
                                                    <option value="Tinggi">Tinggi</option>
                                                </select>
                                                <span class="text-danger" id="urgency_error"></span>
                                            </div>
                                            <div class="form-group">
                                                <label for="description">Deskripsi Kejadian</label>
                                                <textarea name="description" class="form-control" id="description" cols="5" rows="5" placeholder="Masukkan Deskripsi Kejadian"></textarea>
                                                <span class="text-danger" id="description_error"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-md btn-primary float-right">Simpan</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <!-- /.content-wrapper -->
@endsection

@section('js')
    <script>

        $(document).ready(function (){

            $('#incidentForm').on('submit', function(e){
                e.preventDefault();

                var formData = new FormData(this);
                console.log(formData);
                var actionUrl = $(this).attr('action');

                $.ajax({
                    url: actionUrl,
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
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
                        }); 
                    },
                    complete: function() {
                        $('.loader-area').unblock(); // Hide loader after request complete
                    },
                    success: function(response){
                        Swal.fire({
                            title: 'Berhasil',
                            text: response.success,
                            icon: 'success',
                            timer: 2000, 
                            showCancelButton: false,
                            showConfirmButton: false,
                            willClose: () => {
                                window.location.href = "{{ route('absence.index') }}";
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        let errors = xhr.responseJSON.errors;
                        let input = xhr.responseJSON.input;

                        // Clear previous errors
                        $('.text-danger').text('');

                        var response = JSON.parse(xhr.responseText);
                        if (response.error) {
                            errorMessage = xhr.status + ' ' + xhr.statusText + ': ' + response.error;
                        }
                        Swal.fire({
                            title: 'Gagal',
                            text: errorMessage,
                            icon: 'error',
                            timer: 2000,
                            showCancelButton: false,
                            showConfirmButton: false,
                            willClose: () => {
                                if(xhr.status === 500){
                                    window.location.reload(true);
                                } else {
                                    // Display validation errors using SweetAlert
                                    let errorMessage = '';
                                    $.each(errors, function(key, error) {
                                        errorMessage += error[0] + '<br>';
                                        $('#' + key + '_error').text(error[0]);
                                        $('#' + key).addClass('input-error');

                                        // Set timeout to clear the error text after 3 seconds
                                        setTimeout(function() {
                                            $('#' + key + '_error').text('');
                                            $('#' + key).removeClass('input-error');
                                        }, 3000);
                                    });

                                    // Retain input values
                                    $.each(input, function(key, value) {
                                        $('#' + key).val(value);
                                    });
                                }
                            }
                        });
                    }
                })
            });

        });
    </script>
@endsection

@section('css')
    <style>
        .input-error {
            border: 1px solid red;
        }
    </style>
@endsection