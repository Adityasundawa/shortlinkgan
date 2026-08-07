@extends('admin.components.main')
@section('main-content')


@section('main-content')
<!-- [ Main Content ] start -->
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="pc-container">
    <div class="pc-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                            <li class="breadcrumb-item" aria-current="page">List User</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h2 class="mb-0">List User</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->

        <!-- [ Main Content ] start -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>User List</h5>
                    </div>
                    <div class="card-footer d-flex gap-3">
                        <a href="{{ route('admin.create_new_user') }}" class="btn btn-primary">Tambah User <i class="ti ti-arrow-narrow-right"></i></a>
                    </div>
                    
                    
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Team</th>
                                        <th>Name</th>
                                        <th>Username</th>
                                        <th>Label User</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th></th>
                                        <!-- Add more columns if needed -->
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->id_team }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->username }}</td>
                                        <td>{{ $user->user_label }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->role }}</td>
                                        <td colspan="2"><a href="{{route('admin.edit_user', $user->id)}}" class="btn btn-primary rounded-1" ><i class="ti ti-edit">&nbsp;</i>Edit</a>
                                            <form id="delete-form-{{$user->id}}" action="{{ route('admin.delete_user', $user->id) }}" method="post" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger rounded-1" onclick="confirmhapus({{$user->id}})"><i class="ti ti-trash">&nbsp;</i>Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ Main Content ] end -->
    </div>
</div>
<!-- [ Main Content ] end -->
@endsection


@section('custom-js')
<!-- [Page Specific JS] start -->
<script src="{{ asset('assets') }}/js/plugins/apexcharts.min.js"></script>
<script src="{{ asset('assets') }}/js/pages/dashboard-analytics.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmhapus(userId) {
        Swal.fire({
            title: "Apakah Anda yakin?",
            text: "Anda tidak akan dapat mengembalikannya!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, hapus!",
            cancelButtonText: "Batal"
        }).then(result => {
            if  (result.isConfirmed) {
                const token = document.querySelector('meta[name="csrf-token"]').content;

                fetch(`{{url('/admin/user-management')}}/delete-user/${userId}`, {method: 'GET', headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": token
                }})
                    .then(res => {
                        Swal.fire({
                            title: "Hapus!",
                            text: "Anda berhasil menghapus.",
                            icon: "success"
                        }).then(() => {
                            location.reload();
                        })
                    })
            }
        })
    }
</script>


    
<!-- [Page Specific JS] end -->
@endsection
