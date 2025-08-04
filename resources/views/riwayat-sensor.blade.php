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
                                                <h6 class="text-lg mb-0">Waktu</h6>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="text-center">
                                                <h6 class="text-lg mb-0">Kekeruhan</h6>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="text-center">
                                                <h6 class="text-lg mb-0">Keasaman</h6>
                                            </div>
                                        </th>
                                        <th class="align-middle">
                                            <div class="text-center">
                                                <h6 class="text-lg mb-0">Suhu</h6>
                                            </div>
                                        </th>
                                    </tr>
                                    @foreach ($sensorHistories as $sensorItem)
                                        <tr>
                                            <td class="w-30">
                                                <div class="text-center">
                                                    <p class="text-sm font-weight-bold mb-0">{{ $sensorItem->created_at }}
                                                    </p>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-center">
                                                    <p class="text-sm font-weight-bold mb-0">{{ $sensorItem->kekeruhan }}
                                                        NTU</p>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-center">
                                                    <p class="text-sm font-weight-bold mb-0">{{ $sensorItem->keasaman }}</p>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="col text-center">
                                                    <p class="text-sm font-weight-bold mb-0">{{ $sensorItem->suhu }}°C</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="container">
                                <div class="d-flex justify-content-center mt-3">
                                    {{ $sensorHistories->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            @include('partials.footer')
        </div>
    </main>
@endsection
