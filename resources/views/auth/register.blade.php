@extends('layouts.auth')

@section('title')
<title>Register</title>
@endsection

@section('content')
    <section class="content">
        <div class="container" style="margin-top: 25%;">
            <div class="login_box_area p_120">
                @if (session('success'))
                    <input type="hidden" id="success-message" value="{{ session('success') }}">
                @endif
        
                @if (session('error'))
                    <input type="hidden" id="error-message" value="{{ session('error') }}">
                @endif
                <div class="login-logo">
                    <h2>Registrasi</h2>
                </div>
                
                <!-- /.login-logo -->
                <div class="card">
                    <form id="registerForm" action="{{ route('post.newRegister') }}" method="post" novalidate="novalidate" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body loader-area">
                            <div class="mb-3">
                                <a href="{{ route('login')}}" style="color: black;"><i class="fa fa-arrow-left" style="color: black;"></i> <span class="ml-1">Kembali</span></a>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Nama</label>
                                        <input type="text" class="form-control" id="name" name="name" placeholder="Full Name" required>
                                        <span class="text-danger" id="name_error"></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="text" class="form-control" id="email" name="email" placeholder="Masukkan Email" required>
                                        <span class="text-danger" id="email_error"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone_number">Nomor Telepon</label>
                                        <input type="text" maxlength="13" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1');" class="form-control" id="phone_number" name="phone_number" placeholder="Masukkan Nomor Telepon" required>
                                        <span class="text-danger" id="phone_number_error"></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="address">Alamat</label>
                                        <input type="text" class="form-control" id="address" name="address" placeholder="Masukkan Alamat" required>
                                        <span class="text-danger" id="address_error"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-6">
                                    <!---->
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary btn-block">Registrasi</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
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

        $(document).ready(function(){

            var successMessage = $('#success-message').val();
            var errorMessage = $('#error-message').val();

            if (successMessage) {
                Swal.fire({
                    title: 'Berhasil',
                    text: successMessage,
                    icon: 'success'
                });
            }

            if (errorMessage) {
				Swal.fire({
                    title: 'Gagal',
                    text: errorMessage,
                    icon: 'error'
                });
            }

            $('#registerForm').on('submit', function(e) {
                e.preventDefault();
                let formData = $(this).serialize();
                
                $.ajax({
                    url: $(this).attr('action'),
                    method: "POST",
                    data: formData,
                    beforeSend: function() {
                        $('.loader-area').block({ 
                            message: '<i class="fa fa-spinner"></i>',
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
                            timer: 2000, // Display for 2 seconds
                            showCancelButton: false,
                            showConfirmButton: false,
                            willClose: () => {
                                window.location.href = "{{ route('login') }}";
                            }
                        });
                    },
                    error: function(xhr) {
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
                            icon: 'error',
                            title: 'Error',
                            html: errorMessage,
                            timer: 2000, // Display for 2 seconds
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
                });
            });

        })
    </script>
@endsection

@section('css')
    <style>
        .input-error {
            border: 1px solid red;
        }
    </style>
@endsection