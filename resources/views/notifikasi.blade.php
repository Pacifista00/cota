@extends('layouts.main')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>Notifikasi</h6>
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
                    @forelse($notifications as $notification)
                        <div class="border-bottom {{ is_null($notification->read_at) ? 'bg-light' : '' }}">
                            <div class="d-flex align-items-start p-3">
                                <div class="icon icon-shape icon-sm border-radius-md
                                            bg-gradient-{{ $notification->data['color'] ?? 'primary' }}
                                            text-center me-3 flex-shrink-0">
                                    <i class="ni ni-{{ $notification->data['icon'] ?? 'bell-55' }}
                                       text-white opacity-10"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        {{ $notification->data['title'] ?? 'Notifikasi' }}
                                        @if(is_null($notification->read_at))
                                            <span class="badge badge-sm bg-gradient-primary">Baru</span>
                                        @endif
                                    </h6>
                                    <p class="text-sm mb-1">{{ $notification->data['message'] ?? '' }}</p>
                                    <p class="text-xs text-muted mb-0">
                                        {{ $notification->created_at->format('d M Y, H:i') }}
                                        ({{ $notification->created_at->diffForHumans() }})
                                    </p>
                                </div>
                                <div class="ms-auto d-flex gap-2 flex-shrink-0">
                                    @if($notification->data['action_url'] ?? false)
                                        <a href="{{ $notification->data['action_url'] }}"
                                           class="btn btn-link text-primary text-sm mb-0 px-2"
                                           onclick="markAsRead('{{ $notification->id }}')">
                                            <i class="ni ni-bold-right"></i> Lihat
                                        </a>
                                    @endif
                                    @if(is_null($notification->read_at))
                                        <form action="{{ url("/notifikasi/{$notification->id}/mark-as-read") }}"
                                              method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-link text-secondary text-sm mb-0 px-2"
                                                    title="Tandai dibaca">
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
                            <i class="ni ni-bell-55 text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                            <h6 class="mt-3 text-muted">Tidak ada notifikasi</h6>
                            <p class="text-sm text-muted">Notifikasi akan muncul di sini ketika ada jadwal pakan dieksekusi.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
