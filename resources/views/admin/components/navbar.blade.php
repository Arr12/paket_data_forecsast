<section>
    <!-- Left Sidebar -->
    <aside id="leftsidebar" class="sidebar desktop-toggle-hide">
        <!-- User Info -->
        <div class="user-info">
            <div class="image">
                <img src="/images/user.png" width="48" height="48" alt="User" />
            </div>
            <div class="info-container">
                <div class="name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{auth()->user()->name}}</div>
                <div class="email">{{auth()->user()->email}}</div>
                <div class="btn-group user-helper-dropdown">
                    <i class="material-icons" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">keyboard_arrow_down</i>
                    <ul class="dropdown-menu pull-right">
                        {{-- <li><a href="javascript:void(0);"><i class="material-icons">person</i>Profile</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="javascript:void(0);"><i class="material-icons">group</i>Followers</a></li>
                        <li><a href="javascript:void(0);"><i class="material-icons">shopping_cart</i>Sales</a></li>
                        <li><a href="javascript:void(0);"><i class="material-icons">favorite</i>Likes</a></li>
                        <li role="separator" class="divider"></li> --}}
                        <li>
                            <a class="sign-out" href="{{route('actionlogout')}}"><i class="material-icons">input</i>Sign Out</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- #User Info -->
        <!-- Menu -->
        <div class="menu">
            <ul class="list">
                <li class="header">MAIN NAVIGATION</li>
                <li class={{ set_active('home') }}>
                    <a href="{{route('dashboard')}}">
                        <i class="material-icons">home</i>
                        <span>Home</span>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0);" class="menu-toggle">
                        <i class="material-icons">import_contacts</i>
                        <span>Data Master</span>
                    </a>
                    <ul class="ml-menu">
                        <li class={{ set_active('master-provider') }}>
                            <a href="{{route('master-provider')}}">
                                <span>Master Provider</span>
                            </a>
                        </li>
                        <li class={{ set_active('master-barang') }}>
                            <a href="{{route('master-barang')}}">
                                <span>Master Barang</span>
                            </a>
                        </li>
                        @php
                            $roles = Session::get('roles');
                        @endphp
                        @if($roles === 'Administrator')
                            <li class={{ set_active('master-user') }}>
                                <a href="{{route('master-user')}}">
                                    <span>Master User</span>
                                </a>
                            </li>
                            <li class={{ set_active('master-role') }}>
                                <a href="{{route('master-role')}}">
                                    <span>Master Roles</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
                <li class={{ set_active('transaksi') }}>
                    <a href="{{route('transaksi')}}">
                        <i class="material-icons">paid</i>
                        <span>Transaksi</span>
                    </a>
                </li>
                <li class={{ set_active('forecasting') }}>
                    <a href="{{route('forecasting')}}">
                        <i class="material-icons">trending_up</i>
                        <span>Forecasting</span>
                    </a>
                </li>
                <li class={{ set_active('pemesanan') }}>
                    <a href="{{route('pemesanan')}}">
                        <i class="material-icons">local_shipping</i>
                        <span>Pemesanan dan Stok</span>
                    </a>
                </li>
                <li class={{ set_active('laporan') }}>
                    <a href="{{route('laporan')}}">
                        <i class="material-icons">task_alt</i>
                        <span>Laporan</span>
                    </a>
                </li>
            </ul>
        </div>
        <!-- #Menu -->
        <!-- Footer -->
        <div class="legal">
            <div class="copyright">
                &copy; 2021 <a href="javascript:void(0);">All rights reserved</a>.
            </div>
            <div class="version">
                <b>Version: </b> 1.0.0
            </div>
        </div>
        <!-- #Footer -->
    </aside>
    <!-- #END# Left Sidebar -->
</section>
