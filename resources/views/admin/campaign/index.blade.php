@extends('admin.components.main')

@section('custom-css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
@endsection

@section('main-content')
    <div class="pc-container">
        <div class="pc-content">
            {{-- [ breadcrumb ] start --}}
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item"><a href="javascript: void(0)">Campaign</a></li>
                                <li class="breadcrumb-item" aria-current="page">List Campaign</li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h2 class="mb-0">Campaign Analytics</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- [ breadcrumb ] end --}}

            <div class="page-content mt-4">

                {{-- Bagian Tombol New Campaign --}}
                <div class="card">
                    <div class="card-body p-3">
                        <div class="d-sm-flex align-items-center">
                            <ul class="list-inline me-auto my-1">
                                <li class="list-inline-item">
                                    <button type="button" class="btn btn-lg btn-primary rounded-1" data-bs-toggle="modal"
                                        data-bs-target="#modal-create-campaign"> <i class="fa fa-plus"></i> New Campaign
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- 🚨 TABEL UTAMA 🚨 --}}
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table" id="example">
                                        <thead>
                                            <tr>
                                                <th width="70px">#</th>
                                                <th>Nama Campaign</th>
                                                <th>Pembuat Campaign</th>
                                                <th>Jumlah Short Link</th>
                                                <th>Total Visitor Keseluruhan</th>
                                                <th>Tanggal Dibuat</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($campaigns as $campaign)
                                                @php
                                                    // Menghitung agregasi Short Link dan Visitor
                                                    $totalLinks = $campaign->shortLinks->count();
                                                    // Menjumlahkan visitor_day dari semua traffics di semua shortLinks
                                                    $totalVisitors = $campaign->shortLinks->sum(
                                                        fn($link) => $link->traffics->sum('visitor_day'),
                                                    );
                                                @endphp

                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    {{-- FIX DATATABLES WARNING: Menambahkan data-order --}}
                                                    <td data-order="{{ $campaign->name ?? 'Untitled Campaign' }}">
                                                        <strong>{{ $campaign->name ?? 'Untitled Campaign' }}</strong>
                                                    </td>
                                                    <td>
                                                        {{ $campaign->user->name ?? 'N/A' }}
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-secondary">{{ $totalLinks }} Link</span>
                                                    </td>
                                                    <td data-order="{{ $totalVisitors }}">
                                                        <i class="ti ti-accessible"></i>
                                                        <strong>{{ number_format($totalVisitors) }}</strong> Visitor
                                                    </td>
                                                    <td>
                                                        {{ $campaign->created_at?->format('d M Y') ?? 'N/A' }}
                                                    </td>
                                                    <td>
                                                        <div class="text-end d-flex flex-wrap gap-2 justify-content-end">

                                                            <a href="javascript:void(0)"
                                                                class="btn btn-sm btn-outline-danger rounded-1"
                                                                onclick="deleteData({{ $campaign->id }})"><i
                                                                    class="ti ti-trash"></i> Delete</a>

                                                            <a href="javascript:void(0)"
                                                                class="btn btn-sm btn-outline-primary rounded-1"
                                                                onclick="openEdit({{ $campaign->id }})"><i
                                                                    class="ti ti-edit"></i> Edit Campaign</a>

                                                            <a href="{{ route('campaign.analytic.view', ['campaign' => $campaign->id]) }}"
                                                                class="btn btn-sm btn-success rounded-1"><i
                                                                    class="ti ti-report-analytics"></i> View
                                                                Analytic</a>
                                                        </div>
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
                {{-- 🚨 TABEL UTAMA SELESAI DI SINI 🚨 --}}
            </div>
        </div>
    </div>

    {{-- ================================================================= --}}
    {{-- 🚨 MODAL CREATE CAMPAIGN 🚨 --}}
    {{-- ================================================================= --}}
    <div class="modal fade" id="modal-create-campaign" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="modalCreateCampaignLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCreateCampaignLabel">➕ Buat Campaign Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('campaign.store') }}" method="POST" id="form-create-campaign">
                    @csrf
                    <div class="modal-body">
                        {{-- Custom preload dihilangkan dari HTML --}}

                        <div class="form-group mb-3">
                            <label for="create_name" class="form-label">Nama Campaign *</label>
                            <input type="text" name="name" id="create_name" class="form-control" required
                                placeholder="Contoh: Campaign Ramadhan 2025">
                        </div>

                        <div class="form-group mb-3">
                            <label for="create_description" class="form-label">Deskripsi (Opsional)</label>
                            <textarea name="description" id="create_description" class="form-control" rows="3"
                                placeholder="Deskripsi singkat campaign..."></textarea>
                        </div>

                        <div class="form-group mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="create_is_active" name="is_active"
                                    value="1" checked>
                                <label class="form-check-label" for="create_is_active">Campaign Aktif</label>
                            </div>
                        </div>

                        <div id="create-errors" class="alert alert-danger" style="display:none;"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Campaign</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ================================================================= --}}
    {{-- 🚨 MODAL EDIT CAMPAIGN 🚨 --}}
    {{-- ================================================================= --}}
    <div class="modal fade" id="modal-edit-campaign" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="modalEditCampaignLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditCampaignLabel">✏️ Edit Campaign</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" id="form-edit-campaign">
                    @csrf
                    <div class="modal-body">
                        {{-- Custom preload dihilangkan dari HTML --}}

                        <input type="hidden" name="campaign_id" id="edit_campaign_id">

                        <div class="form-group mb-3">
                            <label for="edit_name" class="form-label">Nama Campaign *</label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="edit_description" class="form-label">Deskripsi (Opsional)</label>
                            <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="form-group mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active"
                                    value="1">
                                <label class="form-check-label" for="edit_is_active">Campaign Aktif</label>
                            </div>
                        </div>

                        <div id="edit-errors" class="alert alert-danger" style="display:none;"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Update Campaign</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- END MODALS --}}
@endsection

@section('custom-js')
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.6.7/axios.min.js"></script>

    <script>
        // Inisialisasi DataTables
        new DataTable('#example', {
            language: {
                emptyTable: 'Tidak ada data Campaign tersedia.'
            }
        });

        const notyf = new Notyf({
            duration: 5000,
            position: {
                x: 'right',
                y: 'top'
            }
        });

        function displayErrors(errors, containerId) {
            const container = $(`#${containerId}`);
            container.empty().hide();
            if (errors) {
                let errorHtml = '<ul class="mb-0 ps-3">';
                for (const key in errors) {
                    errorHtml += `<li>${errors[key][0]}</li>`;
                }
                errorHtml += '</ul>';
                container.html(errorHtml).show();
            }
        }

        // HELPER: Mengubah array dari serializeArray menjadi objek JSON
        function serializeFormToObject(formArray) {
            const obj = {};
            $.each(formArray, function(i, field) {
                obj[field.name] = field.value;
            });
            return obj;
        }


        // ===========================================
        // 1. CREATE (Store) Logic
        // ===========================================
        $('#form-create-campaign').submit(function(e) {
            e.preventDefault();
            const form = $(this);
            let formData = serializeFormToObject(form.serializeArray());

            // Pastikan is_active ada (fix validasi required)
            if (!document.getElementById('create_is_active').checked) {
                formData.is_active = 0;
            }

            $('#create-errors').hide();

            axios.post(form.attr('action'), formData)
                .then(res => {
                    notyf.success('Campaign berhasil dibuat!');
                    $('#modal-create-campaign').modal('hide');
                    window.location.reload();
                })
                .catch(err => {
                    if (err.response && err.response.data.errors) {
                        displayErrors(err.response.data.errors, 'create-errors');
                        notyf.error('Terdapat error validasi pada input Anda.');
                    } else {
                        notyf.error(err.response.data.message || 'Gagal membuat campaign. Silakan coba lagi.');
                    }
                });
        });

        // ===========================================
        // 2. READ Data dan Tampilkan Modal EDIT (openEdit)
        // ===========================================
        window.openEdit = function(campaignId) {
            $('#edit-errors').hide();
            const form = $('#form-edit-campaign');

            $('#modal-edit-campaign').modal('show');

            // 🚨 Menggunakan RUTE ANDA: campaign.editnew (GET) 🚨
            let urlEdit = `{{ route('campaign.editnew', ['project_id' => ':id']) }}`.replace(':id', campaignId);

            // 🚨 Menggunakan RUTE ANDA: campaign.update (POST) 🚨
            let urlUpdate = `{{ route('campaign.update', ['project_id' => ':id']) }}`.replace(':id', campaignId);


            axios.get(urlEdit)
                .then(res => {
                    const data = res.data;

                    $('#edit_campaign_id').val(data.id);
                    $('#edit_name').val(data.name);
                    $('#edit_description').val(data.description || '');

                    $('#edit_is_active').prop('checked', data.is_active);

                    // Set action form ke URL POST UPDATE yang BENAR
                    form.attr('action', urlUpdate);

                })
                .catch(err => {
                    $('#modal-edit-campaign').modal('hide');
                    notyf.error('Gagal mengambil data campaign. Silakan coba lagi.');
                });
        }

        // ===========================================
        // 3. UPDATE Logic
        // ===========================================
        $('#form-edit-campaign').submit(function(e) {
            e.preventDefault();
            const form = $(this);

            let formData = serializeFormToObject(form.serializeArray());
            // Fix validasi is_active
            if (!document.getElementById('edit_is_active').checked) {
                formData.is_active = 0;
            }

            $('#edit-errors').hide();

            // Kirim data ke CampaignController@update (POST)
            axios.post(form.attr('action'), formData)
                .then(res => {
                    notyf.success('Campaign berhasil diperbarui!');
                    $('#modal-edit-campaign').modal('hide');
                    window.location.reload();
                })
                .catch(err => {
                    if (err.response && err.response.data.errors) {
                        displayErrors(err.response.data.errors, 'edit-errors');
                        notyf.error('Terdapat error validasi pada input Anda.');
                    } else {
                        notyf.error(err.response.data.message ||
                            'Gagal memperbarui campaign. Silakan coba lagi.');
                    }
                });
        });

        // ===========================================
        // 4. DELETE Logic
        // ===========================================
        window.deleteData = function(campaignId) {
            Swal.fire({
                title: "Are you sure?",
                text: "Campaign dan semua link terkait akan dihapus permanen!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#dc3545",
                confirmButtonText: "Yes, delete it!",
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-outline-primary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // 🚨 MENGGUNAKAN RUTE Resource Standar: campaign.destroy
                    let urlDelete = `{{ route('campaign.destroy', ['campaign' => ':id']) }}`.replace(':id',
                        campaignId);

                    axios.delete(urlDelete)
                        .then(res => {
                            Swal.fire({
                                title: "Deleted!",
                                text: "Campaign Anda telah dihapus.",
                                icon: "success"
                            }).then(() => {
                                window.location.reload();
                            });
                        })
                        .catch(err => {
                            console.error('terjadi kesalahan', err);
                            notyf.error('Gagal menghapus campaign.');
                        });
                }
            });
        }
    </script>
@endsection
