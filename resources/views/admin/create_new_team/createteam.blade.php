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
                            <li class="breadcrumb-item" aria-current="page">Create New Team</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h2 class="mb-0">Create New Team</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->


        <!-- [ Main Content ] start -->
        <form action="{{ route('admin.store_new_team') }}" method="POST" onsubmit="alerttuambah(this)">
          @csrf
          <div class="row">
            <div class="col-md-8">
              <div class="card">
                <div class="card-header">
                  <h5>Create New Team</h5>
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-12">
                      <div class="row mb-3">
                        <label for="teamname" class="col-sm-3 col-form-label mt-3">Nama Team</label>
                        <div class="col-sm-9 mt-3">
                          <input type="text" name="teamname" class="form-control" placeholder="Nama Team">
                        </div>
                        <label for="teamlabel" class="col-sm-3 col-form-label mt-3">Label Team</label>
                        <div class="col-sm-9 mt-3">
                          <input type="text" name="teamlabel" class="form-control" placeholder="Label Team">
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
{{-- @include('sweetalert::alert') --}}
<!-- [Page Specific JS] start -->
<script src="{{ asset('assets') }}/js/plugins/apexcharts.min.js"></script>
<script src="{{ asset('assets') }}/js/pages/dashboard-analytics.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- [Page Specific JS] end -->
@endsection

<script>
  function checkDuplicate(field) {
      var value = field.value;
      var fieldName = field.name;

      // Kirim permintaan Ajax ke backend untuk memeriksa duplikasi
      // Ganti 'check_duplicate_url' dengan URL Anda yang sesuai di backend
      axios.post(`{{ route('admin.check_duplicate') }}`, {
          [fieldName]: value
      })
      .then(response => {
          // Jika nilai tidak duplikat, kosongkan pesan peringatan
          field.setCustomValidity('');
      })
      .catch(error => {
          // Jika nilai duplikat, tampilkan pesan peringatan
          field.setCustomValidity(error.response.data.message);
      });
  }
  function alerttuambah() {
    Swal.fire({
  position: "top-center",
  icon: "success",
  title: "Data berhasil ditambahkan",
  showConfirmButton: false,
  timer: 1500
});
  }
</script>