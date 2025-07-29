@extends('layouts.main')
@section('content')
    <div class="min-height-300 bg-dark position-absolute w-100"></div>
    @include('partials.sidebar')
    <main class="main-content position-relative border-radius-lg ">
        @include('partials.navbar')
        <div class="container-fluid py-4">
            <div class="row mt-4">
                <div class="col-lg-12 mb-lg-0 mb-4">
                    <div class="card ">
                        <div class="card-header pb-0 p-3">
                            <div class="d-flex justify-content-between">
                                <h6 class="mb-2">Riwayat Monitoring Sensor</h6>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-items-center ">
                                <tbody>
                                    <tr>
                                        <th class="w-30">
                                            <div class="text-center">
                                                <h6 class="text-sm mb-0">Waktu</h6>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="text-center">
                                                <h6 class="text-sm mb-0">Kekeruhan</h6>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="text-center">
                                                <h6 class="text-sm mb-0">Keasaman</h6>
                                            </div>
                                        </th>
                                        <th class="align-middle text-sm">
                                            <div class="text-center">
                                                <h6 class="text-sm mb-0">Suhu</h6>
                                            </div>
                                        </th>
                                    </tr>
                                    <tr>
                                        <td class="w-30">
                                            <div class="text-center">
                                                <p class="text-xs font-weight-bold mb-0">22-12-2012 05:12:12</p>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                <p class="text-xs font-weight-bold mb-0">23 NTU</p>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                <p class="text-xs font-weight-bold mb-0">5</p>
                                            </div>
                                        </td>
                                        <td class="align-middle text-sm">
                                            <div class="col text-center">
                                                <p class="text-xs font-weight-bold mb-0">15°C</p>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="w-30">
                                            <div class="text-center">
                                                <p class="text-xs font-weight-bold mb-0">22-12-2012 05:12:12</p>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                <p class="text-xs font-weight-bold mb-0">23 NTU</p>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                <p class="text-xs font-weight-bold mb-0">5</p>
                                            </div>
                                        </td>
                                        <td class="align-middle text-sm">
                                            <div class="col text-center">
                                                <p class="text-xs font-weight-bold mb-0">15°C</p>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="w-30">
                                            <div class="text-center">
                                                <p class="text-xs font-weight-bold mb-0">22-12-2012 05:12:12</p>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                <p class="text-xs font-weight-bold mb-0">23 NTU</p>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                <p class="text-xs font-weight-bold mb-0">5</p>
                                            </div>
                                        </td>
                                        <td class="align-middle text-sm">
                                            <div class="col text-center">
                                                <p class="text-xs font-weight-bold mb-0">15°C</p>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="w-30">
                                            <div class="text-center">
                                                <p class="text-xs font-weight-bold mb-0">22-12-2012 05:12:12</p>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                <p class="text-xs font-weight-bold mb-0">23 NTU</p>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                <p class="text-xs font-weight-bold mb-0">5</p>
                                            </div>
                                        </td>
                                        <td class="align-middle text-sm">
                                            <div class="col text-center">
                                                <p class="text-xs font-weight-bold mb-0">15°C</p>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="w-30">
                                            <div class="text-center">
                                                <p class="text-xs font-weight-bold mb-0">22-12-2012 05:12:12</p>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                <p class="text-xs font-weight-bold mb-0">23 NTU</p>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                <p class="text-xs font-weight-bold mb-0">5</p>
                                            </div>
                                        </td>
                                        <td class="align-middle text-sm">
                                            <div class="col text-center">
                                                <p class="text-xs font-weight-bold mb-0">15°C</p>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
            <div class="row mt-4">
                <div class="col-lg-6 mb-lg-0 mb-4">
                    <div class="card ">
                        <div class="card-header pb-0 p-3">
                            <div class="d-flex justify-content-between">
                                <h6 class="mb-2">Riwayat Status Pakan Otomatis</h6>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-items-center ">
                                <tbody>
                                    <tr>
                                        <th class="w-30">
                                            <div class="text-center">
                                                <h6 class="text-sm mb-0">Waktu</h6>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="text-center">
                                                <h6 class="text-sm mb-0">Status Pakan Otomatis</h6>
                                            </div>
                                        </th>
                                    </tr>
                                    <tr>
                                        <td class="w-30">
                                            <div class="text-center">
                                                <p class="text-xs font-weight-bold mb-0">22-12-2012 05:12:12</p>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                <p class="text-xs font-weight-bold mb-0 text-danger">OFF</p>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="w-30">
                                            <div class="text-center">
                                                <p class="text-xs font-weight-bold mb-0">22-12-2012 05:12:12</p>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                <p class="text-xs font-weight-bold mb-0 text-success">ON</p>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="w-30">
                                            <div class="text-center">
                                                <p class="text-xs font-weight-bold mb-0">22-12-2012 05:12:12</p>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                <p class="text-xs font-weight-bold mb-0 text-danger">OFF</p>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="w-30">
                                            <div class="text-center">
                                                <p class="text-xs font-weight-bold mb-0">22-12-2012 05:12:12</p>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                <p class="text-xs font-weight-bold mb-0 text-success">ON</p>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="w-30">
                                            <div class="text-center">
                                                <p class="text-xs font-weight-bold mb-0">22-12-2012 05:12:12</p>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                <p class="text-xs font-weight-bold mb-0 text-danger">OFF</p>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-lg-0 mb-4">
                    <div class="card ">
                        <div class="card-header pb-0 p-3">
                            <div class="d-flex justify-content-between">
                                <h6 class="mb-2">Riwayat Pemberian Pakan</h6>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-items-center ">
                                <tbody>
                                    <tr>
                                        <th class="w-30">
                                            <div class="text-center">
                                                <h6 class="text-sm mb-0">Waktu</h6>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="text-center">
                                                <h6 class="text-sm mb-0">Status</h6>
                                            </div>
                                        </th>
                                    </tr>
                                    <tr>
                                        <td class="w-30">
                                            <div class="text-center">
                                                <p class="text-xs font-weight-bold mb-0">22-12-2012 05:12:12</p>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                <p class="text-xs font-weight-bold mb-0">Berhasil Terbuka</p>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="w-30">
                                            <div class="text-center">
                                                <p class="text-xs font-weight-bold mb-0">22-12-2012 05:12:12</p>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                <p class="text-xs font-weight-bold mb-0">Berhasil Terbuka</p>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="w-30">
                                            <div class="text-center">
                                                <p class="text-xs font-weight-bold mb-0">22-12-2012 05:12:12</p>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                <p class="text-xs font-weight-bold mb-0">Berhasil Terbuka</p>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="w-30">
                                            <div class="text-center">
                                                <p class="text-xs font-weight-bold mb-0">22-12-2012 05:12:12</p>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                <p class="text-xs font-weight-bold mb-0">Berhasil Terbuka</p>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @include('partials.footer')
        </div>
    </main>
@endsection
