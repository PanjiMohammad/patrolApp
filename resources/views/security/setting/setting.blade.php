@extends('layouts.security')

@section('title')
    <title>Edit Profil</title>
@endsection

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    {{-- <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Edit Profil</h1>
                    </div> --}}
                    <div class="col-sm-12">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('security.dashboard') }}">Beranda</a></li>
                            <li class="breadcrumb-item active">Edit Profil</li>
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
                                <a href="{{ route('security.dashboard') }}" style="color: black;"><span class="fa-solid fa-arrow-left"></span> <span class="ml-1">Kembali</span></a>
                            </div>
                            <form id="updateProfileForm" action="{{ route('security.postAccountSetting') }}" method="post">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="security_id" value="{{ $security->id }}">
                                <div class="card-body loader-area">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name">Nama Lengkap</label>
                                                <input type="text" name="name" id="name" class="form-control" required value="{{ $security->name }}">
                                                <span class="text-danger" id="name_error"></span>
                                            </div>
                                            <div class="form-group">
                                                <label for="email">Email</label>
                                                <input type="email" name="email" id="email" class="form-control" required value="{{ $security->email }}" readonly>
                                                <span class="text-danger" id="email_error"></span>
                                            </div>
                                            <div class="form-group">
                                                <label for="password">Password</label>
                                                <input type="password" name="password" class="form-control" placeholder="*****">
                                                <p class="text-danger">* Biarkan kosong jika tidak ingin mengganti password</p>
                                                <span class="text-danger" id="password_error"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="phone_number">Nomor Telpon</label>
                                                <input type="text" name="phone_number" id="phone_number" class="form-control" placeholder="Masukkan Nomor Telepon" value="{{ $security->phone_number }}">
                                                <span class="text-danger" id="phone_number_error"></span>
                                            </div>
                                            <div class="form-group">
                                                <label for="address">Alamat</label>
                                                <input type="text" name="address" id="address" class="form-control" placeholder="Masukkan Alamat" value="{{ $security->address }}">
                                                {{-- <textarea name="address" id="address" class="form-control" placeholder="Masukkan Alamat" cols="30" rows="4">{{ $customer->address }}</textarea> --}}
                                                <span class="text-danger" id="address_error"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="float-right">
                                        <button class="btn btn-primary btn-md">Ubah</button>
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

        $(document).ready(function(){
            
            // Submit form via AJAX
            $('#updateProfileForm').on('submit', function(event) {
                event.preventDefault(); // Prevent default form submission

                var url = $(this).attr('action');
                var method = $(this).attr('method');
                var formData = $(this).serialize();

                $.ajax({
                    url: url,
                    method: method,
                    data: formData,
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
                        $('.loader-area').unblock();
                    },
                    success: function(response) {
                        Swal.fire({
                            title: 'Berhasil',
                            text: response.success,
                            icon: 'success',
                            timer: 2000, 
                            showCancelButton: false,
                            showConfirmButton: false,
                            willClose: () => {
                                window.location.href = "{{ route('security.dashboard') }}";
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        // Handle error response
                        let errors = xhr.responseJSON.errors;
                        let input = xhr.responseJSON.input;

                        // Clear previous errors
                        $('.text-danger').text('');

                        // response error
                        var response = JSON.parse(xhr.responseText);
						if (response.error) {
							errorMessage = xhr.status + ' ' + xhr.statusText + ': ' + response.error;
						}
                        Swal.fire({
                            title: 'Error',
                            text: errorMessage,
                            icon: 'error',
                            timer: 4000,
                            showCancelButton: false,
                            showConfirmButton: false,
                            willClose: () => {
                                if(xhr.status == 500){
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
                });
            });
        });
    </script>
@endsection