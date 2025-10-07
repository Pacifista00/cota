<!-- Navbar -->
<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl " id="navbarBlur"
    data-scroll="false">
    <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                <li class="breadcrumb-item text-sm"><a class="opacity-5 text-white" href="javascript:;">Pages</a></li>
                <li class="breadcrumb-item text-sm text-white active" aria-current="page">{{ $active }}</li>
            </ol>
            <h6 class="font-weight-bolder text-white mb-0 text-capitalize">{{ $active }}</h6>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                <div class="input-group">

                </div>
            </div>
            <ul class="navbar-nav  justify-content-end">
                <!-- Notification Icon -->
                @php
                    $unreadCount = auth()->user()->unreadNotifications()->count();
                @endphp
                <li class="nav-item dropdown pe-2 d-flex align-items-center">
                    <a href="#" class="nav-link text-white p-0 position-relative" id="dropdownNotification"
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-bell cursor-pointer" style="font-size: 1.2rem;"></i>
                        @if($unreadCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                  style="font-size: 0.65rem;">
                                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                            </span>
                        @endif
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end px-2 py-3 me-sm-n4"
                        aria-labelledby="dropdownNotification"
                        style="min-width: 350px; max-height: 400px; overflow-y: auto;">

                        <li class="mb-2">
                            <div class="d-flex justify-content-between align-items-center px-3">
                                <h6 class="font-weight-bolder mb-0">Notifikasi</h6>
                                @if($unreadCount > 0)
                                    <form action="{{ url('/notifikasi/mark-all-as-read') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-link btn-sm text-primary p-0">
                                            Tandai Semua Dibaca
                                        </button>
                                    </form>
                                @endif
                            </div>
                            <hr class="horizontal dark mt-2">
                        </li>

                        @forelse(auth()->user()->notifications()->limit(5)->get() as $notification)
                            <li>
                                <a class="dropdown-item border-radius-md {{ is_null($notification->read_at) ? 'bg-light' : '' }}"
                                   href="{{ $notification->data['action_url'] ?? '#' }}"
                                   onclick="markAsRead('{{ $notification->id }}')">
                                    <div class="d-flex py-1">
                                        <div class="my-auto">
                                            <i class="ni ni-{{ $notification->data['icon'] ?? 'bell-55' }}
                                               text-{{ $notification->data['color'] ?? 'primary' }}
                                               me-3" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="text-sm font-weight-normal mb-1">
                                                {{ $notification->data['title'] ?? 'Notifikasi' }}
                                            </h6>
                                            <p class="text-xs text-secondary mb-0">
                                                {{ Str::limit($notification->data['message'] ?? '', 60) }}
                                            </p>
                                            <p class="text-xs text-muted mb-0">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @empty
                            <li class="px-3 py-2">
                                <p class="text-center text-sm text-muted mb-0">Tidak ada notifikasi</p>
                            </li>
                        @endforelse

                        @if(auth()->user()->notifications()->count() > 5)
                            <li>
                                <hr class="horizontal dark mt-2 mb-2">
                                <a href="{{ url('/notifikasi') }}" class="dropdown-item text-center text-sm">
                                    Lihat Semua Notifikasi
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>

                <li class="nav-item d-flex align-items-center">
                    <a href="javascript:;" class="nav-link text-white font-weight-bold px-0">
                        <form action="{{ url('/logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                style="background: transparent; border: none; color: inherit; padding: 0;"
                                class="d-sm-inline d-none">
                                <i class="fa fa-sign-out-alt me-sm-1"></i> Logout
                            </button>
                        </form>

                    </a>
                </li>
                <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                    <a href="javascript:;" class="nav-link text-white p-0" id="iconNavbarSidenav">
                        <div class="sidenav-toggler-inner">
                            <i class="sidenav-toggler-line bg-white"></i>
                            <i class="sidenav-toggler-line bg-white"></i>
                            <i class="sidenav-toggler-line bg-white"></i>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!-- End Navbar -->

<script>
function markAsRead(notificationId) {
    fetch(`/notifikasi/${notificationId}/mark-as-read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
        },
    }).catch(err => console.error('Error marking notification as read:', err));
}
</script>
