@extends('layouts.main')
@section('content')
    <div class="min-height-300 bg-dark position-absolute w-100"></div>
    @include('partials.sidebar')
    <main class="main-content position-relative border-radius-lg ">
        @include('partials.navbar')
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-uppercase font-weight-bold">Kekeruhan</p>
                                        <h5 class="font-weight-bolder">
                                            {{ $sensor->kekeruhan }} NTU
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
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-uppercase font-weight-bold">Keasaman</p>
                                        <h5 class="font-weight-bolder">
                                            {{ $sensor->keasaman }}
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
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-uppercase font-weight-bold">Suhu</p>
                                        <h5 class="font-weight-bolder">
                                            {{ $sensor->suhu }}°C
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
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-uppercase font-weight-bold">Pakan Otomatis</p>
                                        <div class="form-check form-switch mt-2">
                                            <input class="form-check-input" type="checkbox" id="pakanToggle"
                                                {{ $feed->status === 'ON' ? 'checked' : '' }}>
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
    {{-- start script post feed & sweetalert --}}
    <script>
        const labels = @json($chartLabels);
        const dataPH = @json($chartPH);
        const dataTemp = @json($chartTemp);
        const dataTurb = @json($chartTurb);
    </script>
    <script>
        document.getElementById('pakanToggle').addEventListener('change', function() {
            const isChecked = this.checked;
            const command = isChecked ? 'ON' : 'OFF';
            const deviceId = this.getAttribute('data-device-id');
            const toggleElement = this;

            fetch('/api/feed/command', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        command: command,
                        device_id: deviceId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 201) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else if (data.status === 409) {
                        // Kembalikan toggle ke posisi sebelumnya karena tidak berubah
                        toggleElement.checked = !isChecked;
                        Swal.fire({
                            icon: 'info',
                            title: 'Tidak ada perubahan',
                            text: data.message,
                            timer: 2500,
                            showConfirmButton: false
                        });
                    }
                })
                .catch(error => {
                    console.error('Gagal menyimpan status:', error);
                    toggleElement.checked = !isChecked;
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat menyimpan status.',
                        timer: 2500,
                        showConfirmButton: false
                    });
                });
        });
    </script>
    {{-- end script post feed & sweetalert --}}
@endsection
