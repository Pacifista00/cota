<aside class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4 "
    id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
            aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand m-0" href=" https://demos.creative-tim.com/argon-dashboard/pages/dashboard.html "
            target="_blank">
            <img src="{{ asset('img/logo.png') }}" width="26px" height="26px" class="navbar-brand-img h-100"
                alt="main_logo">
            <span class="ms-1 font-weight-bold">Monitoring COTA</span>
        </a>
    </div>
    <hr class="horizontal dark mt-0">
    <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link {{ $active == 'dashboard' ? 'active' : '' }}" href="{{ url('/') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-tv-2 text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>
            {{-- <li class="nav-item">
                <a class="nav-link {{ $active == 'tambak' ? 'active' : '' }}" href="{{ url('/tambak') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-map-big text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Tambak</span>
                </a>
            </li> --}}
            <li class="nav-item">
                <a class="nav-link {{ $active == 'jadwal_terjadwal' ? 'active' : '' }}" href="{{ url('/jadwal-terjadwal') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-calendar-grid-58 text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Jadwal Pakan Terjadwal</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $active == 'notifikasi' ? 'active' : '' }}" href="{{ url('/notifikasi') }}">
                    <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-bell-55 text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Notifikasi</span>
                    @php
                        $sidebarUnreadCount = auth()->user()->unreadNotifications()->count();
                    @endphp
                    @if($sidebarUnreadCount > 0)
                        <span class="badge badge-sm bg-gradient-danger ms-auto">
                            {{ $sidebarUnreadCount }}
                        </span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $active == 'riwayat_sensor' || $active == 'riwayat_pakan' ? '' : 'collapsed' }}"
                    data-bs-toggle="collapse" href="#submenuRiwayat" role="button"
                    aria-expanded="{{ $active == 'riwayat_sensor' || $active == 'riwayat_pakan' ? 'true' : 'false' }}"
                    aria-controls="submenuRiwayat">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-calendar-grid-58 text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Riwayat</span>
                </a>
                <div class="collapse {{ $active == 'riwayat_sensor' || $active == 'riwayat_pakan' ? 'show' : '' }}"
                    id="submenuRiwayat">
                    <ul class="nav ms-4 flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ $active == 'riwayat_sensor' ? 'active' : '' }}"
                                href="{{ url('/riwayat/sensor') }}">
                                Riwayat Sensor
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $active == 'riwayat_pakan' ? 'active' : '' }}"
                                href="{{ url('/riwayat/pakan') }}">
                                Riwayat Pakan
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $active == 'preview' ? 'active' : '' }}" href="{{ url('/preview') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-eye text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Preview Aplikasi</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ asset('aplikasi/COTA v1.2.1.apk') }}" download
                class="nav-link btn btn-primary bg-primary d-flex align-items-center">
                    <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-cloud-download-95 text-white text-sm opacity-10"></i>
                    </div>
                    <span class="ms-1 text-white">Download Aplikasi</span>
                </a>
            </li>

            {{-- <li class="nav-item">
                <a class="nav-link " href="../pages/billing.html">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-credit-card text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Billing</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link " href="../pages/virtual-reality.html">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-app text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Virtual Reality</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link " href="../pages/rtl.html">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-world-2 text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">RTL</span>
                </a>
            </li>
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Account pages</h6>
            </li>
            <li class="nav-item">
                <a class="nav-link " href="../pages/profile.html">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-single-02 text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Profile</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link " href="../pages/sign-in.html">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-single-copy-04 text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Sign In</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link " href="../pages/sign-up.html">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-collection text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Sign Up</span>
                </a>
            </li> --}}
        </ul>
    </div>
</aside>
