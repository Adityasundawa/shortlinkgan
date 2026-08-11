@extends('admin.components.main')

{{-- Perbaikan: Menggunakan $campaign --}}
@section('page.title', $campaign->name . ' | Analytic')

@section('custom-css')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
@endsection

@section('main-content')
<div class="pc-container">
    <div class="pc-content">
        <div class="page-header">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h2 class="mb-0">Analitik Campaign: {{ $campaign->name }}</h2>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-start">
                            <div class="flex-grow-1 mx-3">
                                <h4 class="mb-1">{{ $campaign->name }}</h4>
                                <p class="mb-0 text-muted">Total {{ $campaign->shortLinks->count() }} Short Links di Campaign ini.</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">

                        {{-- SUMMARY ANALYTIC --}}
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <h4 class="mb-0 text-primary" id="summary-pageviews">{{ $summary['totalPageViews'] }}</h4>
                                <p class="mb-0 text-muted">Total Page Views</p>
                            </div>
                            <div class="col-md-3">
                                <h4 class="mb-0 text-success" id="summary-visitors">{{ $summary['totalVisitors'] }}</h4>
                                <p class="mb-0 text-muted">Total Visitors (Unique)</p>
                            </div>
                            <div class="col-md-3">
                                <h4 class="mb-0 text-warning" id="summary-avgpage">{{ $summary['avgPagePerVisit'] }}</h4>
                                <p class="mb-0 text-muted">Tampilan halaman per kunjungan</p>
                            </div>
                            <div class="col-md-3">
                                <h4 class="mb-0 text-info" id="summary-lasthit">{{ $summary['lastHitTime'] }}</h4>
                                <p class="mb-0 text-muted">Last hit time</p>
                            </div>
                        </div>
                        <hr>

                        {{-- FILTER DATE RANGE --}}
                        <div class="row mb-4 align-items-center">
                            <div class="col-md-4">
                                <label for="dateRangePicker">Filter Rentang Tanggal:</label>
                                <input type="text" id="dateRangePicker" class="form-control" placeholder="Pilih Rentang Tanggal">
                            </div>
                            <div class="col-md-2 mt-4">
                                <button class="btn btn-primary" id="filterDataBtn">Terapkan Filter</button>
                            </div>
                        </div>


                        {{-- CHART TRAFIK HARIAN (LINE CHART) --}}
                        <div class="row">
                            <div class="col-12">
                                <div class="card shadow-none border">
                                    <div class="card-header">
                                        <h5 class="mb-0">Trafik Harian (Page Views & Visitors)</h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="daily-traffic-chart" style="height: 350px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- CHART NEGARA & PERANGKAT --}}
                        <div class="row">
                            <div class="col-xl-6 col-md-12">
                                <div class="card shadow-none border">
                                    <div class="card-header">
                                        <h5 class="mb-0">Trafik Berdasarkan Negara (Top 10)</h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="traffic-by-country-chart" style="height: 350px;"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-6 col-md-12">
                                <div class="card shadow-none border">
                                    <div class="card-header">
                                        <h5 class="mb-0">Trafik Berdasarkan Tipe Perangkat</h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="traffic-by-device-chart" style="height: 350px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- TABEL TRAFIK KOTA --}}
                        <div class="row">
                            <div class="col-12">
                                <div class="card shadow-none border">
                                    <div class="card-header">
                                        <h5 class="mb-0">Trafik Berdasarkan Kota (Top 15 Visitors)</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0" id="city-traffic-table">
                                                <thead>
                                                    <tr>
                                                        <th>Kota</th>
                                                        <th>Negara</th>
                                                        <th>Visitors</th>
                                                        <th>Page Views</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr><td colspan="4" class="text-center">Memuat data...</td></tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
 {{-- 🚨 BAGIAN BARU: TOP 10 SHORTLINK ANALYTIC 🚨 --}}
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card shadow-none border">
                                    <div class="card-header">
                                        <h5 class="mb-0">Top 10 Short Links Traffic (Paling Efektif)</h5>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-group list-group-flush border-top-0" id="top-shortlinks-list">
                                            {{-- Data akan diisi oleh JavaScript --}}
                                            <li class="list-group-item text-center text-muted">Memuat data...</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card shadow-none border">
                                    <div class="card-header">
                                        <h5 class="mb-0">Performa UTM</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0" id="utm-performance-table">
                                                <thead>
                                                    <tr>
                                                        <th>Parameter</th>
                                                        <th>Value</th>
                                                        <th>Visitors</th>
                                                        <th>Page Views</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr><td colspan="4" class="text-center">Memuat data...</td></tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Card Domain Active (Dipertahankan) --}}
                        <div class="row">
                            <div class="col-12">
                                <div class="card shadow-none border">
                                    <div class="card-header"><h5 class="mb-0">Domain Aktif (Digunakan dalam Campaign)</h5></div>
                                    <ul class="list-group list-group-flush border-top-0">
                                        @forelse($domains as $domain)
                                        <li class="list-group-item">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <div class="avtar avtar-s bg-primary text-white"><i class="ti ti-link"></i></div>
                                                </div>
                                                <div class="flex-grow-1 mx-2">
                                                    <h6 class="mb-1">{{ $domain->domain_url }}</h6>
                                                    <p class="mb-0 text-muted">Domain yang tercatat digunakan Campaign ini.</p>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <a class="avtar avtar-s btn-link-secondary" href="javascript:void(0)" onclick="copy('{{ $domain->domain_url }}')">
                                                        <i class="ti ti-copy f-18"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </li>
                                        @empty
                                        <li class="list-group-item text-center text-muted">Tidak ada domain yang tercatat menggunakan Campaign ini.</li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom-js')
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="{{ asset('assets') }}/js/plugins/apexcharts.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.6.7/axios.min.js"></script>
{{-- 🚨 URUTAN SCRIPT DIPERBAIKI UNTUK DATERANGEPICKER 🚨 --}}
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>


<script>
    // 🚨 KUNCI: Gunakan Campaign ID
    const campaignId = {{ $campaign->id }};
    // Menggunakan nama rute yang sudah kita definisikan
    const analyticUrl = "{{ route('campaign.analytic.data', ['campaign' => $campaign->id]) }}";

    const notyf = new Notyf({ duration: 3000, position: { x: 'right', y: 'top' } });

    let dailyChart, countryChart, deviceChart;

    // Inisialisasi Chart ApexCharts
    function initCharts() {
        // Daily Traffic Chart (Line Chart)
        const dailyOptions = {
            chart: { type: 'line', height: 350, toolbar: { show: false } },
            series: [
                { name: 'Page Views', data: [] },
                { name: 'Visitors', data: [] }
            ],
            xaxis: { categories: [] },
            stroke: { curve: 'smooth' },
            tooltip: { x: { format: 'dd MMM yyyy' } }
        };
        dailyChart = new ApexCharts(document.querySelector("#daily-traffic-chart"), dailyOptions);
        dailyChart.render();

        // Traffic by Country Chart (Bar Horizontal Chart)
        const countryOptions = {
            chart: { type: 'bar', height: 350, toolbar: { show: false } },
            series: [{ name: 'Visitors', data: [] }],
            xaxis: { categories: [] },
            plotOptions: { bar: { horizontal: true, distributed: true } },
            dataLabels: { enabled: true }
        };
        countryChart = new ApexCharts(document.querySelector("#traffic-by-country-chart"), countryOptions);
        countryChart.render();

        // Traffic by Device Chart (Donut Chart)
        const deviceOptions = {
            chart: { type: 'donut', height: 350 },
            series: [],
            labels: [],
            legend: { position: 'bottom' },
            responsive: [{ breakpoint: 480, options: { chart: { width: 200 }, legend: { position: 'bottom' } } }]
        };
        deviceChart = new ApexCharts(document.querySelector("#traffic-by-device-chart"), deviceOptions);
        deviceChart.render();
    }

    // Fungsi untuk mengambil dan memperbarui data
    function fetchAndUpdateData() {
        if (!($('#dateRangePicker').data('daterangepicker'))) {
            notyf.error('Error: Date picker belum siap.');
            return;
        }

        const dateRange = $('#dateRangePicker').data('daterangepicker');
        const startDate = dateRange.startDate.format('YYYY-MM-DD');
        const endDate = dateRange.endDate.format('YYYY-MM-DD');

        $('#city-traffic-table tbody').html('<tr><td colspan="4" class="text-center">Memuat data...</td></tr>');
        $('#top-shortlinks-list').html('<li class="list-group-item text-center text-muted">Memuat data...</li>');
        $('#utm-performance-table tbody').html('<tr><td colspan="4" class="text-center">Memuat data...</td></tr>');

        axios.get(analyticUrl, {
            params: {
                start_date: startDate,
                end_date: endDate
            }
        })
        .then(response => {
            const data = response.data;

            // --- 0. Update Summary Cards ---
            $('#summary-pageviews').text(data.summary.totalPageViews);
            $('#summary-visitors').text(data.summary.totalVisitors);
            $('#summary-avgpage').text(data.summary.avgPagePerVisit);
            $('#summary-lasthit').text(data.summary.lastHitTime);


            // --- 1. Update Daily Traffic Chart (Line Chart) ---
            const dates = data.dailyTraffic.map(item => item.traffic_date);
            const pageViews = data.dailyTraffic.map(item => item.page_views);
            const visitors = data.dailyTraffic.map(item => item.visitors);

            dailyChart.updateOptions({ xaxis: { categories: dates } });
            dailyChart.updateSeries([
                { name: 'Page Views', data: pageViews },
                { name: 'Visitors', data: visitors }
            ]);

            // --- 2. Update Traffic by Country Chart (Bar Horizontal Chart) ---
            const countryLabels = data.trafficByCountry.map(item => item.country || 'Unknown');
            const countrySeries = data.trafficByCountry.map(item => item.visitors);

            countryChart.updateOptions({ xaxis: { categories: countryLabels } });
            countryChart.updateSeries([{ name: 'Visitors', data: countrySeries }]);

            // --- 3. Update Traffic by Device Chart (Donut Chart) ---
            const deviceLabels = data.trafficByDevice.map(item => item.device_type || 'Unknown');
            const deviceSeries = data.trafficByDevice.map(item => item.visitors);

            deviceChart.updateOptions({ labels: deviceLabels });
            deviceChart.updateSeries(deviceSeries);

            // --- 4. Update Traffic by City Table ---
            const cityTableBody = $('#city-traffic-table tbody');
            cityTableBody.empty();

            if(data.trafficByCity.length === 0) {
                cityTableBody.append('<tr><td colspan="4" class="text-center">Tidak ada data trafik untuk periode ini.</td></tr>');
            } else {
                data.trafficByCity.forEach(item => {
                    cityTableBody.append(`
                        <tr>
                            <td>${item.city || 'Unknown'}</td>
                            <td>${item.country || 'Unknown'}</td>
                            <td>${item.visitors}</td>
                            <td>${item.page_views}</td>
                        </tr>
                    `);
                });
            }

            // --- 5. 🚨 BARU: Update Top Short Links List ---
            const topShortlinksList = $('#top-shortlinks-list');
            topShortlinksList.empty();

            if (data.topShortLinks.length === 0) {
                topShortlinksList.append('<li class="list-group-item text-center text-muted">Tidak ada data Short Link dalam Campaign untuk periode ini.</li>');
            } else {
                data.topShortLinks.forEach((item, index) => {
                    // Asumsi full_url sudah disiapkan di Controller, misalnya: http://domain.com/shortcode
                    const domainPrefix = item.full_url.split('/')[0] + '//' + item.full_url.split('/')[2];

                    console.log(item);
                    topShortlinksList.append(`
                        <li class="list-group-item">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 text-primary">
                                        <a href="https://numpang.xyz/${item.short_url}">https://numpang.xyz/<strong>${item.short_url}</strong></a>
                                    </h6>
                                     <a href="/seoadmin/project-analytic/project/${item.id}/analytic"> <small class="text-muted" title="${item.original_url}">
                                       Views: ${item.page_views.toLocaleString()} | Visitors: ${item.visitors.toLocaleString()} |  ${item.description}
                                    </small></strong></a>

                                </div>
                                <a href="/seoadmin/project-analytic/project/${item.id}/analytic">
                                <div class="flex-shrink-0">
                                    <span class="badge bg-success-subtle text-success">
                                        ${item.visitors.toLocaleString()} Visitor
                                    </span>
                                </div>
                                </a>
                            </div>
                        </li>
                    `);
                });
            }

            const utmTableBody = $('#utm-performance-table tbody');
            utmTableBody.empty();

            if (!data.utmPerformance || data.utmPerformance.length === 0) {
                utmTableBody.append('<tr><td colspan="4" class="text-center">Belum ada traffic dengan UTM untuk short link dalam campaign ini.</td></tr>');
            } else {
                data.utmPerformance.forEach(item => {
                    utmTableBody.append(`
                        <tr>
                            <td><span class="badge bg-primary">${item.parameter}</span></td>
                            <td>${item.value}</td>
                            <td>${item.visitors}</td>
                            <td>${item.page_views}</td>
                        </tr>
                    `);
                });
            }

            notyf.success('Data analitik berhasil diperbarui!');
        })
        .catch(error => {
            console.error('Error fetching data:', error);
            $('#city-traffic-table tbody').html('<tr><td colspan="4" class="text-center text-danger">Gagal memuat data. Periksa koneksi atau server.</td></tr>');
            $('#top-shortlinks-list').html('<li class="list-group-item text-center text-danger">Gagal memuat data Short Link.</li>');
            $('#utm-performance-table tbody').html('<tr><td colspan="4" class="text-center text-danger">Gagal memuat data.</td></tr>');
            notyf.error('Gagal mengambil data analitik.');
        });
    }

    // Initial load ketika DOM siap
    $(document).ready(function() {

        // 🚨 INISIALISASI DATERANGEPICKER DI SINI 🚨
        $('#dateRangePicker').daterangepicker({
            startDate: moment().subtract(29, 'days'),
            endDate: moment(),
            ranges: {
               'Hari Ini': [moment(), moment()],
               'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
               '7 Hari Terakhir': [moment().subtract(6, 'days'), moment()],
               '30 Hari Terakhir': [moment().subtract(29, 'days'), moment()],
               'Bulan Ini': [moment().startOf('month'), moment().endOf('month')],
               'Bulan Lalu': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            locale: { format: 'YYYY-MM-DD' }
        }, function(start, end, label) {
            // Callback setelah range dipilih
        });

        initCharts();
        fetchAndUpdateData(); // Ambil data pertama kali

        // Event listener untuk tombol filter
        $('#filterDataBtn').on('click', fetchAndUpdateData);
    });

    // Fungsi copy (dibuat sederhana untuk contoh)
    function copy(text) {
        navigator.clipboard.writeText(text).then(() => {
            notyf.success('Link berhasil dicopy!');
        }).catch(err => {
            console.error('Gagal menyalin: ', err);
            notyf.error('Gagal menyalin link.');
        });
    }
</script>
@endsection
