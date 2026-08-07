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
                                <li class="breadcrumb-item"><a href="https://ableproadmin.com/navigation/index.html">Home</a>
                                </li>
                                <li class="breadcrumb-item"><a href="javascript: void(0)">Dashboard</a></li>
                                <li class="breadcrumb-item" aria-current="page">Create New User</li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h2 class="mb-0">Create New User</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->


            <!-- [ Main Content ] start -->
            <form action="{{ route('admin.store_new') }}" method="POST" onsubmit="alerttambah(event)" id="tambahdata">
                @csrf
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5>Create New User</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="row mb-3">
                                            <label for="team" class="col-sm-3 col-form-label mt-3">Team</label>
                                            <div class="col-sm-9 mt-3">
                                                <select name="team" class="form-control">
                                                    @foreach ($teamss as $team)
                                                        <option value="{{ $team->id }}">{{ $team->team_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <label for="nama" class="col-sm-3 col-form-label mt-3">Nama</label>
                                            <div class="col-sm-9 mt-3">
                                                <input type="text" name="name" class="form-control"
                                                    placeholder="Nama">
                                            </div>
                                            <label for="username" class="col-sm-3 col-form-label mt-3">Username</label>
                                            <div class="col-sm-9 mt-3">
                                                <input type="text" name="username" id="username" class="form-control"
                                                    placeholder="Username" oninput="checkDuplicate(this)">
                                            </div>
                                            <label for="userlabel" class="col-sm-3 col-form-label mt-3">User Label</label>
                                            <div class="col-sm-9 mt-3">
                                                <input type="text" name="userlabel" id="userlabel" class="form-control"
                                                    placeholder="User Label" oninput="validateforem(event)">
                                            </div>

                                            <label for="email" class="col-sm-3 col-form-label mt-3">Email</label>
                                            <div class="col-sm-9 mt-3">
                                                <input type="text" name="email" id="email" class="form-control"
                                                    placeholder="Email" oninput="checkDuplicate(this)">
                                            </div>
                                            <label for="password" class="col-sm-3 col-form-label mt-3">Password</label>
                                            <div class="col-sm-9 mt-3">
                                                <div class="input-group">
                                                    <input type="password" id="password" name="password"
                                                        class="form-control" placeholder="Password">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text"
                                                            onclick="togglePasswordVisibility('password')">
                                                            <i id="password-toggle" class="ti ti-eye"
                                                                style="font-size: 20px; line-height: 38px;"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <label for="confirm-password" class="col-sm-3 col-form-label mt-3">Confirm
                                                Password</label>
                                            <div class="col-sm-9 mt-3">
                                                <div class="input-group">
                                                    <input type="password" id="confirm-password" name="confirm-password"
                                                        class="form-control" placeholder="Confirm Password"
                                                        oninput="confirmPassword(event)">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text"
                                                            onclick="togglePasswordVisibility('confirm-password')">
                                                            <i id="confirm-password-toggle" class="ti ti-eye"
                                                                style="font-size: 20px; line-height: 38px;"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <label for="role" class="col-sm-3 col-form-label mt-3">Role</label>
                                            <div class="col-sm-9 mt-3">
                                                <select name="role" class="form-control">
                                                    @foreach ($roless as $role)
                                                        <option value="{{ $role->role }}">{{ $role->role }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer d-flex justify-content-between gap-3">
                                    <a href="{{ route('admin.list_user') }}" class="btn btn-outline-secondary">Kembali</a>
                                    <button type="submit" class="btn btn-primary">Simpan Data <i
                                            class="ti ti-arrow-narrow-right"></i></button>
                                </div>
                                <!-- [ Main Content ] end -->
                            </div>
                        </div>
            </form>
            <!-- [ Main Content ] end -->
        @endsection

        @section('custom-js')
            @include('sweetalert::alert')
            <!-- [Page Specific JS] start -->
            <script src="{{ asset('assets') }}/js/plugins/apexcharts.min.js"></script>
            <script src="{{ asset('assets') }}/js/pages/dashboard-analytics.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <!-- [Page Specific JS] end -->


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

                function alerttambah(event) {
                    var confirmpassword = document.getElementById("confirm-password").value;
                    var password = document.getElementById("password").value;
                    var username = document.getElementById("username").value;
                    var email = document.getElementById("email").value;
                    var userlabel = document.getElementById("userlabel").value;

                    if (!username || !email || !password || !confirmpassword || !userlabel) {
                        // Tampilkan pesan kesalahan menggunakan SweetAlert 2
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Harap lengkapi semua bidang sebelum melanjutkan.'
                        });
                        event.preventDefault();
                      } else if (confirmpassword !== password) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Password harus sama.'
                            });
                            event.preventDefault();
                        } else {
                            Swal.fire({
                                position: "top-center",
                                icon: "success",
                                title: "Data berhasil ditambahkan",
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }

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

                    function togglePasswordVisibility(inputId) {
                        var inputElement = document.getElementById(inputId);
                        var iconElement = document.getElementById(`${inputId}-toggle`);
                        if (inputElement.type === "password") {
                            inputElement.type = "text";
                            iconElement.classList.remove("ti-eye");
                            iconElement.classList.add("ti-eye-off");
                        } else {
                            inputElement.type = "password";
                            iconElement.classList.remove("ti-eye-off");
                            iconElement.classList.add("ti-eye");
                        }
                    }
            </script>
