@extends('layouts.main')
@section('content')
    <section>
        <div class="container py-4">
            <h1>Hi, Dzulfikar!</h1>
            <h5>Semoga harimu menyenangkan.</h5>
        </div>
    </section>
    <section class="py-4">
        <div class="card gradient-card">
            <div class="row">
                <div class="col-12 col-lg-4 p-0">
                    <img src="{{ asset('img/3d-illus.png') }}" alt="Gambar" class="card-img-left w-100"
                        style="object-fit: cover;" />
                </div>
                <div class="col-12 col-lg-8 p-4 d-flex flex-column justify-content-center">
                    <h2 class="card-title">Selamat datang di Monitoring COTA</h2>
                    <p class="card-text">
                        Lorem ipsum dolor sit amet consectetur adipisicing elit. Mollitia, qui quod nisi
                        quibusdam praesentium debitis vero itaque id animi ratione.
                    </p>
                </div>
            </div>
        </div>
    </section>
    <section class="py-4">
        <div class="row g-3">
            <div class="col-6 col-lg-3">
                <div class="card shadow-md border-0 rounded-4 bg-light">
                    <div class="card-body bg-label-success rounded-4 p-4 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="me-3 text-primary">
                                <i class="fa-solid fa-water fa-3x"></i>
                            </div>
                            <div>
                                <h2 class="mb-0 fw-bold">
                                    12 <span class="fs-5 text-secondary">NTU</span>
                                </h2>
                                <div class="card-title text-muted">Kekeruhan</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card shadow-md border-0 rounded-4 bg-light">
                    <div class="card-body bg-label-success rounded-4 p-4 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="me-3 text-primary">
                                <i class="fa-solid fa-vial fa-3x"></i>
                            </div>
                            <div>
                                <h2 class="mb-0 fw-bold">
                                    12
                                </h2>
                                <div class="card-title text-muted">Keasaman</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card shadow-md border-0 rounded-4 bg-light">
                    <div class="card-body bg-label-success rounded-4 p-4 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="me-3 text-primary">
                                <i class="fa-solid fa-temperature-three-quarters fa-3x"></i>
                            </div>
                            <div>
                                <h2 class="mb-0 fw-bold">
                                    12 <span class="fs-5 text-secondary">°C</span>
                                </h2>
                                <div class="card-title text-muted">Suhu</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card shadow-md border-0 rounded-4 bg-light">
                    <div class="card-body bg-label-success rounded-4 p-4 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="me-3 text-primary">
                                <i class="fa-solid fa-fish-fins fa-3x"></i>
                            </div>
                            <div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input form-switch" type="checkbox" role="switch"
                                        id="flexSwitchCheckDefault">

                                </div>
                                <div class="card-title text-muted">Pakan Otomatis</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
