@extends('layouts.main')
@section('content')
    <div class="min-height-300 bg-dark position-absolute w-100"></div>
    @include('partials.sidebar')
    <main class="main-content position-relative border-radius-lg ">
        @include('partials.navbar')
        <div class="container-fluid py-4">
            <div class="row mt-4">
                <div class="px-3 text-end">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#modalTambahJadwal">Tambah Jadwal</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-light table-striped align-middle mb-0"
                        style="border: 1px solid #dee2e6 !important;">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">Waktu</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center">06:00 WIB</td>
                                <td class="text-center">
                                    <span class="badge bg-success">Edit</span>
                                    <span class="badge bg-success">Hapus</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center" style="border: 1px solid #dee2e6;">12:00 WIB</td>
                                <td class="text-center">
                                    <span class="badge bg-success">Edit</span>
                                    <span class="badge bg-success">Hapus</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>


            </div>
            @include('partials.footer')
        </div>
    </main>
    <div class="modal fade" id="modalTambahJadwal" tabindex="-1" aria-labelledby="modalTambahJadwalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form action="/jadwal/store" method="POST"> <!-- Ganti action sesuai rute Laravel kamu -->
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTambahJadwalLabel">Tambah Jadwal Pakan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="waktu" class="form-label">Jam Pakan</label>
                            <input type="time" class="form-control" id="waktu" name="waktu" required>
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
@endsection
