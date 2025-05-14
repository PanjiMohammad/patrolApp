<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        
        <!-- Messages Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fa fa-gear" style="color: black;"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="width: 300px;">
                <div class="ml-3 mt-2">
                    <h5 class="font-weight-bold">Pengaturan</h5>
                </div>
                <div class="dropdown-divider"></div>
                @if (Auth::guard('web')->check())
                    <div class="dropdown-item">
                        <a style="color: #000;" href="{{ route('user.acountSetting', Auth::guard('web')->user()->id) }}">
                            <span class="fa fa-gear mr-1"></span> 
                            <span class="text-capitalize">{{ Auth::guard('web')->user()?->name }}</span>
                        </a>
                    </div>
                @else
                    @if (Auth::guard('security')->check())
                        <a class="dropdown-item" style="color: #000;"
                            href="{{ route('security.acountSetting', Auth::guard('security')->user()->id) }}">
                            <div class="d-flex align-items-center">
                                <span class="fa fa-gear mr-2"></span>
                                <span>{{ Auth::guard('security')->user()?->name }}</span>
                            </div>
                        </a>
                    @endif
                @endif
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" id="logout-button">
                    <i class="fas fa-sign-out-alt mr-2"></i>Keluar
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </li>
        <!-- end of Messages Dropdown Menu -->
    </ul>
</nav>