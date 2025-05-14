  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link">
        <img src="{{ asset('admin-lte/dist/img/AdminLTELogo.png')}}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
            style="opacity: .8">
        <span class="brand-text font-weight-light">AdminLTE 3</span>
    </a>
    <!-- Sidebar -->
    <div class="sidebar">

        <!-- Sidebar Menu -->
        <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            @if(auth()->guard('web')->check())
                <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a href="{{ route('home') }}" class="nav-link {{Request::path() == 'administrator/home' ? 'active' : ''}}">
                        <i class="nav-icon fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-header">MANAJEMEN DATA</li>
                <li class="nav-item">
                    <a href="{{ route('security.index') }}" class="nav-link {{Request::path() == 'administrator/security' ? 'active' : ''}}">
                        <i class="nav-icon fa-solid fa-circle-user"></i>
                        <p>Kelola Satpam</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('location.index') }}" class="nav-link {{Request::path() == 'administrator/location' ? 'active' : ''}}">
                        <i class="nav-icon fa-solid fa-location-dot"></i>
                        <p>Kelola Lokasi</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('schedule.index') }}" class="nav-link {{Request::path() == 'administrator/schedule' ? 'active' : ''}}">
                        <i class="nav-icon fa-regular fa-calendar"></i>
                        <p>Jadwal Tugas</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('report.index') }}" class="nav-link {{Request::path() == 'administrator/report' ? 'active' : ''}}">
                        <i class="nav-icon fa-solid fa-file"></i>
                        <p>Laporan</p>
                    </a>
                </li>
            @else
                @if(auth()->guard('security')->check())
                <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a href="{{ route('security.dashboard') }}" class="nav-link {{Request::path() == 'security/dashboard' ? 'active' : ''}}">
                        <i class="nav-icon fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-header">MANAJEMEN DATA</li>
                <li class="nav-item">
                    <a href="{{ route('absence.index') }}" class="nav-link {{Request::path() == 'security/absence' ? 'active' : ''}}">
                        <i class="nav-icon fa-solid fa-circle-user"></i>
                        <p>Kelola Absensi</p>
                    </a>
                </li>
                {{-- <li class="nav-item">
                    <a href="{{ route('location.index') }}" class="nav-link {{Request::path() == 'administrator/location' ? 'active' : ''}}">
                        <i class="nav-icon fa-solid fa-location-dot"></i>
                        <p>Kelola Laporan Kejadian</p>
                    </a>
                </li> --}}
                @endif
            @endif
            {{-- <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-book"></i>
                    <p>Laporan<i class="right fas fa-angle-right"></i></p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('report.order') }}" class="nav-link {{Request::path() == 'administrator/reports/order' ? 'active' : ''}}">
                            <i class="nav-icon fas fa-warehouse"></i>
                            <p>Laporan Order</p>
                        </a>
                    </li>
                </ul>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('report.return') }}" class="nav-link {{Request::path() == 'administrator/reports/return' ? 'active' : ''}}">
                            <i class="nav-icon fas fa-warehouse"></i>
                            <p>Laporan Order Return</p>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
                <i class="nav-icon fas fa-cogs"></i>
                <p>Pengaturan<i class="right fas fa-angle-right"></i></p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-warehouse"></i>
                    <p>Toko</p>
                </a>
                </li>
            </ul> --}}
            </li>
        </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
  <!-- Main Sidebar Container -->