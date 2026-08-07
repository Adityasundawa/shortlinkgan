@extends('admin.components.main')

@section('page.title',''.$detail->campaign_name.' | Analytic')

@section('custom-css')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
@endsection

@section('main-content')
<!-- [ Main Content ] start -->
<div class="pc-container">
    <div class="pc-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0)">Project Analytic</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('project_analytic.list') }}">List Project</a></li>
                            <li class="breadcrumb-item" aria-current="page">Analytic</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h2 class="mb-0">Project Analytics</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->

        <div class="page-content mt-4">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <div class="avtar avtar-m bg-light-danger">
                                        <i class="ti ti-brand-google f-18"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 mx-3">
                                    <h4 class="mb-1">{{ $detail->campaign_name }}</h4>
                                    <p class="mb-0">/{{ $detail->short_url }}</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <a href="#" class="avtar avtar-s btn-link-secondary">
                                        <i class="ti ti-bookmarks f-18"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xl-4 col-md-6">
                                    <div class="card">
                                        <div class="card-body pb-0">
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <h5 class="mb-0">Domain Active</h5>
                                            </div>
                                        </div>
                                        <ul class="list-group list-group-flush border-top-0">
                                            @foreach($domains as $domain)
                                            <li class="list-group-item">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <div class="avtar avtar-s bg-primary text-white">
                                                            <i class="ti ti-link"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 mx-2">
                                                        <h6 class="mb-1">{{ $domain->domain_url }}</h6>
                                                        <a href="{{ $domain->domain_url }}/{{ $detail->short_url }}" target="new" class="mb-0">{{ $domain->domain_url }}/{{ $detail->short_url }}</a>
                                                    </div>
                                                    <div class="flex-shrink-0">
                                                        <div class="dropdown">
                                                            <a class="avtar avtar-s btn-link-secondary" href="javascript:void(0)" onclick="copy('{{ $domain->domain_url }}/{{ $detail->short_url }}')">
                                                                <i class="ti ti-copy f-18"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>

                            </div>

                            <div class="row mt-5">
                                <div class="col-12 mb-3 border-bottom pb-3">
                                    <div class="d-flex gap-3 justify-content-between">
                                        <h4>Analytic</h4>
                                        <div id="reportrange" name="reportrange" class="d-flex align-items-center justify-content-between gap-2 border px-3 py-2 rounded-2">
                                            <i class="ti ti-calendar-stats"></i>
                                            <span></span> <b class="ti ti-caret-down"></b>
                                        </div>
                                    </div>
                                </div>

                                <div id="area-chart-1" data-visitor-data="{{ json_encode($linktraffic) }}">
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<!-- [ Main Content ] end -->
@endsection

@section('custom-js')
<!-- [Page Specific JS] start -->
<script src="{{ asset('assets') }}/js/plugins/apexcharts.min.js"></script>
<script src="{{ asset('assets') }}/js/plugins/choices.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
<script src="{{ asset('assets') }}/js/plugins/sweetalert2.all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.6.7/axios.min.js" integrity="sha512-NQfB/bDaB8kaSXF8E77JjhHG5PM6XVRxvHzkZiwl3ddWCEPBa23T76MuWSwAJdMGJnmQqM0VeY9kFszsrBEFrQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<!-- [Page Specific JS] ends -->

<script>
    function copy(text) {
        navigator.clipboard.writeText(text)
    }

</script>

<script type="text/javascript">
    function filterData(start = new Date, end = new Date) {
        let url = `{{ route('project_analytic.analytic_json', ['project_id'=>':dataID']) }}`.replace(':dataID', `{{ $detail->id }}`)
        axios.get(url, {
                params: {
                    start: start
                    , end: end
                }
            })
            .then(res => {
                console.log(res);
                let keys = Object.keys(res.data.dataJSON)

                // Update Chart
                ApexCharts.exec('mychart', 'updateOptions', {
                    xaxis: {
                        categories: res.data.dates.carbon.map((data) => data)
                    }
                , }, false, true);

                let updateSeries = [];
                keys.map((key, index) => {
                    updateSeries.push({
                        name: keys[index]
                        , data: res.data.dataJSON[keys[index]]
                    });
                })

                ApexCharts.exec('mychart', 'updateSeries', updateSeries, true);
            })
            .catch(err => {
                console.log('terjadi kesalahan', err)
            })
    }

    $(function() {
        var start = moment().subtract(7, 'days');
        var end = moment();

        function cb(start, end) {
            filterData(start, end);
            $('#reportrange span').html(start.format('D MMMM YYYY') + ' - ' + end.format('D MMMM YYYY'));
        }

        $('#reportrange').daterangepicker({
            startDate: start
            , endDate: end
            , maxDate: new Date()
            , ranges: {
                'Today': [moment(), moment()]
                , 'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')]
                , 'Last 7 Days': [moment().subtract(6, 'days'), moment()]
                , 'Last 30 Days': [moment().subtract(29, 'days'), moment()]
                , 'This Month': [moment().startOf('month'), moment().endOf('month')]
                , 'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cb);

        cb(start, end);
    });

</script>


<script>
    var visitorData = JSON.parse(document.getElementById('area-chart-1').dataset.visitorData);

    var visitorDataPerDate = {};

    visitorData.forEach(function(entry) {
        var date = entry.date;
        var domain = entry.domain;
        var visitors = entry.visitors;

        if (!visitorDataPerDate[date]) {
            visitorDataPerDate[date] = {};
        }

        if (!visitorDataPerDate[date][domain]) {
            visitorDataPerDate[date][domain] = 0;
        }

        visitorDataPerDate[date][domain] += visitors;
    });

    var dates = Object.keys(visitorDataPerDate);
    var visitors = Object.values(visitorDataPerDate);

    console.log(visitorDataPerDate);

    var options = {
        chart: {
            id: 'mychart',
            height: 550,
            type: 'area'
        },
        dataLabels: {
            formatter: function(value) {
                return numberFormat(value) + ' visitor';
            }
        },
        stroke: {
            curve: 'smooth'
        },
        series: [{
            name: 'Visitors',
            data: visitors
        }],
        xaxis: {
            categories: dates
        },
        tooltip: {
            x: {
                format: 'dd-MM-yyyy'
            }
        }
    };

    var chart = new ApexCharts(document.querySelector('#area-chart-1'), options);
    chart.render();

    // Fungsi untuk memformat angka dengan ribuan dan desimal
    function numberFormat(number, decimals = 0, decimalSeparator = ",", thousandsSeparator = ".") {
        let formattedNumber = number.toFixed(decimals).toString();
        let parts = formattedNumber.split(".");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousandsSeparator);
        formattedNumber = parts.join(decimalSeparator);
        return formattedNumber;
    }
</script>
@endsection
