@extends('layouts.main')
@section('content')
    <style>
        .gradient-card {
            background: linear-gradient(135deg, #007bff, #00c6ff);
            color: white;
            box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.3);
            border: none;
            border-radius: 1rem;
        }

        .card-body-custom {
            padding: 1.5rem;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .card-text {
            font-size: 1rem;
        }
    </style>
    <section>
        <div class="container py-4">
            <h1>Hi, Dzulfikar!</h1>
            <h5>Semoga harimu menyenangkan.</h5>
        </div>
    </section>
    <section class="py-4">
        <div class="card gradient-card d-flex flex-row overflow-hidden">
            <div class="col-md-4 p-0">
                <img src="{{ asset('img/3d-illus.png') }}" alt="Gambar" class="card-img-left h-100 w-100">
            </div>
            <div class="col-md-8 p-4 d-flex flex-column justify-content-center">
                <h2 class="card-title">Selamat datang di Monitoring COTA</h2>
                <p class="card-text">Lorem ipsum dolor sit amet consectetur adipisicing elit. Mollitia, qui quod nisi
                    quibusdam praesentium debitis vero itaque id animi ratione.</p>
            </div>
        </div>
    </section>
    <section class="py-4">
        <div class="row g-4">

            <!-- Card: Kekeruhan -->
            <div class="col-md-6">
                <div class="card shadow-md">
                    <div class="card-body card-body-custom d-flex justify-content-between align-items-center">
                        <div>
                            <div class="card-title">Kekeruhan Air</div>
                            <div class="card-text">Nilai: <strong>120 NTU</strong></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card: pH -->
            <div class="col-md-6">
                <div class="card shadow-md">
                    <div class="card-body card-body-custom d-flex justify-content-between align-items-center">
                        <div>
                            <div class="card-title">pH Air</div>
                            <div class="card-text">Nilai: <strong>7.2</strong></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card: Suhu -->
            <div class="col-md-6">
                <div class="card shadow-md">
                    <div class="card-body card-body-custom d-flex justify-content-between align-items-center">
                        <div>
                            <div class="card-title">Suhu Air</div>
                            <div class="card-text">Nilai: <strong>27°C</strong></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card: Pakan Ikan -->
            <div class="col-md-6">
                <div class="card shadow-md">
                    <div class="card-body card-body-custom d-flex justify-content-between align-items-center">
                        <div>
                            <div class="card-title">Pakan Ikan</div>
                            <div class="card-text mb-3">Kontrol Pemberian Pakan</div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="pakanSwitch">
                                <label class="form-check-label" for="pakanSwitch">Pakan <span
                                        id="switchStatus">OFF</span></label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection
