@extends('layouts.main')
@section('content')
    <div class="min-height-300 bg-dark position-absolute w-100"></div>
    @include('partials.sidebar')
    <main class="main-content position-relative border-radius-lg ">
        @include('partials.navbar')
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card card-sensor">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 font-weight-bold">Kekeruhan Air</p>
                                        <h5 class="font-weight-bolder"><span id="kekeruhan-value">0</span> NTU
                                        </h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div
                                        class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
                                        <i class="fas fa-water text-lg opacity-10" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card card-sensor">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 font-weight-bold">pH Air</p>
                                        <h5 class="font-weight-bolder"><span id="keasaman-value">0</span>
                                        </h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div
                                        class="icon icon-shape bg-gradient-danger shadow-danger text-center rounded-circle">
                                        <i class="fas fa-flask text-lg opacity-10" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card card-sensor">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 font-weight-bold">Suhu Air</p>
                                        <h5 class="font-weight-bolder"><span id="suhu-value">0</span> °C
                                        </h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div
                                        class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">
                                        <i class="fas fa-temperature-low text-lg opacity-10" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="card card-sensor">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-uppercase font-weight-bold">Pakan Otomatis</p>
                                        <div class="form-check form-switch mt-2 ps-0">
                                            <form action="{{ url('/beri-pakan') }}" method="POST"
                                                class="my-0 py-0 me-auto">
                                                @csrf
                                                <button type="submit" class="py-1 my-0 rounded-pill btn btn-primary">Beri
                                                    Pakan</button>
                                            </form>
                                            {{-- <input class="form-check-input" type="checkbox" id="pakanToggle"
                                                {{ $feed->status === 'ON' ? 'checked' : '' }}> --}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div
                                        class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle">
                                        <i class="fas fa-fish text-lg opacity-10" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="row mt-4">
                <div class="col-lg-7 mb-lg-0 mb-4">
                    <div class="card z-index-2 h-100">
                        <div class="card-header pb-0 pt-3 bg-transparent">
                            <h6 class="text-capitalize">Grafik pH & Suhu</h6>
                            <p class="text-sm mb-0">
                                <span class="font-weight-bold">Data dalam 7 hari</span>
                            </p>
                        </div>
                        <div class="card-body p-3">
                            <div class="chart">
                                <canvas id="chart-line" class="chart-canvas" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 mb-lg-0 mb-4">
                    <div class="card z-index-2 h-100">
                        <div class="card-header pb-0 pt-3 bg-transparent">
                            <h6 class="text-capitalize">Grafik Kekeruhan</h6>
                            <p class="text-sm mb-0">
                                <span class="font-weight-bold">Data dalam 7 hari</span>
                            </p>
                        </div>
                        <div class="card-body p-3">
                            <div class="chart">
                                <canvas id="chart-line-2" class="chart-canvas" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @include('partials.footer')
        </div>
    </main>

    <script>
        setInterval(() => {
            fetch('/api/sensor-data/latest')
                .then(response => response.json())
                .then(response => {
                    const sensor = response.data; // Ini adalah { keasaman, kekeruhan, suhu, waktu }
                    console.log(sensor);

                    document.getElementById('kekeruhan-value').textContent = sensor.kekeruhan;
                    document.getElementById('keasaman-value').textContent = sensor.keasaman;
                    document.getElementById('suhu-value').textContent = sensor.suhu;
                })
                .catch(error => {
                    console.error('Gagal mengambil data sensor:', error);
                });
        }, 3000); // update setiap 3 detik
    </script>

    // {{-- start script post feed & sweetalert --}}
    <script>
        const labels = @json($chartLabels);
        const dataPH = @json($chartPH);
        const dataTemp = @json($chartTemp);
        const dataTurb = @json($chartTurb);
    </script>
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

    // {{-- end script post feed & sweetalert --}}
@endsection
