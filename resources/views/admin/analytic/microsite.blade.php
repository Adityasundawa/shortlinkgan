@extends('admin.components.main')

@section('page.title',''.$shortLink->campaign_name.' | Analytic')

@section('custom-css')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
{{-- Asumsikan Anda menggunakan style dari Blade sebelumnya --}}
<style>
    .utm-group-row td {
        background: var(--bs-tertiary-bg);
        color: var(--bs-secondary-color);
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .utm-value-cell {
        max-width: 360px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .utm-bar-track {
        width: 100%;
        min-width: 160px;
        height: 6px;
        overflow: hidden;
        background: var(--bs-border-color-translucent);
        border-radius: 999px;
    }

    .utm-bar-fill {
        height: 100%;
        min-width: 4px;
        background: #2f80ed;
        border-radius: inherit;
    }

    .utm-tabs .nav-link {
        color: var(--bs-body-color);
        font-weight: 600;
    }

    .utm-tabs .nav-link.active {
        color: #2f80ed;
        border-bottom-color: #2f80ed;
    }

    .button-click-url {
        max-width: 420px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
</style>
@endsection

@section('main-content')
<div class="pc-container">
    <div class="pc-content">
        <div class="page-header">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h2 class="mb-0">Analitik Proyek: {{ $shortLink->campaign_name }}</h2>
                </div>
            </div>
            </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-start">
                            <div class="flex-grow-1 mx-3">
                                <h4 class="mb-1">{{ $shortLink->campaign_name }}</h4>
                                <p class="mb-0">/{{ $shortLink->short_url }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">

                        {{-- SUMMARY ANALYTIC (Mirip Gambar) --}}
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <h4 class="mb-0 text-primary">{{ $summary['totalPageViews'] }}</h4>
                                <p class="mb-0 text-muted">Total Page Views</p>
                            </div>
                            <div class="col-md-3">
                                <h4 class="mb-0 text-success">{{ $summary['totalVisitors'] }}</h4>
                                <p class="mb-0 text-muted">Total Visitors (Unique)</p>
                            </div>
                            <div class="col-md-3">
                                <h4 class="mb-0 text-warning">{{ $summary['avgPagePerVisit'] }}</h4>
                                <p class="mb-0 text-muted">Tampilan halaman per kunjungan</p>
                            </div>
                            <div class="col-md-3">
                                <h4 class="mb-0 text-info">{{ $summary['lastHitTime'] }}</h4>
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

                        {{-- TABEL CLICK BUTTON --}}
                        <div class="row">
                            <div class="col-12">
                                <div class="card shadow-none border">
                                    <div class="card-header">
                                        <h5 class="mb-0">Button Clicks <small class="text-muted fw-normal">(sesuai rentang tanggal terpilih)</small></h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0" id="button-clicks-table">
                                                <thead>
                                                    <tr>
                                                        <th>Caption</th>
                                                        <th>Button Link</th>
                                                        <th class="text-end">Clicks</th>
                                                        <th style="width: 32%"></th>
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

                        {{-- TABEL PERFORMA UTM --}}
                        <div class="row">
                            <div class="col-12">
                                <div class="card shadow-none border">
                                    <div class="card-header">
                                        <h5 class="mb-0">Performa UTM</h5>
                                    </div>
                                    <div class="card-body">
                                        <ul class="nav nav-tabs utm-tabs mb-3" id="utm-tabs" role="tablist">
                                            <li class="nav-item" role="presentation"><button class="nav-link active" type="button" data-utm-param="utm_campaign">Campaign <span class="badge bg-light text-dark ms-1" data-utm-count="utm_campaign">0</span></button></li>
                                            <li class="nav-item" role="presentation"><button class="nav-link" type="button" data-utm-param="utm_source">Source <span class="badge bg-primary ms-1" data-utm-count="utm_source">0</span></button></li>
                                            <li class="nav-item" role="presentation"><button class="nav-link" type="button" data-utm-param="utm_medium">Medium <span class="badge bg-light text-dark ms-1" data-utm-count="utm_medium">0</span></button></li>
                                            <li class="nav-item" role="presentation"><button class="nav-link" type="button" data-utm-param="utm_content">Content <span class="badge bg-light text-dark ms-1" data-utm-count="utm_content">0</span></button></li>
                                            <li class="nav-item" role="presentation"><button class="nav-link" type="button" data-utm-param="utm_term">Term <span class="badge bg-light text-dark ms-1" data-utm-count="utm_term">0</span></button></li>
                                        </ul>
                                        <div class="d-flex flex-column flex-md-row gap-2 justify-content-between mb-3">
                                            <input type="search" class="form-control" id="utm-search-input" placeholder="Search UTM value..." style="max-width: 320px;">
                                            <select class="form-select" id="utm-sort-select" style="max-width: 220px;">
                                                <option value="page_views">Sort: Page Views</option>
                                                <option value="visitors">Sort: Visitors</option>
                                                <option value="value">Sort: Value A-Z</option>
                                            </select>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0" id="utm-performance-table">
                                                <thead>
                                                    <tr>
                                                        <th>Value</th>
                                                        <th class="text-end">Visitors</th>
                                                        <th class="text-end">Page Views</th>
                                                        <th style="width: 32%"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr><td colspan="4" class="text-center">Memuat data...</td></tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="d-flex flex-column flex-md-row gap-2 justify-content-between align-items-md-center mt-3">
                                            <small class="text-muted" id="utm-result-summary">Showing 0 data</small>
                                            <ul class="pagination pagination-sm mb-0" id="utm-pagination"></ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Card Domain Active (seperti kode Anda sebelumnya) --}}
                        <div class="row">
                            <div class="col-12">
                                <div class="card shadow-none border">
                                    <div class="card-header"><h5 class="mb-0">Domain Aktif</h5></div>
                                    <ul class="list-group list-group-flush border-top-0">
                                        @foreach($domains as $domain)
                                        <li class="list-group-item">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <div class="avtar avtar-s bg-primary text-white"><i class="ti ti-link"></i></div>
                                                </div>
                                                <div class="flex-grow-1 mx-2">
                                                    <h6 class="mb-1">{{ $domain->domain_url }}</h6>
                                                    <a href="{{ $domain->domain_url }}/{{ $shortLink->short_url }}" target="_blank" class="mb-0">{{ $domain->domain_url }}/{{ $shortLink->short_url }}</a>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <a class="avtar avtar-s btn-link-secondary" href="javascript:void(0)" onclick="copy('{{ $domain->domain_url }}/{{ $shortLink->short_url }}')">
                                                        <i class="ti ti-copy f-18"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </li>
                                        @endforeach
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
@section('custom-js')
<script src="{{ asset('assets') }}/js/plugins/apexcharts.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.6.7/axios.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script>
    const shortLinkId = {{ $shortLink->id }};
    const analyticUrl = "{{ route('microsite_analytic.analytic_json', $shortLink->id) }}";

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

    function escapeHtml(value) {
        return $('<div>').text(value ?? '').html();
    }

    function formatNumber(value) {
        return Number(value || 0).toLocaleString('en-US');
    }

    const utmLabels = {
        utm_campaign: 'Campaign',
        utm_source: 'Source',
        utm_medium: 'Medium',
        utm_content: 'Content',
        utm_term: 'Term'
    };
    let utmRows = [];
    let utmActiveParam = 'utm_campaign';
    let utmPage = 1;
    const utmPerPage = 25;

    function renderUtmPerformance(rows, emptyMessage) {
        utmRows = rows || [];
        utmPage = 1;
        renderUtmCounts();
        renderUtmTable(emptyMessage);
    }

    function renderUtmCounts() {
        Object.keys(utmLabels).forEach(parameter => {
            const count = utmRows.filter(item => item.parameter === parameter).length;
            $(`[data-utm-count="${parameter}"]`).text(count);
        });
    }

    function renderUtmTable(emptyMessage = 'Belum ada traffic dengan UTM.') {
        const utmTableBody = $('#utm-performance-table tbody');
        const search = ($('#utm-search-input').val() || '').toLowerCase();
        const sortBy = $('#utm-sort-select').val() || 'page_views';
        let filteredRows = utmRows.filter(item => item.parameter === utmActiveParam);

        if (search) {
            filteredRows = filteredRows.filter(item => String(item.value || '').toLowerCase().includes(search));
        }

        filteredRows.sort((a, b) => {
            if (sortBy === 'value') {
                return String(a.value || '').localeCompare(String(b.value || ''));
            }

            return Number(b[sortBy] || 0) - Number(a[sortBy] || 0);
        });

        const totalRows = filteredRows.length;
        const totalPages = Math.max(Math.ceil(totalRows / utmPerPage), 1);
        utmPage = Math.min(utmPage, totalPages);
        const start = (utmPage - 1) * utmPerPage;
        const pageRows = filteredRows.slice(start, start + utmPerPage);
        const maxPageViews = Math.max(...filteredRows.map(item => Number(item.page_views || 0)), 1);

        utmTableBody.empty();

        if (utmRows.length === 0) {
            utmTableBody.append(`<tr><td colspan="4" class="text-center">${emptyMessage}</td></tr>`);
            $('#utm-result-summary').text('Showing 0 data');
            $('#utm-pagination').empty();
            return;
        }

        if (pageRows.length === 0) {
            utmTableBody.append(`<tr><td colspan="4" class="text-center">Tidak ada data untuk ${utmLabels[utmActiveParam]}.</td></tr>`);
        }

        pageRows.forEach(item => {
            const pageViews = Number(item.page_views || 0);
            const percentage = Math.max((pageViews / maxPageViews) * 100, pageViews > 0 ? 3 : 0);

            utmTableBody.append(`
                <tr>
                    <td class="utm-value-cell" title="${escapeHtml(item.value || '-')}"><strong>${escapeHtml(item.value || '-')}</strong></td>
                    <td class="text-end">${formatNumber(item.visitors)}</td>
                    <td class="text-end">${formatNumber(pageViews)}</td>
                    <td><div class="utm-bar-track"><div class="utm-bar-fill" style="width: ${percentage}%"></div></div></td>
                </tr>
            `);
        });

        const end = Math.min(start + pageRows.length, totalRows);
        $('#utm-result-summary').text(totalRows ? `Showing ${start + 1}-${end} of ${totalRows} ${utmLabels[utmActiveParam]}` : `Showing 0 ${utmLabels[utmActiveParam]}`);
        renderUtmPagination(totalPages);
    }

    function renderUtmPagination(totalPages) {
        const pagination = $('#utm-pagination');
        pagination.empty();

        const button = (page, label = page, disabled = false, active = false) => `
            <li class="page-item ${disabled ? 'disabled' : ''} ${active ? 'active' : ''}">
                <button class="page-link" type="button" data-utm-page="${page}" ${disabled ? 'disabled' : ''}>${label}</button>
            </li>
        `;

        pagination.append(button(Math.max(utmPage - 1, 1), 'Previous', utmPage === 1));

        for (let page = 1; page <= totalPages; page++) {
            if (page === 1 || page === totalPages || Math.abs(page - utmPage) <= 1) {
                pagination.append(button(page, page, false, page === utmPage));
            }
        }

        pagination.append(button(Math.min(utmPage + 1, totalPages), 'Next', utmPage === totalPages));
    }

    function renderButtonClicks(rows) {
        const tableBody = $('#button-clicks-table tbody');
        tableBody.empty();

        if (!rows || rows.length === 0) {
            tableBody.append('<tr><td colspan="4" class="text-center">Belum ada button di microsite ini.</td></tr>');
            return;
        }

        const maxClicks = Math.max(...rows.map(item => Number(item.clicks || 0)), 1);

        rows.forEach(item => {
            const clicks = Number(item.clicks || 0);
            const percentage = Math.max((clicks / maxClicks) * 100, clicks > 0 ? 3 : 0);

            tableBody.append(`
                <tr>
                    <td><strong>${escapeHtml(item.title || 'Button')}</strong></td>
                    <td class="button-click-url" title="${escapeHtml(item.url || '-')}">${escapeHtml(item.url || '-')}</td>
                    <td class="text-end">${formatNumber(clicks)}</td>
                    <td><div class="utm-bar-track"><div class="utm-bar-fill" style="width: ${percentage}%"></div></div></td>
                </tr>
            `);
        });
    }

    // Fungsi untuk mengambil dan memperbarui data
    function fetchAndUpdateData() {
        const dateRange = $('#dateRangePicker').data('daterangepicker');
        const startDate = dateRange.startDate.format('YYYY-MM-DD');
        const endDate = dateRange.endDate.format('YYYY-MM-DD');

        // Tampilkan loading state
        $('#city-traffic-table tbody').html('<tr><td colspan="4" class="text-center">Memuat data...</td></tr>');
        $('#utm-performance-table tbody').html('<tr><td colspan="4" class="text-center">Memuat data...</td></tr>');
        $('#button-clicks-table tbody').html('<tr><td colspan="4" class="text-center">Memuat data...</td></tr>');

        axios.get(analyticUrl, {
            params: {
                start_date: startDate,
                end_date: endDate
            }
        })
        .then(response => {
            const data = response.data;

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

            renderButtonClicks(data.buttonClicks);
            renderUtmPerformance(data.utmPerformance, 'Belum ada traffic dengan UTM untuk microsite ini.');

            notyf.success('Data analitik berhasil diperbarui!');
        })
        .catch(error => {
            console.error('Error fetching data:', error);
            $('#city-traffic-table tbody').html('<tr><td colspan="4" class="text-center text-danger">Gagal memuat data. Periksa koneksi atau server.</td></tr>');
            $('#button-clicks-table tbody').html('<tr><td colspan="4" class="text-center text-danger">Gagal memuat data.</td></tr>');
            $('#utm-performance-table tbody').html('<tr><td colspan="4" class="text-center text-danger">Gagal memuat data.</td></tr>');
            notyf.error('Gagal mengambil data analitik.');
        });
    }

    // Initial load ketika DOM siap
    $(document).ready(function() {
        // Inisialisasi Date Range Picker default 30 hari terakhir
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
            // Callback setelah range dipilih (bisa langsung panggil fetchAndUpdateData jika tidak ada tombol filter)
            // console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
        });

        initCharts();
        fetchAndUpdateData(); // Ambil data pertama kali

        // Event listener untuk tombol filter
        $('#filterDataBtn').on('click', fetchAndUpdateData);

        $('#utm-tabs').on('click', '.nav-link', function() {
            $('#utm-tabs .nav-link').removeClass('active');
            $(this).addClass('active');
            utmActiveParam = $(this).data('utm-param');
            utmPage = 1;
            renderUtmTable();
        });

        $('#utm-search-input, #utm-sort-select').on('input change', function() {
            utmPage = 1;
            renderUtmTable();
        });

        $('#utm-pagination').on('click', '[data-utm-page]', function() {
            utmPage = Number($(this).data('utm-page'));
            renderUtmTable();
        });
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

@endsection
