@extends('layouts.auth')

@section('title')
    <title>Reset Password</title>
@endsection

@section('content')
    <div class="login-box">
        <div class="login-logo">
            <p>Lupa Password</p>
        </div>
        
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                <div class="mb-3">
                    <a href="{{ route('login') }}" style="color: black;"><span class="fa-solid fa-arrow-left"></span> <span class="ml-1">Kembali</span></a>
                </div>

                <form id="reset-form" action="{{ route('security.postForgotPassword') }}"
                    data-default-action="{{ route('security.postForgotPassword') }}"
                    data-admin-action="{{ route('postForgotPassword') }}"
                    method="post">
                    @csrf
                    <div class="form-group">
                        <div class="input-group" id="email">
                            <input class="form-control" type="email" name="email" placeholder="Masukkan Email">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <i class="fas fa-at"></i>
                                </div>
                            </div>
                        </div>
                        <span class="text-danger" id="email_error"></span>
                    </div>
                    <div class="float-right">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Reset</button>
                        </div>
                    </div>
                </form>
            </div>
        <!-- /.login-card-body -->
        </div>
    </div>
    <!-- /.login-box -->
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        $(document).ready(function () {

            // Handle form submission via Ajax
            function updateFormForEmail() {
                var email = $('input[name="email"]').val();
                var defaultAction = $('#reset-form').data('default-action');
                var adminAction = $('#reset-form').data('admin-action');

                if (email === 'admin@admin.com') {
                    $('button[type="submit"]').text('Reset Password Admin');
                    $('#reset-form').attr('action', adminAction);
                } else {
                    $('button[type="submit"]').text('Reset');
                    $('#reset-form').attr('action', defaultAction);
                }
            }

            // Trigger the update function when the email input changes
            $('input[name="email"]').on('keyup change', function () {
                console.log($(this).val());
                updateFormForEmail();
            });

            // Handle form submission via Ajax
            $('#reset-form').on('submit', function (e) {
                e.preventDefault();

                var formData = $(this).serialize();
                var actionUrl = $(this).attr('action');
                console.log(actionUrl);

                $.ajax({
                    url: actionUrl,
                    type: 'POST',
                    data: formData,
                    beforeSend: function() {
                        $('.login-card-body').block({ 
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
                    complete: function(){
                        $('.login-card-body').unblock();
                    },
                    success: function (response) {
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
                    error: function (xhr) {
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
                                if(xhr.status === 404){
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

@section('css')
    <style>
        .input-error {
            border: 1px solid red;
            border-radius: 5px;
        }
    </style>
@endsection