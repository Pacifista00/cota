@extends('layouts.main')
@section('content')
    <div class="bg-white">

        <div class="container login-container d-flex align-items-center justify-content-center flex-column min-vh-100">

            <div class="text-center">
                <img src="{{ asset('img/logo.png') }}" alt="" style="width: 86px">
                <h4 class="card-title my-2">Login</h4>
            </div>
            <form action="{{ url('/login') }}" method="POST" class="mt-3" style="width: 18rem;">
                @csrf
                <div class="mb-3">
                    <input type="email" class="form-control" placeholder="Masukkan email" name="email">
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control" placeholder="Masukkan password" name="password">
                </div>
                <button type="submit" class="btn btn-primary rounded-pill w-100">Login</button>
                <p class="text-center">Belum membuat akun? Silahkan <a href="{{ url('/register') }}">register</a></p>
            </form>
        </div>
    </div>

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
@endsection
