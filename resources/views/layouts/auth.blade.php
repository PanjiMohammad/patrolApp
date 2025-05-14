<!DOCTYPE html>
<html>
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
<body class="hold-transition login-page">
    @yield('content')

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

    @yield('js')

</body>
</html>
