<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="description" content="Zakaria Levi - Aplikasi Monitoring Satpam">
  <meta name="author" content="Zakaria Levi">
  <meta name="keyword" content="Aplikasi Monitoring Satpam">

  @yield('title')

  <!-- Font Awesome Icons -->
  {{-- <link rel="stylesheet" href="{{asset('admin-lte/plugins/fontawesome-free/css/all.min.css')}}"> --}}
  <link rel="stylesheet" href="{{asset('admin-lte/plugins/fontawesome-free-2/css/all.min.css')}}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bbootstrap 4 -->
  <link rel="stylesheet" href="{{asset('admin-lte/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('admin-lte/dist/css/adminlte.min.css')}}">
  <!-- DataTables -->
  {{-- <link rel="stylesheet" href="{{asset('admin-lte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
  <link rel="stylesheet" href="{{asset('admin-lte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}"> --}}
  <link rel="stylesheet"  href="{{asset('admin-lte/plugins/jquery-datatables/jquery.datatables.css')}}">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="{{asset('admin-lte/plugins/overlayScrollbars/css/OverlayScrollbars.min.css')}}">
  <!-- summernote -->
  <link rel="stylesheet" href="{{asset('admin-lte/plugins/summernote/summernote-bs4.css')}}">
  <!-- Date Range Picker -->
  <link rel="stylesheet" href="{{asset('admin-lte/plugins/daterangepicker/daterangepicker.css')}}">
  <!-- Jquery UI -->
  <link rel="stylesheet" href="{{asset('admin-lte/plugins/jquery-ui/jquery-ui.css')}}">
  <!-- Full Calendar -->
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet" />
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

  @yield('css')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Navbar -->
  @include('layouts.module.header')
  <!-- /.navbar -->

  <!-- Navbar -->
  @include('layouts.module.sidebar')
  <!-- /.navbar -->

  <!-- Main content -->
  @yield('content')
  <!-- Main content -->


  <!-- Main Footer -->
  <footer class="main-footer">
    <!-- To the right -->
    <div class="float-right d-none d-sm-inline">
      Anything you want
    </div>
    <!-- Default to the left -->
    <strong>Copyright &copy; 2014-2025 <a href="https://adminlte.io">AdminLTE.io</a>.</strong> All rights reserved.
    
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->
<script src="{{asset('admin-lte/plugins/jquery/jquery.min.js')}}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{asset('admin-lte/plugins/jquery-ui/jquery-ui.min.js')}}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="{{asset('admin-lte/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- ChartJS -->
<script src="{{asset('admin-lte/plugins/chart.js/Chart.min.js')}}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{asset('admin-lte/dist/js/demo.js')}}"></script>
<!-- daterangepicker -->
<script src="{{asset('admin-lte/plugins/moment/moment.min.js')}}"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="{{asset('admin-lte/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js')}}"></script>
<!-- Summernote -->
<script src="{{asset('admin-lte/plugins/summernote/summernote-bs4.min.js')}}"></script>
<!-- overlayScrollbars -->
<script src="{{asset('admin-lte/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('admin-lte/dist/js/adminlte.js')}}"></script>
<!-- Sweet Alert -->
<script src="{{ asset('admin-lte/plugins/sweetalert2/sweetalert2-new.min.js') }}"></script>
<!-- Bootbox -->
<script src="{{ asset('admin-lte/plugins/bootbox/bootbox.min.js') }}"></script>
<!-- Block UI -->
<script src="{{ asset('admin-lte/plugins/block-ui/jquery.blockUI.min.js') }}"></script>
<!-- Date Range Picker -->
<script src="{{ asset('admin-lte/plugins/daterangepicker/daterangepicker-new.min.js') }}"></script>
<!-- Multi Dates Picker -->
<script src="{{ asset('admin-lte/plugins/multidatespicker/jquery-multidatespicker.js') }}"></script>
<!-- DataTable -->
<script src="{{ asset('admin-lte/plugins/jquery-datatables/jquery.datatables.min.js') }}"></script>
<!-- Full Calendar -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales-all.min.js"></script>
<!-- Moment -->
<script src="{{ asset('admin-lte/plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('admin-lte/plugins/moment/moment-with-locales.min.js') }}"></script>
<script>
  $(document).ready(function() {
      $('#logout-button').click(function(event) {
          event.preventDefault();

          Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah anda ingin logout?',
                icon: 'question',
                showCancelButton: true,
                cancelButtonColor: '#d33',
                confirmButtonColor: 'green',
                cancelButtonText: 'Tidak',
                confirmButtonText: 'Ya, Lanjut',
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                  $.ajax({
                    url: $('#logout-form').attr('action'),
                    method: 'POST',
                    data: {
                        _token: $('input[name="_token"]').val()
                    },
                    beforeSend: function() {
                        $.blockUI({ 
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
                        $.unblockUI();
                    },
                    success: function(response) {
                      if (response.success) {
                        Swal.fire({
                            title: 'Berhasil',
                            text: response.success,
                            icon: 'success',
                            timer: 1500, // Display for 2 seconds
                            showCancelButton: false,
                            showConfirmButton: false,
                            willClose: () => {
                                window.location.href = response.redirect;
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: response.success,
                            icon: 'error',
                            timer: 2000 // Display for 2 seconds
                        });
                    }
                  },
                  error: function(xhr, status, error) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.error) {
                      errorMessage = xhr.status + ' ' + xhr.statusText + ': ' + response.error;
                    }
                    Swal.fire({
                        title: 'Gagal',
                        text: errorMessage,
                        icon: 'error',
                        timer: 2000, // Display for 2 seconds
                        showCancelButton: false,
                        showConfirmButton: false,
                        willClose: () => {
                            window.location.reload();
                        }
                    });
                  }
                });
                }
            });
          
      });
  });
</script>

@yield('js')

</body>
</html>
