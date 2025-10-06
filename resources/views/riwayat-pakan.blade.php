@extends('layouts.main')
@section('content')
    <div class="min-height-300 bg-dark position-absolute w-100"></div>
    @include('partials.sidebar')
    <main class="main-content position-relative border-radius-lg ">
        @include('partials.navbar')
        <div class="container-fluid py-4">

            {{-- Statistics Cards --}}
            <div class="row mb-4">
                <div class="col-xl-2 col-sm-6 mb-3">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-uppercase font-weight-bold">Total</p>
                                        <h5 class="font-weight-bolder mb-0">{{ $statistics['total'] }}</h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
                                        <i class="ni ni-archive-2 text-lg opacity-10" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-sm-6 mb-3">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-uppercase font-weight-bold">Berhasil</p>
                                        <h5 class="font-weight-bolder mb-0 text-success">{{ $statistics['success'] }}</h5>
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
                <div class="col-xl-2 col-sm-6 mb-3">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-uppercase font-weight-bold">Gagal</p>
                                        <h5 class="font-weight-bolder mb-0 text-danger">{{ $statistics['failed'] }}</h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-danger shadow-danger text-center rounded-circle">
                                        <i class="ni ni-fat-remove text-lg opacity-10" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-sm-6 mb-3">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-uppercase font-weight-bold">Pending</p>
                                        <h5 class="font-weight-bolder mb-0 text-warning">{{ $statistics['pending'] }}</h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle">
                                        <i class="ni ni-time-alarm text-lg opacity-10" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-sm-6 mb-3">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-uppercase font-weight-bold">Manual</p>
                                        <h5 class="font-weight-bolder mb-0">{{ $statistics['manual'] }}</h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-secondary shadow-secondary text-center rounded-circle">
                                        <i class="ni ni-hand-click text-lg opacity-10" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-sm-6 mb-3">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-uppercase font-weight-bold">Terjadwal</p>
                                        <h5 class="font-weight-bolder mb-0">{{ $statistics['scheduled'] }}</h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-info shadow-info text-center rounded-circle">
                                        <i class="ni ni-calendar-grid-58 text-lg opacity-10" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filters Section --}}
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body p-3">
                            <form method="GET" action="{{ url('/riwayat/pakan') }}" class="row g-3 align-items-end">
                                <div class="col-md-3">
                                    <label for="filterStatus" class="form-label text-sm mb-1">Filter Status</label>
                                    <select class="form-select form-select-sm" id="filterStatus" name="status">
                                        <option value="all" {{ ($filters['status'] ?? 'all') == 'all' ? 'selected' : '' }}>
                                            Semua Status
                                        </option>
                                        <option value="success" {{ ($filters['status'] ?? '') == 'success' ? 'selected' : '' }}>
                                            Berhasil
                                        </option>
                                        <option value="failed" {{ ($filters['status'] ?? '') == 'failed' ? 'selected' : '' }}>
                                            Gagal
                                        </option>
                                        <option value="pending" {{ ($filters['status'] ?? '') == 'pending' ? 'selected' : '' }}>
                                            Pending
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="filterType" class="form-label text-sm mb-1">Filter Tipe</label>
                                    <select class="form-select form-select-sm" id="filterType" name="trigger_type">
                                        <option value="all" {{ ($filters['trigger_type'] ?? 'all') == 'all' ? 'selected' : '' }}>
                                            Semua Tipe
                                        </option>
                                        <option value="manual" {{ ($filters['trigger_type'] ?? '') == 'manual' ? 'selected' : '' }}>
                                            Manual
                                        </option>
                                        <option value="scheduled" {{ ($filters['trigger_type'] ?? '') == 'scheduled' ? 'selected' : '' }}>
                                            Terjadwal
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="perPage" class="form-label text-sm mb-1">Per Halaman</label>
                                    <select class="form-select form-select-sm" id="perPage" name="per_page">
                                        <option value="10" {{ ($filters['per_page'] ?? 20) == 10 ? 'selected' : '' }}>10</option>
                                        <option value="20" {{ ($filters['per_page'] ?? 20) == 20 ? 'selected' : '' }}>20</option>
                                        <option value="50" {{ ($filters['per_page'] ?? 20) == 50 ? 'selected' : '' }}>50</option>
                                        <option value="100" {{ ($filters['per_page'] ?? 20) == 100 ? 'selected' : '' }}>100</option>
                                    </select>
                                </div>
                                <div class="col-md-4 text-end">
                                    <button type="submit" class="btn btn-primary btn-sm mb-0">
                                        <i class="ni ni-zoom-split-in text-xs"></i> Terapkan Filter
                                    </button>
                                    <a href="{{ url('/riwayat/pakan') }}" class="btn btn-outline-secondary btn-sm mb-0">
                                        <i class="ni ni-curved-next text-xs"></i> Reset
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Table --}}
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header pb-0 p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Riwayat Pemberian Pakan</h6>
                                <span class="text-sm text-secondary">
                                    Menampilkan {{ $feedHistories->firstItem() ?? 0 }} - {{ $feedHistories->lastItem() ?? 0 }}
                                    dari {{ $feedHistories->total() }} data
                                </span>
                            </div>
                        </div>
                        <div class="card-body px-0 pt-0 pb-2">
                            {{-- Desktop Table View --}}
                            <div class="table-responsive p-0 d-none d-md-block">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Waktu Eksekusi
                                            </th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                Tipe Trigger
                                            </th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                Nama Jadwal
                                            </th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Status
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($feedHistories as $feedItem)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">
                                                            {{ $feedItem->updated_at->format('d/m/Y H:i') }}
                                                        </h6>
                                                        <p class="text-xs text-secondary mb-0">
                                                            {{ $feedItem->updated_at->diffForHumans() }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    // Trigger type badge logic
                                                    $triggerConfig = [
                                                        'manual' => ['color' => 'secondary', 'icon' => 'ni-hand-click', 'label' => 'Manual'],
                                                        'scheduled' => ['color' => 'primary', 'icon' => 'ni-calendar-grid-58', 'label' => 'Terjadwal'],
                                                    ];
                                                    $triggerData = $triggerConfig[$feedItem->trigger_type] ?? $triggerConfig['manual'];
                                                @endphp
                                                <span class="badge badge-sm badge-{{ $triggerData['color'] }}">
                                                    <i class="ni {{ $triggerData['icon'] }} text-xs"></i> {{ $triggerData['label'] }}
                                                </span>
                                            </td>
                                            <td class="align-middle">
                                                @if($feedItem->schedule)
                                                    <span class="text-secondary text-xs font-weight-bold">
                                                        {{ $feedItem->schedule->name }}
                                                    </span>
                                                @else
                                                    <span class="text-secondary text-xs">-</span>
                                                @endif
                                            </td>
                                            <td class="align-middle text-center">
                                                @php
                                                    // Status badge logic
                                                    $statusColors = [
                                                        'success' => 'success',
                                                        'failed' => 'danger',
                                                        'pending' => 'warning',
                                                    ];
                                                    $statusValue = is_object($feedItem->status) ? $feedItem->status->value : $feedItem->status;
                                                    $statusColor = $statusColors[$statusValue] ?? 'secondary';
                                                    $statusLabel = is_object($feedItem->status) ? $feedItem->status->label() : ucfirst($statusValue);
                                                @endphp
                                                <span class="badge badge-sm badge-{{ $statusColor }}">
                                                    {{ $statusLabel }}
                                                </span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4">
                                                <div class="text-center">
                                                    <i class="ni ni-archive-2 text-muted" style="font-size: 3rem;"></i>
                                                    <p class="text-sm text-secondary mt-2 mb-0">
                                                        Belum ada riwayat pemberian pakan.
                                                    </p>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            {{-- Mobile Card View --}}
                            <div class="d-md-none px-3">
                                @forelse ($feedHistories as $feedItem)
                                <div class="card mb-3">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h6 class="text-sm mb-1">
                                                    {{ $feedItem->updated_at->format('d/m/Y H:i') }}
                                                </h6>
                                                <p class="text-xs text-secondary mb-0">
                                                    {{ $feedItem->updated_at->diffForHumans() }}
                                                </p>
                                            </div>
                                            @php
                                                // Status badge logic - mobile
                                                $statusColors = [
                                                    'success' => 'success',
                                                    'failed' => 'danger',
                                                    'pending' => 'warning',
                                                ];
                                                $statusValue = is_object($feedItem->status) ? $feedItem->status->value : $feedItem->status;
                                                $statusColor = $statusColors[$statusValue] ?? 'secondary';
                                                $statusLabel = is_object($feedItem->status) ? $feedItem->status->label() : ucfirst($statusValue);
                                            @endphp
                                            <span class="badge badge-sm badge-{{ $statusColor }}">
                                                {{ $statusLabel }}
                                            </span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                @php
                                                    // Trigger type badge logic - mobile
                                                    $triggerConfig = [
                                                        'manual' => ['color' => 'secondary', 'icon' => 'ni-hand-click', 'label' => 'Manual'],
                                                        'scheduled' => ['color' => 'primary', 'icon' => 'ni-calendar-grid-58', 'label' => 'Terjadwal'],
                                                    ];
                                                    $triggerData = $triggerConfig[$feedItem->trigger_type] ?? $triggerConfig['manual'];
                                                @endphp
                                                <span class="badge badge-sm badge-{{ $triggerData['color'] }}">
                                                    <i class="ni {{ $triggerData['icon'] }} text-xs"></i> {{ $triggerData['label'] }}
                                                </span>
                                            </div>
                                            @if($feedItem->schedule)
                                                <span class="text-xs text-secondary">
                                                    {{ $feedItem->schedule->name }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="text-center py-4">
                                    <i class="ni ni-archive-2 text-muted" style="font-size: 3rem;"></i>
                                    <p class="text-sm text-secondary mt-2">
                                        Belum ada riwayat pemberian pakan.
                                    </p>
                                </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- Pagination --}}
                        @if($feedHistories->hasPages())
                        <div class="card-footer">
                            <div class="d-flex justify-content-center">
                                {{ $feedHistories->links() }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            @include('partials.footer')
        </div>
    </main>
@endsection
