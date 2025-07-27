@extends('layouts.main')
@section('content')
    <div class="container-xxl position-relative bg-white d-flex p-0">
        @include('partials.sidebar')
        <!-- Content Start -->
        <div class="content">
            @include('partials.navbar')
            <!-- Sale & Revenue Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="row g-4">
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-light rounded d-flex align-items-center justify-content-between p-4"
                            style="height:120px;">
                            <i class="fa-solid fa-water fa-3x text-primary"></i>
                            <div class="ms-3">
                                <p class="mb-2">Kekeruhan</p>
                                <h6 class="mb-0">12</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-light rounded d-flex align-items-center justify-content-between p-4"
                            style="height:120px;">
                            <i class="fa-solid fa-vial fa-3x text-primary"></i>
                            <div class="ms-3">
                                <p class="mb-2">Keasaman</p>
                                <h6 class="mb-0">12</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-light rounded d-flex align-items-center justify-content-between p-4"
                            style="height:120px;">
                            <i class="fa-solid fa-temperature-three-quarters fa-3x text-primary"></i>
                            <div class="ms-3">
                                <p class="mb-2">Suhu</p>
                                <h6 class="mb-0">12</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-light rounded d-flex align-items-center justify-content-between p-4"
                            style="height:120px;">
                            <i class="fa-solid fa-fish-fins fa-3x text-primary"></i>
                            <div class="ms-3">
                                <p class="mb-2">Pakan Otomatis</p>
                                <div class="form-check form-switch">
                                    <input class="form-check-input form-switch" type="checkbox" role="switch"
                                        id="flexSwitchCheckDefault">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Sale & Revenue End -->

                <!-- Sales Chart Start -->
                <div class="container-fluid py-4 px-4">
                    <div class="row g-4">
                        <div class="col-sm-12 col-xl-12">
                            <div class="bg-light text-center rounded p-4">
                                <div class="d-flex align-items-center justify-content-between mb-4">
                                    <h6 class="mb-0">Grafik Monitoring</h6>
                                    <a href="">Lihat Riwayat</a>
                                </div>
                                <canvas id="worldwide-sales"></canvas>
                            </div>
                        </div>
                        {{-- <div class="col-sm-12 col-xl-6">
                            <div class="bg-light text-center rounded p-4">
                                <div class="d-flex align-items-center justify-content-between mb-4">
                                    <h6 class="mb-0">Grafik Pakan Otomatis</h6>
                                    <a href="">Lihat Riwayat</a>
                                </div>
                                <canvas id="salse-revenue"></canvas>
                            </div>
                        </div> --}}
                    </div>
                </div>
                <!-- Sales Chart End -->
            </div>
            <!-- Content End -->
        </div>
    @endsection
