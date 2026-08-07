@extends('admin.components.main')

@section('main-content')
@php
    use Carbon\Carbon;
    use App\Models\DomainDecentralize;
@endphp
<div class="pc-container">
    <div class="pc-content">
      <!-- [ breadcrumb ] start -->
      <div class="page-header">
        <div class="page-block">
          <div class="row align-items-center">
            <div class="col-md-12">
              <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin">Home</a></li>
                <li class="breadcrumb-item" aria-current="page">Shorted Link</li>
                <li class="breadcrumb-item" aria-current="page">Statistic</li>
              </ul>
            </div>
            <div class="col-md-12">
              <div class="page-header-title">
                <h2 class="mb-0">Statistic</h2>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- [ breadcrumb ] end -->

      <!-- [ Main Content ] start -->
      <div class="row">
        <!-- [ sample-page ] start -->
        <div class="card">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between pb-0 ">
              <div>
                <h4 class="mb-0">S.id/{{$a->short_url}}</h4>
                @if (strlen($a->original_url) > 63)
                  <small class="card-subtitle">Original URL: <a href="{{$a->original_url}}" >{{ substr($a->original_url, 0, 60) }}...</a></small>
                @else
                <small class="card-subtitle">Original URL: <a href="{{$a->original_url}}" >{{$a->original_url}}</a></small>
                @endif
              </div>
            <div class="d-flex justify-content-between">
              <div>
                @if ($all)
                <h4 class=>{{$all}}</h4>
                @else
                <h4 class="">0</h4>
                @endif
                <div class="d-flex align-items-center mb-2">
                  <div class="flex-shrink-0">
                    <span class="p-1 d-block bg-primary rounded-circle">
                      <span class="visually-hidden">New alerts</span>
                    </span>
                  </div>
                  <div class="flex-grow-1 ms-2">
                    <small class="mb-0">Lifetime Visitor</small>
                  </div>
                </div>
              </div>
              <div class="ms-4">
                @if ($count)
                <h4 class=>{{$count}}</h4>
                @else
                <h4 class="">0</h4>
                @endif
                <div class="d-flex align-items-center mb-2">
                  <div class="flex-shrink-0">
                    <span class="p-1 d-block bg-warning rounded-circle">
                      <span class="visually-hidden">New alerts</span>
                    </span>
                  </div>
                  <div class="flex-grow-1 ms-2">
                    <small class="mb-0">Unique Visitor</small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-12 col-xxl-12">
          <div class="card">
            <div class="card-body">
              <h4>Statistic</h4>
              <div class="row">
                <div class="col-lg-4">
                  <div id="total-income-graph"></div>
                  <div class="d-flex">
                    <div class="d-flex align-items-center">
                      <div class="flex-shrink-0">
                        <span class="p-1 d-block bg-primary rounded-circle">
                          <span class="visually-hidden">New alerts</span>
                        </span>
                      </div>
                      <div class="flex-grow-1 ms-2">
                        <small class="mb-0">Lifetime Visitor</small>
                      </div>
                    </div>
                    <div class="d-flex align-items-center ms-5">
                      <div class="flex-shrink-0">
                        <span class="p-1 d-block bg-warning rounded-circle">
                          <span class="visually-hidden">New alerts</span>
                        </span>
                      </div>
                      <div class="flex-grow-1 ms-2">
                        <small class="mb-0">Unique Visitor</small>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-8 col-xxl-8 pt-4">
                  <div id="customer-rate-graph"></div>
                </div>
              </div>
            </div>
            @if (!$get->isEmpty())
              <div class="flex-grow-1 ms-0">
                  <table class="table">
                      <thead>
                          <tr>
                              <th>Tanggal</th>
                              <th>Hari</th>
                              <th>Unique Visitor Day</th>
                              <th>Unique Visitor</th>
                              <th>Domain</th>
                          </tr>
                      </thead>
                      <tbody>
                          @foreach ($get as $item)
                              @php
                                  $timestamp = strtotime($item->date);
                                  $hari = Carbon::createFromTimestamp($timestamp)->locale('id')->dayName;
                                  $domain = DomainDecentralize::find($item->domain_decentralizes_id);
                              @endphp
                              <tr>
                                  <td>{{ $item->date }}</td>
                                  <td>{{ $hari }}</td>
                                  <td>{{ $item->unique_visitor_day }}</td>
                                  <td>{{ $item->visitor_day }}</td>
                                  <td>{{ $domain->domain_url }}</td>
                              </tr>
                          @endforeach
                      </tbody>
                  </table>
              </div>
            @else
            @endif          
          </div>          
          </div>
        </div>
        <!-- [ sample-page ] end -->
      </div>
      <!-- [ Main Content ] end -->
    </div>
  </div>
@endsection

@section('custom-js')
<script src="{{ asset('assets') }}/js/plugins/popper.min.js"></script>
<script src="{{ asset('assets') }}/js/plugins/simplebar.min.js"></script>
<script src="{{ asset('assets') }}/js/plugins/bootstrap.min.js"></script>
<script src="{{ asset('assets') }}/js/fonts/custom-font.js"></script>
<script src="{{ asset('assets') }}/js/config.js"></script>
<script src="{{ asset('assets') }}/js/pcoded.js"></script>
<script src="{{ asset('assets') }}/js/plugins/feather.min.js"></script>
<script src="{{ asset('assets') }}/js/plugins/apexcharts.min.js"></script>
<script src="{{ asset('assets') }}/js/pages/w-chart.js"></script>
@endsection
