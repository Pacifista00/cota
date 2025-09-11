@extends('layouts.main')
@section('content')
    <div class="min-height-300 bg-dark position-absolute w-100"></div>
    @include('partials.sidebar')
    <main class="main-content position-relative border-radius-lg ">
        @include('partials.navbar')
        <div class="container-fluid py-4">
            <div class="row mt-4 px-4">
                <div class="card">
                    <div class="table-responsive">
                        <video width="100%" height="600" controls>
                            <source src="{{ asset('video/preview.mp4') }}" type="video/mp4">
                            Your browser does not support the video tag.
                    </div>
                </div>
            </div>
            @include('partials.footer')
        </div>
    </main>
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
