@extends('admin.components.main')

@section('custom-css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
{{-- CSS untuk ApexCharts (opsional, tapi baik untuk styling) --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.44.0/dist/apexcharts.css">
<style>
    /* Style sederhana untuk memformat angka besar di kartu */
    .stat-card-number {
        font-size: 2.25rem;
        font-weight: 700;
        line-height: 1.2;
    }
    .stat-card-title {
        font-size: 0.9rem;
        color: #6c757d;
        text-transform: uppercase;
    }
    .list-group-item .badge {
        font-size: 0.9rem;
        padding: 0.4em 0.6em;
    }
</style>
@endsection

@section('main-content')
<div class="pc-container">
    <div class="pc-content">
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0)">Dashboard</a></li>
                            <li class="breadcrumb-item" aria-current="page">Analytics</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h2 class="mb-0">Dashboard</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="page-content mt-4">

            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <div class="card text-center">
                        <div class="card-body">
                            <div class="stat-card-title">Total Microsite Visitors</div>
                            <div class="stat-card-number text-primary">{{ number_format($totalMicrositeVisitors) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card text-center">
                        <div class="card-body">
                            <div class="stat-card-title">Unique Microsite Visitors</div>
                            <div class="stat-card-number text-info">{{ number_format($totalMicrositeUnique) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card text-center">
                        <div class="card-body">
                            <div class="stat-card-title">Total Shortlink Visitors</div>
                            <div class="stat-card-number text-success">{{ number_format($totalShortlinkVisitors) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card text-center">
                        <div class="card-body">
                            <div class="stat-card-title">Unique Shortlink Visitors</div>
                            <div class="stat-card-number text-warning">{{ number_format($totalShortlinkUnique) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Traffic (Last 7 Days)</h5>
                        </div>
                        <div class="card-body">
                            <div id="traffic-chart"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Top 5 Microsites by Traffic</h5>
                        </div>
                        <div class="card-body p-0">
                            @if($topMicrosites->isEmpty())
                                <div class="text-center p-5">No Microsite traffic data found.</div>
                            @else
                                <ul class="list-group list-group-flush">
                                    @foreach($topMicrosites as $microsite)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-0">{{ $microsite->title }}</h6>
                                                <small class="text-muted">{{ $microsite->short_url }}</small>
                                            </div>
                                            <span class="badge bg-light-primary text-primary rounded-pill">{{ number_format($microsite->total_visitors) }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Top 5 Shortlinks by Traffic</h5>
                        </div>
                        <div class="card-body p-0">
                             @if($topShortlinks->isEmpty())
                                <div class="text-center p-5">No Shortlink traffic data found.</div>
                            @else
                                <ul class="list-group list-group-flush">
                                    @foreach($topShortlinks as $shortlink)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-0">{{ $shortlink->campaign_name ?? 'No Campaign' }}</h6>
                                                <small class="text-muted">{{ $shortlink->short_url }}</small>
                                            </div>
                                            <span class="badge bg-light-success text-success rounded-pill">{{ number_format($shortlink->total_visitors) }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            </div>

    </div>
</div>
@endsection

@section('custom-js')
<script src="{{ asset('assets') }}/js/plugins/choices.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
<script src="{{ asset('assets') }/js/plugins/sweetalert2.all.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
    (function () {
        // Ambil data dari controller
        const chartData = @json($chartData);

        var options = {
            chart: {
                type: 'line',
                height: 350,
                toolbar: {
                    show: true
                }
            },
            series: [{
                name: 'Microsite Visitors',
                data: chartData.microsite
            }, {
                name: 'Shortlink Visitors',
                data: chartData.shortlink
            }],
            xaxis: {
                categories: chartData.labels
            },
            yaxis: {
                title: {
                    text: 'Total Visitors'
                }
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            colors: ['#007bff', '#28a745'], // Biru (Primary) dan Hijau (Success)
            tooltip: {
                x: {
                    format: 'dd MMM yyyy'
                },
            },
            legend: {
                position: 'top'
            }
        };

        var chart = new ApexCharts(document.querySelector("#traffic-chart"), options);
        chart.render();
    })();
</script>
@endsection
