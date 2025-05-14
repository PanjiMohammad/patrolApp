@extends('layouts.admin')

@section('title')
    <title>Tambah Anggota</title>
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
                            <li class="breadcrumb-item"><a href="{{ route('security.index') }}">Satpam</a></li>
                            <li class="breadcrumb-item active">Tambah Satpam</li>
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
                                <a href="{{ route('security.index') }}" style="color: black;"><span class="fa-solid fa-arrow-left"></span> <span class="ml-1">Kembali</span></a>
                            </div>
                            <form id="addSecurityForm" action="{{ route('security.store') }}" method="post">
                                @csrf
                                <div class="card-body loader-area">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name">Nama Lengkap</label>
                                                <input type="text" name="name" id="name" class="form-control" placeholder="Masukkan Nama">
                                                <span class="text-danger" id="name_error"></span>
                                            </div>
                                            <div class="form-group">
                                                <label for="email">Email</label>
                                                <input type="email" name="email" id="email" class="form-control" placeholder="Masukkan Email">
                                                <span class="text-danger" id="email_error"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="phone_number">Nomor Telepon</label>
                                                <input type="phone_number" name="phone_number" id="phone_number" class="form-control" placeholder="Masukkan Nomor Telepon">
                                                <span class="text-danger" id="phone_number_error"></span>
                                            </div>
                                            <div class="form-group">
                                                <label for="address">Alamat</label>
                                                <input type="text" name="address" id="address" class="form-control" placeholder="Masukkan Alamat">
                                                <span class="text-danger" id="address_error"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary btn-md float-right">Simpan</button>
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

        // display image after upload
        function previewImage(event) {
            const image = event.target.files[0];
            const preview = document.getElementById('preview');

            if (image) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(image);
            } else {
                preview.style.display = 'none';
            }
        }

        $(document).ready(function (){

            $('#addSecurityForm').on('submit', function(e){
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
                                window.location.href = "{{ route('security.index') }}";
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