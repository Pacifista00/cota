@extends('layouts.main')
@section('content')
    <div class="container-xxl position-relative bg-white d-flex p-0">
        @include('partials.sidebar')
        <!-- Content Start -->
        <div class="content">
            @include('partials.navbar')
            <!-- Table Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="row g-4">
                    <div class="col-sm-12 col-xl-7">
                        <div class="bg-light rounded h-100 p-4">
                            <h6 class="mb-4">Monitoring Sensor</h6>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">Waktu</th>
                                        <th scope="col">Kekeruhan</th>
                                        <th scope="col">Keasaman</th>
                                        <th scope="col">Suhu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>20-12-2025</td>
                                        <td>12</td>
                                        <td>12</td>
                                        <td>12</td>
                                    </tr>
                                    <tr>
                                        <td>20-12-2025</td>
                                        <td>12</td>
                                        <td>12</td>
                                        <td>12</td>
                                    </tr>
                                    <tr>
                                        <td>20-12-2025</td>
                                        <td>12</td>
                                        <td>12</td>
                                        <td>12</td>
                                    </tr>
                                    <tr>
                                        <td>20-12-2025</td>
                                        <td>12</td>
                                        <td>12</td>
                                        <td>12</td>
                                    </tr>
                                    <tr>
                                        <td>20-12-2025</td>
                                        <td>12</td>
                                        <td>12</td>
                                        <td>12</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-sm-12 col-xl-5">
                        <div class="bg-light rounded h-100 p-4">
                            <h6 class="mb-4">Pakan Otomatis</h6>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">Waktu</th>
                                        <th scope="col">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>12-12-2025</td>
                                        <td>OFF</td>
                                    </tr>
                                    <tr>
                                        <td>12-12-2025</td>
                                        <td>OFF</td>
                                    </tr>
                                    <tr>
                                        <td>12-12-2025</td>
                                        <td>OFF</td>
                                    </tr>
                                    <tr>
                                        <td>12-12-2025</td>
                                        <td>OFF</td>
                                    </tr>
                                    <tr>
                                        <td>12-12-2025</td>
                                        <td>OFF</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Table End -->
        </div>
        <!-- Content End -->

    </div>
@endsection
