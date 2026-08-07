@extends('admin.components.main')

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
                            <li class="breadcrumb-item"><a href="https://ableproadmin.com/navigation/index.html">Home</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0)">Dashboard</a></li>
                            <li class="breadcrumb-item" aria-current="page">Edit Team</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h2 class="mb-0">Edit Team</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->

        <!-- [ Main Content ] start -->
        <form action="{{ route('admin.edit_team', $team->id) }}" method="POST" onsubmit="alercak(this)">
          @csrf
          <div class="row">
            <div class="col-md-8">
              <div class="card">
                <div class="card-header">
                  <h5>Edit Team</h5>
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-12">
                      <div class="row mb-3">
                        <label for="teamname" class="col-sm-3 col-form-label mt-3">Nama Team</label>
                        <div class="col-sm-9 mt-3">
                          <input value="{{ $team->team_name }}" type="text" name="teamname" class="form-control" placeholder="Nama Team">
                        </div>
                        <label for="teamslug" class="col-sm-3 col-form-label mt-3">Team Slug</label>
                        <div class="col-sm-9 mt-3">
                          <input value="{{ $team->team_slug }}" type="text" name="teamslug" class="form-control" placeholder="Team Slug">
                        </div>
                        <label for="teamlabel" class="col-sm-3 col-form-label mt-3">Label Team</label>
                        <div class="col-sm-9 mt-3">
                          <input value="{{ $team->team_label }}" type="text" name="teamlabel" class="form-control" placeholder="Label Team">
                        </div>
                      </div>
                  </div>
                </div>
                <div class="card-footer d-flex justify-content-between gap-3">
                  <a href="{{ route('admin.list_team') }}" class="btn btn-outline-secondary">Kembali</a>
                  <button type="submit" class="btn btn-primary">Simpan Data <i class="ti ti-arrow-narrow-right"></i></button>
                </div>
        <!-- [ Main Content ] end -->
    </div>
</div>
</form>
<!-- [ Main Content ] end -->
@endsection

@section('custom-js')
<!-- [Page Specific JS] start -->
<script src="{{ asset('assets') }}/js/plugins/apexcharts.min.js"></script>
<script src="{{ asset('assets') }}/js/pages/dashboard-analytics.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- [Page Specific JS] end -->
@endsection

<script>
  function alercak() {
    Swal.fire({
  position: "top-center",
  icon: "success",
  title: "Data Berhasil di Ubah",
  showConfirmButton: false,
  timer: 1500
});
  }

</script>