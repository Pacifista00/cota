@extends('layouts.main')
@section('content')
    <div class="bg-white">

        <div class="container login-container d-flex align-items-center justify-content-center flex-column min-vh-100">

            <div class="text-center">
                <img src="{{ asset('img/logo.png') }}" alt="" style="width: 86px">
                <h4 class="card-title my-2">Register</h4>
            </div>
            <form action="{{ url('register') }}" method="POST" class="mt-3" style="width: 18rem;">
                @csrf
                <div class="mb-3">
                    <input type="text" class="form-control" aria-describedby="emailHelp"
                        placeholder="Masukkan nama lengkap" name="fullname">
                </div>
                <div class="mb-3">
                    <input type="email" class="form-control" aria-describedby="emailHelp" placeholder="Masukkan email"
                        name="email">
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" aria-describedby="emailHelp"
                        placeholder="Masukkan No telepon" name="no_telepon">
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control" placeholder="Masukkan password" name="password">
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control" id="exampleInputPassword1"
                        placeholder="Masukkan konfirmasi password" name="password_confirm">
                </div>
                <button type="submit" class="btn btn-primary rounded-pill w-100">Register</button>
                <p class="text-center">Sudah membuat akun? Silahkan <a href="{{ url('/login') }}">login</a></p>
            </form>
        </div>
    </div>
    @if ($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                html: `{!! implode('<br>', $errors->all()) !!}`,
                confirmButtonText: 'Perbaiki'
            });
        </script>
    @endif
@endsection
