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
                            <li class="breadcrumb-item" aria-current="page">Edit User</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h2 class="mb-0">Edit User</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->

        <!-- [ Main Content ] start -->
        <form action="{{ route('admin.edit_user', $user->id) }}" method="POST" onsubmit="showAlert(this)">
          @csrf
          <div class="row">
            <div class="col-md-8">
              <div class="card">
                <div class="card-header">
                  <h5>Edit User</h5>
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-12">
                      <div class="row mb-3">
                        <label for="team" class="col-sm-3 col-form-label mt-3">Team</label>
                        <div class="col-sm-9 mt-3">
                          <select name="team" class="form-control">
                            @foreach($teamss as $team)
                                <option @selected($team->id == $user->id_team) value="{{ $team->id }}">{{ $team->team_name }}</option>
                            @endforeach
                        </select>
                        </div>
                        <label for="nama" class="col-sm-3 col-form-label mt-3">Nama</label>
                        <div class="col-sm-9 mt-3">
                          <input value="{{ $user->name }}" type="text" name="name" class="form-control" placeholder="Nama">
                        </div>
                        <label for="username" class="col-sm-3 col-form-label mt-3">Username</label>
                          <div class="col-sm-9 mt-3">
                              <input value="{{ $user->username }}" type="text" name="username" id="username" class="form-control" placeholder="Username" oninput="checkDuplicate(this)">
                          </div>

                        <label for="username" class="col-sm-3 col-form-label mt-3">User Label</label>
                          <div class="col-sm-9 mt-3">
                              <input value="{{ $user->user_label }}" type="text" name="userlabel" id="userlabel" class="form-control" placeholder="User Label" oninput="validateforem(event)">
                          </div>

                          <label for="email" class="col-sm-3 col-form-label mt-3">Email</label>
                          <div class="col-sm-9 mt-3">
                              <input value="{{  $user->email }}" type="text" name="email" id="email" class="form-control" placeholder="Email" oninput="checkDuplicate(this)">
                          </div>
                        <label for="password" class="col-sm-3 col-form-label mt-3">Password</label>
                        <div class="col-sm-9 mt-3">
                          <input value="" type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah password">
                        </div>
                        <label for="role" class="col-sm-3 col-form-label mt-3">Role</label>
                        <div class="col-sm-9 mt-3">
                          <select name="role" class="form-control">
                            @foreach($roless as $role)
                                  <option {{ $role->role == $user->role ? 'selected' : '' }} value="{{ $role->role }}">{{ $role->role }}</option>
                              @endforeach
                        </select>
                        </div>
                      </div>
                  </div>
                </div>
                <div class="card-footer d-flex justify-content-between gap-3">
                  <a href="{{ route('admin.list_user') }}" class="btn btn-outline-secondary">Kembali</a>
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
  function checkDuplicate(field) {
      var value = field.value;
      var fieldName = field.name;

      // Kirim permintaan Ajax ke backend untuk memeriksa duplikasi
      axios.post(`{{ route('admin.check_duplicate') }}`, {
          [fieldName]: value
      })
      .then(response => {
          // Jika nilai tidak duplikat, kosongkan pesan peringatan
          field.setCustomValidity('');

          // Tampilkan pesan alert bahwa data berhasil diubah
      //     Swal.fire({
      //         title: "Success!",
      //         text: "Data berhasil diubah.",
      //         icon: "success"
      //     });
      })
      .catch(error => {
          // Jika nilai duplikat, tampilkan pesan peringatan
          field.setCustomValidity(error.response.data.message);
      });
  }

  function showAlert() {
    Swal.fire({
  position: "top-center",
  icon: "success",
  title: "Data Berhasil di Ubah",
  showConfirmButton: false,
  timer: 1500
});
  }

  function validateforem(event) {
    var userlabel = event.target.value;

    // Periksa apakah panjang user label lebih dari 5 karakter
    if (userlabel.length > 5) {
        // Tampilkan pesan kesalahan menggunakan SweetAlert 2
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'User Label tidak boleh lebih dari 5 huruf.'
        });

        // Kosongkan nilai input
        event.target.value = userlabel.slice(0, 5);

        event.preventDefault(); // Hentikan pengiriman formulir
        event.stopPropagation();
    }
}

</script>
