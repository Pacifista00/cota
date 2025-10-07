@extends('layouts.main')
@section('content')
<div class="min-height-300 bg-dark position-absolute w-100"></div>
@include('partials.sidebar')
<main class="main-content position-relative border-radius-lg ">
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
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Jadwal</p>
                                    <h5 class="font-weight-bolder">
                                        {{ $statistics['total'] ?? 0 }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div
                                    class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
                                    <i class="ni ni-calendar-grid-58 text-lg opacity-10" aria-hidden="true"></i>
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
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Aktif</p>
                                    <h5 class="font-weight-bolder">
                                        {{ $statistics['active'] ?? 0 }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div
                                    class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">
                                    <i class="ni ni-check-bold text-lg opacity-10" aria-hidden="true"></i>
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
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Non-Aktif</p>
                                    <h5 class="font-weight-bolder">
                                        {{ $statistics['inactive'] ?? 0 }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div
                                    class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle">
                                    <i class="ni ni-button-pause text-lg opacity-10" aria-hidden="true"></i>
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
                                        {{ $statistics['executed_today'] ?? 0 }}
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

        {{-- Main Table --}}
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Jadwal Pakan Terjadwal</h6>
                            </div>
                            <div class="col-md-6 text-end">
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#modalTambahJadwal">
                                    <i class="ni ni-fat-add"></i> Tambah Jadwal
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        {{-- Desktop Table View --}}
                        <div class="table-responsive p-0 d-none d-md-block">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Nama Jadwal</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Waktu Pakan</th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Periode</th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Status</th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Eksekusi Berikutnya</th>
                                        <th class="text-secondary opacity-7"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($schedules as $schedule)
                                    <tr class="hover-shadow">
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div>
                                                    <div
                                                        class="icon icon-shape icon-sm border-radius-md bg-gradient-{{ $schedule->is_active ? 'primary' : 'secondary' }} d-flex align-items-center justify-content-center me-2">
                                                        <i
                                                            class="ni ni-calendar-grid-58 text-white text-sm opacity-10"></i>
                                                    </div>
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">
                                                        {{ $schedule->name ?? 'Jadwal #' . $schedule->id }}</h6>
                                                    @if ($schedule->description)
                                                    <p class="text-xs text-secondary mb-0"
                                                       title="{{ $schedule->description }}"
                                                       data-bs-toggle="tooltip"
                                                       data-bs-placement="top">
                                                        {{ Str::limit($schedule->description, 50) }}
                                                    </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <p class="text-xs font-weight-bold mb-0">
                                                {{ \Carbon\Carbon::parse($schedule->waktu_pakan)->format('H:i') }}</p>
                                            <p class="text-xs text-secondary mb-0">{{ $schedule->frequency_type_label }}
                                            </p>
                                        </td>
                                        <td class="align-middle text-center">
                                            <p class="text-xs font-weight-bold mb-0">
                                                {{ $schedule->start_date ? $schedule->start_date->format('d/m/y') : '-' }}
                                                -
                                                {{ $schedule->end_date ? $schedule->end_date->format('d/m/y') : 'Unlimited' }}
                                            </p>
                                        </td>
                                        <td class="align-middle text-center">
                                            @php
                                                // Status badge configuration
                                                if (!$schedule->is_active) {
                                                    $statusBadge = ['color' => 'secondary', 'icon' => 'ni-button-pause', 'text' => 'Non-aktif'];
                                                } elseif (!$schedule->is_valid) {
                                                    $statusBadge = ['color' => 'warning', 'icon' => 'ni-time-alarm', 'text' => 'Expired'];
                                                } elseif ($schedule->was_executed_today) {
                                                    $statusBadge = ['color' => 'info', 'icon' => 'ni-check-bold', 'text' => 'Tereksekusi'];
                                                } else {
                                                    $statusBadge = ['color' => 'success', 'icon' => 'ni-check-bold', 'text' => 'Aktif'];
                                                }
                                            @endphp
                                            <span class="badge bg-{{ $statusBadge['color'] }} text-white text-xs">
                                                <i class="ni {{ $statusBadge['icon'] }}"></i> {{ $statusBadge['text'] }}
                                            </span>
                                            @if ($schedule->remaining_days !== null && $schedule->remaining_days <= 7)
                                            <p class="text-xs text-secondary mb-0 mt-1">
                                                @if ($schedule->remaining_days == 0)
                                                Berakhir hari ini
                                                @elseif($schedule->remaining_days == 1)
                                                1 hari lagi
                                                @else
                                                {{ $schedule->remaining_days }} hari lagi
                                                @endif
                                            </p>
                                            @endif
                                        </td>
                                        <td class="align-middle text-center">
                                            @if ($schedule->next_execution)
                                            <p class="text-xs font-weight-bold mb-0">
                                                {{ \Carbon\Carbon::parse($schedule->next_execution)->format('d/m/y H:i') }}
                                            </p>
                                            <p class="text-xs text-secondary mb-0">
                                                {{ \Carbon\Carbon::parse($schedule->next_execution)->diffForHumans() }}
                                            </p>
                                            @else
                                            <p class="text-xs text-secondary mb-0">-</p>
                                            @endif
                                        </td>
                                        <td class="align-middle text-center">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary mb-0" type="button"
                                                    id="dropdownMenu{{ $schedule->id }}" data-bs-toggle="dropdown"
                                                    aria-expanded="false" title="Aksi">
                                                    <i class="fa fa-ellipsis-v text-xs"></i>
                                                </button>
                                                <ul class="dropdown-menu"
                                                    aria-labelledby="dropdownMenu{{ $schedule->id }}">
                                                    <li>
                                                        <a class="dropdown-item" href="javascript:void(0)"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalEdit{{ $schedule->id }}">
                                                            <i class="ni ni-settings text-sm me-2"></i> Edit
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <form
                                                            action="{{ url('/jadwal-terjadwal/toggle/' . $schedule->id) }}"
                                                            method="POST" style="display: inline;">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item">
                                                                @if ($schedule->is_active)
                                                                <i class="ni ni-button-pause text-sm me-2"></i>
                                                                Nonaktifkan
                                                                @else
                                                                <i class="ni ni-button-play text-sm me-2"></i>
                                                                Aktifkan
                                                                @endif
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item text-danger" href="javascript:void(0)"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalHapus{{ $schedule->id }}">
                                                            <i class="ni ni-fat-remove text-sm me-2"></i> Hapus
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="text-center py-3">
                                                <div class="icon icon-shape icon-xxl bg-gradient-primary shadow-primary text-center rounded-circle mx-auto mb-3">
                                                    <i class="ni ni-calendar-grid-58 text-white text-lg"></i>
                                                </div>
                                                <h6 class="text-muted mb-2">Belum Ada Jadwal Pakan</h6>
                                                <p class="text-sm text-secondary mb-3">
                                                    Buat jadwal pertama untuk mengotomasi pemberian pakan.
                                                </p>
                                                <button type="button" class="btn btn-primary btn-sm"
                                                    data-bs-toggle="modal" data-bs-target="#modalTambahJadwal">
                                                    <i class="ni ni-fat-add me-1"></i> Buat Jadwal Pertama
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Mobile Card View --}}
                        <div class="d-md-none px-3">
                            @forelse ($schedules as $schedule)
                            <div class="card mb-3 shadow-sm">
                                <div class="card-body p-3">
                                    {{-- Header: Name & Status --}}
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="icon icon-shape icon-sm border-radius-md bg-gradient-{{ $schedule->is_active ? 'primary' : 'secondary' }} d-flex align-items-center justify-content-center me-2">
                                                <i class="ni ni-calendar-grid-58 text-white text-sm"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 text-sm">
                                                    {{ $schedule->name ?? 'Jadwal #' . $schedule->id }}
                                                </h6>
                                                @if ($schedule->description)
                                                <p class="text-xs text-secondary mb-0"
                                                   title="{{ $schedule->description }}"
                                                   data-bs-toggle="tooltip"
                                                   data-bs-placement="top">
                                                    {{ Str::limit($schedule->description, 40) }}
                                                </p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            @php
                                                // Status badge configuration
                                                if (!$schedule->is_active) {
                                                    $statusBadge = ['color' => 'secondary', 'icon' => 'ni-button-pause', 'text' => 'Non-aktif'];
                                                } elseif (!$schedule->is_valid) {
                                                    $statusBadge = ['color' => 'warning', 'icon' => 'ni-time-alarm', 'text' => 'Expired'];
                                                } elseif ($schedule->was_executed_today) {
                                                    $statusBadge = ['color' => 'info', 'icon' => 'ni-check-bold', 'text' => 'Tereksekusi'];
                                                } else {
                                                    $statusBadge = ['color' => 'success', 'icon' => 'ni-check-bold', 'text' => 'Aktif'];
                                                }
                                            @endphp
                                            <span class="badge bg-{{ $statusBadge['color'] }} text-white text-xs">
                                                <i class="ni {{ $statusBadge['icon'] }}"></i> {{ $statusBadge['text'] }}
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Details Grid --}}
                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <div class="border-start border-primary border-3 ps-2">
                                                <p class="text-xs text-secondary mb-0">Waktu Pakan</p>
                                                <p class="text-sm font-weight-bold mb-0">
                                                    {{ \Carbon\Carbon::parse($schedule->waktu_pakan)->format('H:i') }}
                                                </p>
                                                <p class="text-xs text-secondary mb-0">
                                                    {{ $schedule->frequency_type_label }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="border-start border-info border-3 ps-2">
                                                <p class="text-xs text-secondary mb-0">Eksekusi Berikutnya</p>
                                                @if ($schedule->next_execution)
                                                <p class="text-sm font-weight-bold mb-0">
                                                    {{ \Carbon\Carbon::parse($schedule->next_execution)->format('d/m H:i') }}
                                                </p>
                                                <p class="text-xs text-secondary mb-0">
                                                    {{ \Carbon\Carbon::parse($schedule->next_execution)->diffForHumans() }}
                                                </p>
                                                @else
                                                <p class="text-sm text-secondary mb-0">-</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Period Info --}}
                                    <div class="border-top pt-2 mb-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <p class="text-xs text-secondary mb-0">Periode Aktif</p>
                                                <p class="text-xs font-weight-bold mb-0">
                                                    {{ $schedule->start_date ? $schedule->start_date->format('d/m/y') : '-' }}
                                                    -
                                                    {{ $schedule->end_date ? $schedule->end_date->format('d/m/y') : 'Unlimited' }}
                                                </p>
                                            </div>
                                            @if ($schedule->remaining_days !== null && $schedule->remaining_days <= 7)
                                            <span class="badge bg-warning text-white text-xs">
                                                @if ($schedule->remaining_days == 0)
                                                Berakhir hari ini
                                                @elseif($schedule->remaining_days == 1)
                                                1 hari lagi
                                                @else
                                                {{ $schedule->remaining_days }} hari
                                                @endif
                                            </span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Action Buttons --}}
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-outline-primary flex-fill" type="button"
                                            data-bs-toggle="modal" data-bs-target="#modalEdit{{ $schedule->id }}">
                                            <i class="ni ni-settings text-xs me-1"></i> Edit
                                        </button>
                                        <form action="{{ url('/jadwal-terjadwal/toggle/' . $schedule->id) }}"
                                            method="POST" class="flex-fill">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-{{ $schedule->is_active ? 'warning' : 'success' }} w-100">
                                                @if ($schedule->is_active)
                                                <i class="ni ni-button-pause text-xs me-1"></i> Nonaktifkan
                                                @else
                                                <i class="ni ni-button-play text-xs me-1"></i> Aktifkan
                                                @endif
                                            </button>
                                        </form>
                                        <button class="btn btn-sm btn-outline-danger" type="button"
                                            data-bs-toggle="modal" data-bs-target="#modalHapus{{ $schedule->id }}">
                                            <i class="ni ni-fat-remove text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-5">
                                <div class="icon icon-shape icon-xxl bg-gradient-primary shadow-primary text-center rounded-circle mx-auto mb-3">
                                    <i class="ni ni-calendar-grid-58 text-white text-lg"></i>
                                </div>
                                <h6 class="text-muted mb-2">Belum Ada Jadwal Pakan</h6>
                                <p class="text-sm text-secondary mb-3">
                                    Buat jadwal pertama untuk mengotomasi pemberian pakan.
                                </p>
                                <button type="button" class="btn btn-primary btn-sm"
                                    data-bs-toggle="modal" data-bs-target="#modalTambahJadwal">
                                    <i class="ni ni-fat-add me-1"></i> Buat Jadwal Pertama
                                </button>
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

{{-- Modals Edit --}}
@foreach ($schedules as $schedule)
<div class="modal fade" id="modalEdit{{ $schedule->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ url('/jadwal-terjadwal/update/' . $schedule->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Jadwal Pakan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Jadwal</label>
                            <input type="text" class="form-control" name="name" value="{{ $schedule->name }}"
                                placeholder="Contoh: Pakan Pagi">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Waktu Pakan <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" name="waktu_pakan"
                                value="{{ \Carbon\Carbon::parse($schedule->waktu_pakan)->format('H:i') }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="description" rows="2"
                            placeholder="Deskripsi jadwal (opsional)">{{ $schedule->description }}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" name="start_date"
                                value="{{ $schedule->start_date?->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" class="form-control" name="end_date"
                                value="{{ $schedule->end_date?->format('Y-m-d') }}"
                                placeholder="Kosongkan untuk tidak terbatas">
                            <small class="text-muted">Kosongkan untuk tidak terbatas</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endforeach

{{-- Modals Hapus --}}
@foreach ($schedules as $schedule)
<div class="modal fade" id="modalHapus{{ $schedule->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ url('/jadwal-terjadwal/delete/' . $schedule->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hapus Jadwal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus jadwal
                        <strong>{{ $schedule->name ?? 'ini' }}</strong>?
                    </p>
                    <p class="text-sm text-danger">Tindakan ini tidak dapat dibatalkan!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endforeach

{{-- Modal Tambah Jadwal --}}
<div class="modal fade" id="modalTambahJadwal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ url('/jadwal-terjadwal/store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Jadwal Pakan Terjadwal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Jadwal</label>
                            <input type="text" class="form-control" name="name" placeholder="Contoh: Pakan Pagi"
                                value="{{ old('name') }}">
                            <small class="text-muted">Beri nama yang mudah diingat</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Waktu Pakan <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" name="waktu_pakan" required
                                value="{{ old('waktu_pakan') }}">
                            <small class="text-muted">Waktu pemberian pakan (HH:MM)</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="description" rows="2"
                            placeholder="Deskripsi jadwal (opsional)">{{ old('description') }}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" name="start_date"
                                value="{{ old('start_date', date('Y-m-d')) }}">
                            <small class="text-muted">Kapan jadwal mulai berlaku</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" class="form-control" name="end_date" value="{{ old('end_date') }}">
                            <small class="text-muted">Kosongkan untuk tidak terbatas</small>
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <small>
                            <i class="ni ni-bell-55"></i>
                            <strong>Info:</strong> Jadwal akan otomatis dieksekusi setiap hari pada waktu yang
                            ditentukan.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Jadwal</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Custom Styles --}}
<style>
    /* Hover effect for table rows */
    .hover-shadow {
        transition: all 0.3s ease;
    }

    .hover-shadow:hover {
        background-color: rgba(0, 0, 0, 0.02);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transform: translateY(-1px);
    }

    /* Smooth transition for badges */
    .badge {
        transition: all 0.2s ease;
    }

    /* Button hover improvements */
    .btn-sm {
        transition: all 0.2s ease;
    }

    .btn-sm:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
    }

    /* Mobile card hover effect */
    @media (max-width: 767px) {
        .card.shadow-sm {
            transition: all 0.3s ease;
        }

        .card.shadow-sm:active {
            transform: scale(0.98);
        }
    }
</style>

{{-- SweetAlert Messages --}}
<script>
@if(session('success'))
Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: '{{ session('success') }}',
    confirmButtonText: 'OK',
    confirmButtonColor: '#5e72e4'
});
@endif

@if(session('error'))
Swal.fire({
    icon: 'error',
    title: 'Gagal!',
    text: '{{ session('error') }}',
    confirmButtonText: 'Coba Lagi',
    confirmButtonColor: '#5e72e4'
});
@endif

@if($errors->any())
Swal.fire({
    icon: 'error',
    title: 'Validasi Gagal',
    html: '{!! implode('<br>', $errors->all()) !!}',
    confirmButtonText: 'OK',
    confirmButtonColor: '#5e72e4'
});
@endif

// Initialize Bootstrap tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection