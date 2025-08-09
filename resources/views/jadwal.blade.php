@extends('layouts.main')
@section('content')
    <div class="min-height-300 bg-dark position-absolute w-100"></div>
    @include('partials.sidebar')
    <main class="main-content position-relative border-radius-lg ">
        @include('partials.navbar')
        <div class="container-fluid py-4">
            <div class="row mt-4 px-4">
                <div class="px-3 text-end">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#modalTambahJadwal">Tambah Jadwal</button>
                </div>
                <div class="card">
                    <div class="table-responsive">
                        <table class="table align-items-center ">
                            <tbody>
                                <tr class="py-5">
                                    <th>
                                        <div class="text-center">
                                            <h6 class="text-lg mb-0">Waktu</h6>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="text-center">
                                            <h6 class="text-lg mb-0">Status</h6>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="text-center">
                                            <h6 class="text-lg mb-0">Aksi</h6>
                                        </div>
                                    </th>
                                </tr>
                                @foreach ($jadwalList as $jadwalItem)
                                    <tr>
                                        <td class="w-30">
                                            <div class="text-center">
                                                <p class="text-sm font-weight-bold mb-0">{{ $jadwalItem->waktu_pakan }}
                                                </p>
                                            </div>
                                        </td>
                                        <td class="w-30">
                                            <div class="text-center">
                                                <span
                                                    class="py-1 px-2 rounded {{ $jadwalItem->executions->count() > 0 ? 'text-success bg-success-subtle' : 'text-danger bg-danger-subtle' }}">
                                                    {{ $jadwalItem->executions->count() > 0 ? 'Sudah diberikan' : 'Belum diberikan' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-primary badge fs-6" data-bs-toggle="modal"
                                                data-bs-target="#modalEdit{{ $jadwalItem->id }}">Edit</button>
                                            <button class="btn btn-danger badge fs-6" data-bs-toggle="modal"
                                                data-bs-target="#modalHapus{{ $jadwalItem->id }}">Hapus</button>
                                        </td>
                                    </tr>
                                    <!-- Modal khusus baris ini -->
                                    <div class="modal fade" id="modalEdit{{ $jadwalItem->id }}" tabindex="-1"
                                        aria-hidden="true">
                                        <div class="modal-dialog">
                                            <form action="/jadwal/update/{{ $jadwalItem->id }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Jadwal</h5>
                                                        <button type="submit" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Tutup"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="text" class="form-control" id="waktu"
                                                            name="waktu_pakan" value="{{ $jadwalItem->waktu_pakan }}"
                                                            required>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="modal fade" id="modalHapus{{ $jadwalItem->id }}" tabindex="-1"
                                        aria-hidden="true">
                                        <div class="modal-dialog">
                                            <form action="/jadwal/delete/{{ $jadwalItem->id }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Hapus Jadwal</h5>
                                                        <button type="submit" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Tutup"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Apakah anda yakin ingin menghapus jadwal ini?</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-danger">Hapus</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @include('partials.footer')
        </div>
    </main>
    <div class="modal fade" id="modalTambahJadwal" tabindex="-1" aria-labelledby="modalTambahJadwalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ url('/jadwal/store') }}" method="POST"> <!-- Ganti action sesuai rute Laravel kamu -->
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTambahJadwalLabel">Tambah Jadwal Pakan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="waktu" class="form-label">Jam Pakan</label>
                            <input type="time" class="form-control" id="waktu" name="waktu_pakan" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Tambahkan di dalam <head> atau sebelum </body> -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('success') }}',
                confirmButtonText: 'OK'
            });
        @elseif (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '{{ session('error') }}',
                confirmButtonText: 'Coba Lagi'
            });
        @endif
    </script>
    <script>
        @if ($errors->any())
            let errorMessages = `{!! implode('<br>', $errors->all()) !!}`;
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                html: errorMessages,
                confirmButtonText: 'OK'
            });
        @endif
    </script>
@endsection
