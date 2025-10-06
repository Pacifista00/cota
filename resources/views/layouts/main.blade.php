<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('img/logo.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <title>
        Monitoring COTA
    </title>
    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <!-- Nucleo Icons -->
    <link href="https://demos.creative-tim.com/argon-dashboard-pro/assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="https://demos.creative-tim.com/argon-dashboard-pro/assets/css/nucleo-svg.css" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <!-- CSS Files -->
    <link id="pagestyle" href="{{ asset('argon/css/argon-dashboard.css?v=2.1.0') }}" rel="stylesheet" />
    <!-- Versi Font Awesome CDN yang tidak menggunakan kit -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    {{-- sweetalert --}}
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

</head>

<body class="g-sidenav-show   bg-gray-100">

    @yield('content')
    <!--   Core JS Files   -->
    <script src="{{ asset('argon/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('argon/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('argon/js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('argon/js/plugins/smooth-scrollbar.min.js') }}"></script>
    <script src="{{ asset('argon/js/plugins/chartjs.min.js') }}"></script>
    <script>
        var ctx1 = document.getElementById("chart-line").getContext("2d");

        var gradientStrokePH = ctx1.createLinearGradient(0, 230, 0, 50);
        gradientStrokePH.addColorStop(1, 'rgba(94, 114, 228, 0.2)');
        gradientStrokePH.addColorStop(0.2, 'rgba(94, 114, 228, 0.0)');
        gradientStrokePH.addColorStop(0, 'rgba(94, 114, 228, 0)');

        var gradientStrokeTemp = ctx1.createLinearGradient(0, 230, 0, 50);
        gradientStrokeTemp.addColorStop(1, 'rgba(255, 99, 132, 0.2)');
        gradientStrokeTemp.addColorStop(0.2, 'rgba(255, 99, 132, 0.0)');
        gradientStrokeTemp.addColorStop(0, 'rgba(255, 99, 132, 0)');

        new Chart(ctx1, {
            type: "line",
            data: {
                labels: labels,
                datasets: [{
                        label: "Keasaman",
                        tension: 0.4,
                        borderWidth: 3,
                        pointRadius: 0,
                        borderColor: "#5e72e4",
                        backgroundColor: gradientStrokePH,
                        fill: true,
                        data: dataPH,
                        maxBarThickness: 6
                    },
                    {
                        label: "Suhu",
                        tension: 0.4,
                        borderWidth: 3,
                        pointRadius: 0,
                        borderColor: "#ff6384",
                        backgroundColor: gradientStrokeTemp,
                        fill: true,
                        data: dataTemp,
                        maxBarThickness: 6
                    }
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                scales: {
                    y: {
                        grid: {
                            drawBorder: false,
                            display: true,
                            drawOnChartArea: true,
                            drawTicks: false,
                            borderDash: [5, 5]
                        },
                        ticks: {
                            display: true,
                            padding: 10,
                            color: '#fbfbfb',
                            font: {
                                size: 11,
                                family: "Open Sans",
                                style: 'normal',
                                lineHeight: 2
                            },
                        }
                    },
                    x: {
                        grid: {
                            drawBorder: false,
                            display: false,
                            drawOnChartArea: false,
                            drawTicks: false,
                            borderDash: [5, 5]
                        },
                        ticks: {
                            display: true,
                            color: '#ccc',
                            padding: 20,
                            font: {
                                size: 11,
                                family: "Open Sans",
                                style: 'normal',
                                lineHeight: 2
                            },
                        }
                    },
                },
            },
        });
    </script>

    <script>
        const ctx2 = document.getElementById("chart-line-2").getContext("2d");

        const gradientStroke1 = ctx1.createLinearGradient(0, 230, 0, 50);
        gradientStroke1.addColorStop(1, 'rgba(94, 114, 228, 0.2)');
        gradientStroke1.addColorStop(0.2, 'rgba(94, 114, 228, 0.0)');
        gradientStroke1.addColorStop(0, 'rgba(94, 114, 228, 0)');

        new Chart(ctx2, {
            type: "line",
            data: {
                labels: labels,
                datasets: [{
                    label: "Kekeruhan",
                    tension: 0.4,
                    borderWidth: 0,
                    pointRadius: 3,
                    pointBackgroundColor: "#5e72e4",
                    pointBorderColor: "transparent",
                    borderColor: "#5e72e4",
                    backgroundColor: gradientStroke1,
                    borderWidth: 3,
                    fill: true,
                    data: dataTurb,
                    maxBarThickness: 6
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            font: {
                                family: 'Open Sans',
                                size: 12
                            }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                scales: {
                    y: {
                        grid: {
                            drawBorder: false,
                            display: true,
                            drawOnChartArea: true,
                            drawTicks: false,
                            borderDash: [5, 5]
                        },
                        ticks: {
                            display: true,
                            padding: 10,
                            color: '#fbfbfb',
                            font: {
                                size: 11,
                                family: "Open Sans",
                                style: 'normal',
                                lineHeight: 2
                            },
                        }
                    },
                    x: {
                        grid: {
                            drawBorder: false,
                            display: false,
                            drawOnChartArea: false,
                            drawTicks: false,
                            borderDash: [5, 5]
                        },
                        ticks: {
                            display: true,
                            color: '#ccc',
                            padding: 20,
                            font: {
                                size: 11,
                                family: "Open Sans",
                                style: 'normal',
                                lineHeight: 2
                            },
                        }
                    },
                },
            },
        });
    </script>
    <script>
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                damping: '0.5'
            }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }
    </script>
    <script>
        // Fungsi untuk mengecek status MQTT response untuk scheduled feeds
        function checkScheduledFeedStatus(jadwalId) {
            let attempts = 0;
            const maxAttempts = 20; // Maksimal 20 kali cek (20 detik)
            
            const pollStatus = () => {
                attempts++;
                const waktuSekarang = new Date().toLocaleTimeString();
                
                fetch('/api/feed/status')
                    .then(response => response.json())
                    .then(data => {
                        console.log(`[${waktuSekarang}] Status polling (attempt ${attempts}) untuk jadwal ID ${jadwalId}:`, data);
                        
                        if (data.status === 'success') {
                            console.log(`[${waktuSekarang}] ✅ Jadwal ID ${jadwalId} berhasil dieksekusi!`);
                        } else if (data.status === 'pending' && attempts < maxAttempts) {
                            // Lanjutkan polling
                            setTimeout(pollStatus, 1000);
                        } else if (attempts >= maxAttempts) {
                            console.warn(`[${waktuSekarang}] ⚠️ Timeout untuk jadwal ID ${jadwalId}`);
                        }
                    })
                    .catch(error => {
                        console.error(`[${waktuSekarang}] Error checking feed status untuk jadwal ID ${jadwalId}:`, error);
                    });
            };
            
            // Mulai polling setelah 2 detik
            setTimeout(pollStatus, 2000);
        }

        // Polling untuk mengecek jadwal yang siap dieksekusi
        setInterval(() => {
            const waktuSekarang = new Date().toLocaleTimeString();
            console.log(`[${waktuSekarang}] Cek jadwal siap jalan...`);

            fetch('/api/feed/ready', {
                headers: {
                    "Accept": "application/json"
                }
            })
            .then(res => res.json())
            .then(data => {
                console.log(`[${waktuSekarang}] Response dari /api/feed/ready:`, data);

                if (data.length > 0) {
                    data.forEach(jadwal => {
                        console.log(`[${waktuSekarang}] Menjalankan jadwal ID: ${jadwal.id}, Waktu: ${jadwal.waktu_pakan}`);

                        fetch('/api/feed/give/' + jadwal.id)
                            .then(res => res.json())
                            .then(result => {
                                console.log(`[${waktuSekarang}] Hasil eksekusi jadwal ID ${jadwal.id}:`, result);
                                
                                // Mulai polling status setelah eksekusi
                                if (result.status === 'success') {
                                    checkScheduledFeedStatus(jadwal.id);
                                }
                            })
                            .catch(err => {
                                console.error(`[${waktuSekarang}] ERROR eksekusi jadwal ID ${jadwal.id}:`, err);
                            });
                    });
                } else {
                    console.log(`[${waktuSekarang}] Tidak ada jadwal yang siap jalan.`);
                }
            })
            .catch(err => {
                console.error(`[${waktuSekarang}] ERROR saat cek jadwal:`, err);
            });

        }, 3000);
    </script>

    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="{{ asset('argon/js/argon-dashboard.min.js?v=2.1.0') }}"></script>
</body>

</html>
