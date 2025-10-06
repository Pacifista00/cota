@extends('layouts.main')
@section('content')
<div class="min-height-300 bg-dark position-absolute w-100"></div>
@include('partials.sidebar')
<main class="main-content position-relative border-radius-lg ">
    @include('partials.navbar')
    <div class="container-fluid py-4">
        {{-- Statistics Cards --}}
        <div class="row">
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
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
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
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
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
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
            <div class="col-xl-3 col-sm-6">
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
                        <div class="table-responsive p-0">
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
                                    <tr>
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
                                                    <p class="text-xs text-secondary mb-0">
                                                        {{ Str::limit($schedule->description, 50) }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">
                                                {{ \Carbon\Carbon::parse($schedule->waktu_pakan)->format('H:i') }}</p>
                                            <p class="text-xs text-secondary mb-0">{{ $schedule->frequency_type_label }}
                                            </p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <p class="text-xs font-weight-bold mb-0">
                                                {{ $schedule->start_date ? \Carbon\Carbon::parse($schedule->start_date)->format('d/m/Y') : '-' }}
                                            </p>
                                            <p class="text-xs text-secondary mb-0">s/d</p>
                                            <p class="text-xs font-weight-bold mb-0">
                                                {{ $schedule->end_date ? \Carbon\Carbon::parse($schedule->end_date)->format('d/m/Y') : 'Tidak terbatas' }}
                                            </p>
                                        </td>
                                        <td class="align-middle text-center">
                                            @if (!$schedule->is_active)
                                            <span class="badge badge-sm badge-secondary">Non-aktif</span>
                                            @elseif(!$schedule->is_valid)
                                            <span class="badge badge-sm badge-warning">Expired</span>
                                            @elseif($schedule->was_executed_today)
                                            <span class="badge badge-sm badge-success">Tereksekusi Hari Ini</span>
                                            @else
                                            <span class="badge badge-sm badge-primary">Aktif</span>
                                            @endif
                                            @if ($schedule->remaining_days !== null)
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
                                                {{ \Carbon\Carbon::parse($schedule->next_execution)->format('d/m/Y H:i') }}
                                            </p>
                                            @else
                                            <p class="text-xs text-secondary mb-0">-</p>
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            <div class="dropdown">
                                                <button class="btn btn-link text-secondary mb-0" type="button"
                                                    id="dropdownMenu{{ $schedule->id }}" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
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

                                    {{-- Modal Edit --}}
                                    <div class="modal fade" id="modalEdit{{ $schedule->id }}" tabindex="-1"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <form action="{{ url('/jadwal-terjadwal/update/' . $schedule->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Jadwal Pakan</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Nama Jadwal</label>
                                                                <input type="text" class="form-control" name="name"
                                                                    value="{{ $schedule->name }}"
                                                                    placeholder="Contoh: Pakan Pagi">
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Waktu Pakan <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="time" class="form-control"
                                                                    name="waktu_pakan"
                                                                    value="{{ \Carbon\Carbon::parse($schedule->waktu_pakan)->format('H:i') }}"
                                                                    required>
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
                                                                <input type="date" class="form-control"
                                                                    name="start_date"
                                                                    value="{{ $schedule->start_date?->format('Y-m-d') }}">
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Tanggal Selesai</label>
                                                                <input type="date" class="form-control" name="end_date"
                                                                    value="{{ $schedule->end_date?->format('Y-m-d') }}"
                                                                    placeholder="Kosongkan untuk tidak terbatas">
                                                                <small class="text-muted">Kosongkan untuk tidak
                                                                    terbatas</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary">Simpan
                                                            Perubahan</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    {{-- Modal Hapus --}}
                                    <div class="modal fade" id="modalHapus{{ $schedule->id }}" tabindex="-1"
                                        aria-hidden="true">
                                        <div class="modal-dialog">
                                            <form action="{{ url('/jadwal-terjadwal/delete/' . $schedule->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Hapus Jadwal</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Apakah Anda yakin ingin menghapus jadwal
                                                            <strong>{{ $schedule->name ?? 'ini' }}</strong>?
                                                        </p>
                                                        <p class="text-sm text-danger">Tindakan ini tidak dapat
                                                            dibatalkan!</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-danger">Ya,
                                                            Hapus</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="text-center">
                                                <i class="ni ni-calendar-grid-58 text-muted"
                                                    style="font-size: 3rem;"></i>
                                                <p class="text-sm text-secondary mt-2">Belum ada jadwal pakan
                                                    terjadwal.</p>
                                                <button type="button" class="btn btn-primary btn-sm mt-2"
                                                    data-bs-toggle="modal" data-bs-target="#modalTambahJadwal">
                                                    <i class="ni ni-fat-add"></i> Tambah Jadwal Pertama
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('partials.footer')
    </div>
</main>

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

{{-- SweetAlert Messages --}}
<script>
@if(session('success'))
Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: '{{ session('
    success ') }}',
    confirmButtonText: 'OK',
    confirmButtonColor: '#5e72e4'
});
@endif

@if(session('error'))
Swal.fire({
    icon: 'error',
    title: 'Gagal!',
    text: '{{ session('
    error ') }}',
    confirmButtonText: 'Coba Lagi',
    confirmButtonColor: '#5e72e4'
});
@endif

@if($errors -> any())
Swal.fire({
    icon: 'error',
    title: 'Validasi Gagal',
    html: '{!! implode(' < br > ', $errors->all()) !!}',
    confirmButtonText: 'OK',
    confirmButtonColor: '#5e72e4'
});
@endif
</script>
@endsection