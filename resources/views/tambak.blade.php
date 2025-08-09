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
                        data-bs-target="#modalTambahTambak">Tambah Tambak</button>
                </div>
                <div class="row">
                    @foreach ($ponds as $pondItem)
                        <div class="col-md-4 mb-4">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <h5 class="card-title mb-1">{{ $pondItem->nama }}</h5>
                                    <p class="text-sm text-muted mb-2">{{ $pondItem->lokasi }}</p>
                                    <div class="d-flex gap-2 w-100">
                                        <button class="btn btn-warning w-50" data-bs-toggle="modal"
                                            data-bs-target="#modalEditTambak{{ $pondItem->id }}"><i class="fas fa-edit"></i>
                                            Edit</button>
                                        <button class="btn btn-danger w-50" data-bs-toggle="modal"
                                            data-bs-target="#modalHapusTambak{{ $pondItem->id }}"><i
                                                class="fas fa-trash"></i>
                                            Hapus</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal fade" id="modalEditTambak{{ $pondItem->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <form action="/pond/update/{{ $pondItem->id }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Tambak</h5>
                                            <button type="submit" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Tutup"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Token : <span class="text-danger">{{ $pondItem->token_tambak }}</span></p>
                                            <div class="mb-3">
                                                <label for="nama" class="form-label">Nama</label>
                                                <input type="text" class="form-control" id="nama" name="nama"
                                                    required value="{{ $pondItem->nama }}">
                                            </div>
                                            <div class="mb-3">
                                                <label for="lokasi" class="form-label">Lokasi</label>
                                                <input type="text" class="form-control" id="lokasi" name="lokasi"
                                                    required value="{{ $pondItem->lokasi }}">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="modal fade" id="modalHapusTambak{{ $pondItem->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <form action="/pond/delete/{{ $pondItem->id }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Hapus pond</h5>
                                            <button type="submit" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Tutup"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Apakah anda yakin ingin menghapus tambak ini?</p>
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

                </div>
            </div>
            @include('partials.footer')
        </div>
    </main>
    <div class="modal fade" id="modalTambahTambak" tabindex="-1" aria-labelledby="modalTambahJadwalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ url('/pond/store') }}" method="POST"> <!-- Ganti action sesuai rute Laravel kamu -->
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTambahJadwalLabel">Tambah Jadwal Pakan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="nama" name="nama" required>
                        </div>
                        <div class="mb-3">
                            <label for="lokasi" class="form-label">Lokasi</label>
                            <input type="text" class="form-control" id="lokasi" name="lokasi" required>
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
        @if (session('token_tambak'))
            Swal.fire({
                icon: 'success',
                title: 'Tambak Berhasil Disimpan!',
                html: `<p><strong>Token Tambak:</strong><br><code>{{ session('token_tambak') }}</code></p>`,
                confirmButtonText: 'Tutup'
            });
        @elseif (session('success'))
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
