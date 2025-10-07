@extends('layouts.main')

@section('content')
<div class="min-height-300 bg-dark position-absolute w-100"></div>
@include('partials.sidebar')
<main class="main-content position-relative border-radius-lg">
    @include('partials.navbar')
    <div class="container-fluid py-4">
        {{-- Statistics Cards --}}
        <div class="row mb-4">
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-3">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Notifikasi</p>
                                    <h5 class="font-weight-bolder">
                                        {{ $notifications->count() }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
                                    <i class="ni ni-bell-55 text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-3">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Belum Dibaca</p>
                                    <h5 class="font-weight-bolder">
                                        {{ $unreadCount }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle">
                                    <i class="ni ni-email-83 text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-3">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Sudah Dibaca</p>
                                    <h5 class="font-weight-bolder">
                                        {{ $notifications->count() - $unreadCount }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">
                                    <i class="ni ni-check-bold text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-3 mb-xl-0">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Hari Ini</p>
                                    <h5 class="font-weight-bolder">
                                        {{ $notifications->where('created_at', '>=', now()->startOfDay())->count() }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-info shadow-info text-center rounded-circle">
                                    <i class="ni ni-time-alarm text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Success/Error Messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <span class="alert-icon"><i class="ni ni-check-bold"></i></span>
                <span class="alert-text">{{ session('success') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <span class="alert-icon"><i class="ni ni-fat-remove"></i></span>
                <span class="alert-text">{{ session('error') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Main Notification List --}}
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Daftar Notifikasi</h6>
                        @if($unreadCount > 0)
                            <form action="{{ url('/notifikasi/mark-all-as-read') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="ni ni-check-bold"></i> Tandai Semua Dibaca ({{ $unreadCount }})
                                </button>
                            </form>
                        @endif
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            @forelse($notifications as $notification)
                                <div class="border-bottom {{ is_null($notification->read_at) ? 'bg-gradient-light' : '' }}"
                                     style="transition: all 0.3s ease;">
                                    <div class="d-flex align-items-start p-3">
                                        <div class="icon icon-shape icon-sm border-radius-md
                                                    bg-gradient-{{ $notification->data['color'] ?? 'primary' }}
                                                    text-center me-3 flex-shrink-0">
                                            <i class="ni ni-{{ $notification->data['icon'] ?? 'bell-55' }}
                                               text-white opacity-10"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="text-sm mb-1">
                                                {{ $notification->data['title'] ?? 'Notifikasi' }}
                                                @if(is_null($notification->read_at))
                                                    <span class="badge badge-sm bg-gradient-primary ms-2">Baru</span>
                                                @endif
                                            </h6>
                                            <p class="text-xs mb-1">{{ $notification->data['message'] ?? '' }}</p>
                                            <p class="text-xxs text-muted mb-0">
                                                <i class="ni ni-calendar-grid-58"></i>
                                                {{ $notification->created_at->format('d M Y, H:i') }}
                                                <span class="ms-2">({{ $notification->created_at->diffForHumans() }})</span>
                                            </p>
                                        </div>
                                        <div class="ms-auto d-flex gap-1 flex-shrink-0">
                                            @if($notification->data['action_url'] ?? false)
                                                <a href="{{ $notification->data['action_url'] }}"
                                                   class="btn btn-link text-primary text-sm mb-0 px-2"
                                                   onclick="markAsRead('{{ $notification->id }}')"
                                                   title="Lihat Detail">
                                                    <i class="ni ni-bold-right"></i>
                                                </a>
                                            @endif
                                            @if(is_null($notification->read_at))
                                                <form action="{{ url("/notifikasi/{$notification->id}/mark-as-read") }}"
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-link text-success text-sm mb-0 px-2"
                                                            title="Tandai Dibaca">
                                                        <i class="ni ni-check-bold"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ url("/notifikasi/{$notification->id}") }}"
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link text-danger text-sm mb-0 px-2"
                                                        onclick="return confirm('Hapus notifikasi ini?')"
                                                        title="Hapus">
                                                    <i class="ni ni-fat-remove"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="p-5 text-center">
                                    <div class="icon icon-shape icon-xxl bg-gradient-light shadow text-center border-radius-xl mb-3 mx-auto">
                                        <i class="ni ni-bell-55 text-primary" style="font-size: 3rem; opacity: 0.6;"></i>
                                    </div>
                                    <h5 class="text-muted mb-2">Tidak Ada Notifikasi</h5>
                                    <p class="text-sm text-muted mb-0">
                                        Notifikasi akan muncul di sini ketika ada jadwal pakan yang dieksekusi.
                                    </p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('partials.footer')
    </div>
</main>

<script>
function markAsRead(notificationId) {
    fetch(`/notifikasi/${notificationId}/mark-as-read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
        },
    });
}
</script>
@endsection
