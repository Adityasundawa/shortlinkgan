@extends('admin.components.main')

@section('page.title', 'Microsite')

@section('custom-css')
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
                            <li class="breadcrumb-item" aria-current="page">Microsite</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h2 class="mb-0">Microsite</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->

        <div class="page-content mt-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-sm-flex align-items-center">
                        <ul class="list-inline me-auto my-1">
                            <li class="list-inline-item">
                                <a href="{{ route('admin.microsite.create') }}" class="btn btn-lg btn-primary rounded-1">
                                    <i class="fa fa-plus"></i> New Microsite
                                </a>
                            </li>
                        </ul>
                        <ul class="list-inline ms-auto my-1">
                            <li class="list-inline-item">
                                <form action="" method="POST" class="form-search" id="searchForm">
                                    @csrf
                                    <i class="ti ti-search" onclick="searchForm()"></i>
                                    <input type="search" name="search" class="form-control" placeholder="Search Projects" value="{{ $search ?? '' }}">
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modalEdit" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Update Domain</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="reloadIt()">
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="custom-preload" style="display: none">
                                <span class="loader"></span>
                            </div>

                            <form action="" method="POST" id="formEdit" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="edit_dataid" id="edit_data_id" value="">
                                <div class="form-group">
                                    <label for="">Microsite Name *</label>
                                    <input type="text" name="microsite_name" id="edit_microsite_name" class="form-control" required placeholder="Project #1">
                                </div>
                                <div class="form-group">
                                    <label for="">Description (Optional)</label>
                                    <textarea name="description" id="edit_description" class="form-control" rows="3" placeholder="your project description"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="">New Images Profile *</label>
                                    <div style="display: flex; align-items: center;">
                                        <img src="" name="image" id="image" alt="Photo Profile" style="width: 180px; height: 165px; margin-bottom: 10px; margin-top: 10px; border-radius: 70%; object-fit: cover;">
                                        <div style="margin-left: 20px; ">
                                            <img id="preview" src="#" alt="your image" style="display: none; width: 180px; height: 165px; margin-bottom: 10px; margin-top: 10px; border-radius: 70%; object-fit: cover;">
                                        </div>
                                    </div>
                                    <div class="input-group copy-wrapper">
                                        <input type="file" name="images" id="edit_images" class="form-control">
                                        <button type="button" id="buttonCancel" class="btn btn-light border border-start-0 rounded-0" style="display: none;" onclick="cancel(event)"><i class="fa fa-times"></i></button>
                                    </div>
                                    <a href="#" class="" id="deleteFoto" style="background-color: transparent; color: #fff; text-decoration: none; display: block;" onclick="deleteProfilePhoto()">Delete profile photo</a>
                                </div>

                                <div class="form-group">
                                    <label for="">Shorted Link</label>
                                    <div class="input-group">
                                        <div class="input-group-text">
                                            {{ ENV('APP_URL') }}/
                                        </div>
                                        <input type="text" name="result_link" id="edit_short_link" value="YHBuasdgj" data-default="YHBuasdgj" data-id="0" required class="form-control result-link" onkeyup="addParamLink(this)">
                                    </div>
                                </div>

                                {{-- Domain Section --}}
                                <div class="pt-3 mt-3 border-top border-secondary">
                                    @forelse($domains as $item)
                                    <div class="form-group mb-3">
                                        <h5>{{ $item['domain_url'] }}</h5>
                                        <div class="input-group copy-wrapper">
                                            <input type="text" data-domain="{{  $item['domain_url'] }}" class="form-control rounded-0 fw-bold alt-domain" value="{{  $item['domain_url'] }}/" readonly>
                                            <button type="button" class="btn btn-light border border-start-0 rounded-0" onclick="copy(event)"><i class="fa fa-copy"></i></button>
                                        </div>
                                    </div>

                                    @empty
                                    <p>Tidak ada data.</p>
                                    @endforelse
                                </div>

                                <div class="pt-3 mt-3 border-top border-secondary">
                                    <h5>Buttons</h5>
                                    <div class="button-fields"></div>
                                </div>
                                <div class="row mt-5 mb-4">
                                    <div class="col-12">
                                        <h5>Button Links</h5>

                                        <div id="buttons-form">

                                        </div>


                                        <div class="text-center">
                                            <button type="button" class="btn btn-success rounded-1 add-button"><i class="ti ti-plus"></i> Add Link</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="pt-3 mt-3 border-top border-secondary">
                                    <h5>Pop Under</h5>
                                    <div class="popunder-fields"></div>
                                </div>
                                <div class="row mt-5 mb-4">
                                    <div class="col-12">
                                        <h5>Pop Under</h5>

                                        <div id="popunder-form">

                                        </div>
                                        <div class="text-center">
                                            <button type="button" class="btn btn-success rounded-1 add-popunder"><i class="ti ti-plus"></i> Add Pop Under</button>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-lg btn-primary w-100 rounded-1 mt-4">Update Data</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Project</th>
                                    <th>Site Code</th>
                                    <th>Author</th>
                                    <th>Date Created</th>
                                    <th>Total Visitor</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($projects as $project)
                                <tr>
                                    <td>{{ ($projects->currentPage() - 1) * $projects->perPage() + $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $project->title }}</strong><br>
                                        <a href="#" target="new"><small>{{ ENV('APP_URL') }}/{{ $project->short_url }}</small></a>
                                    </td>
                                    <td><strong>{{ $project->short_url }}</strong></td>
                                    <td>{{ $project->user->name }}</td>
                                    <td>{{ Carbon::parse($project->created_at)->format('d M Y') }}</td>
                                    <td><strong>{{ $project->trafficts->sum('total_visitors'); }}</strong> Visitor</td>
                                    <td>
                                        <div class="d-flex flex-wrap justify-content-end gap-2">
                                            <a href="javascript:void(0)" class="btn btn-sm btn-outline-danger rounded-1" onclick="removeData({{$project->id}})"><i class="ti ti-trash"></i> Delete</a>
                                            <a href="javascript:void(0)" class="btn btn-sm btn-outline-primary rounded-1" onclick="openEdit({{$project->id}})"><i class="ti ti-edit"></i> Edit</a>
                                            <a href="{{route('admin.microsite.analytics',$project->id)}}" class="btn btn-sm btn-success rounded-1"><i class="ti ti-report-analytics"></i> View Analytic</a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <nav class="mt-5">
                        {{ $projects->links() }}
                    </nav>
                </div>
            </div>
        </div>

    </div>
</div>
<!-- [ Main Content ] end -->
@endsection

@section('custom-js')
<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
<script>
    $(document).on('click', ' .add-button', function() {
        let code = `
            <div class="mb-3 d-flex gap-3 align-items-end justify-content-between">
                <div class="w-100">
                    <label for="">Caption</label>
                    <input type="text" name="button_caption[]" class="form-control" placeholder="button caption">
                </div>
                <div class="w-100">
                    <label for="">Button Link</label>
                    <input type="text" name="button_link[]" class="form-control" placeholder="button caption">
                </div>
                <button type="button" class="btn btn-outline-danger rounded-1 mb-1 remove-button">&times;</button>
            </div>
        `;
        $('#buttons-form').append(code)
    })

    $(document).on('click', '.remove-button', function() {
        $(this).closest('div').remove();
    })
</script>
<script>
    $(document).on('click', ' .add-popunder', function() {
        let code = `
        <div class="mb-3 d-flex gap-3 align-items-end justify-content-between">
                <div class="w-100">
                    <label for="">Link</label>
                    <input type="text" name="popunder_link_new[]" class="form-control" placeholder="Link">
                </div>
                <div class="w-100">
                    <label for="">Percentage</label>
                    <input type="text" name="percentage_new[]" maxlength="3" class="form-control percentage-input" placeholder="percentage (data adjust or 0)" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                </div>
                <button type="button" class="btn btn-outline-danger rounded-1 mb-1 remove-popunder">&times;</button>
            </div>
        `;
        $('#popunder-form').append(code)
    })

    $(document).on('click', '.remove-popunder', function() {
        $(this).closest('div').remove();
    })
</script>

<script>
    function searchForm() {
        $('#searchForm').submit();
    }

</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const image = document.getElementById('edit_images');
        const preview = document.getElementById('preview');
        var cancelButton = document.getElementById('buttonCancel');

        if (image) {
            image.onchange = function (evt) {
                const file = evt.target.files[0];
                if (file) {
                    preview.style.display = 'block';
                    preview.src = URL.createObjectURL(file);
                    cancelButton.style.display = 'block';
                } else {
                    preview.style.display = 'none';
                    cancelButton.style.display = 'none';
                }
            };
        } else {
            console.error('Error: Elemen Image atau Preview tidak ditemukan.');
        }
    });
</script>
<script>
    function removeData(id) {
        Swal.fire({
            title: 'Apakah anda yakin?'
            , text: "Link akan dihapus secara permanen!"
            , icon: 'warning'
            , showCancelButton: true
            , confirmButtonText: 'Ya, hapus link!'
            , cancelButtonText: 'Batalkan'
            , customClass: {
                confirmButton: 'btn btn-link me-5'
                , cancelButton: 'btn btn-primary ms-5'
            }
            , buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                axios.delete(`{{ route('microsite-destroy') }}`, {
                        params: {
                            'dataID': id
                        }
                    })  
                    .then(res => {
                        console.log('Respon dari server:', res);

                        if (res.status === 200) {
                            Swal.fire({
                                    icon: 'success'
                                    , title: 'Ok'
                                    , text: "Data berhasil dihapus"
                                })
                                .then(() => {
                                    console.log('Me-refresh halaman...');
                                    window.location.reload();
                                });
                        } else {
                            Swal.fire({
                                icon: 'error'
                                , title: 'Oops...'
                                , text: "Terjadi kesalahan saat menghapus data: "
                            }).then(() => {
                                console.log('Me-refresh halaman...');
                                window.location.reload();
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Terjadi kesalahan saat menghapus data:', error);
                        Swal.fire({
                            icon: 'error'
                            , title: 'Oops...'
                            , text: "Terjadi kesalahan saat menghapus data"
                        }).then(() => {
                            console.log('Me-refresh halaman...');
                            window.location.reload();
                        });
                    });
            }
        });
    }
</script>
<script>
    function cancel(event) {
        var fileInput = document.getElementById('edit_images');
        const preview = document.getElementById('preview');
        var cancelButton = document.getElementById('buttonCancel');


        cancelButton.style.display = 'none';
        preview.style.display = 'none';
        fileInput.value = '';
    }

    function addParamLink(e) {
        var resultLink = $(e).val();
        $('.alt-domain').each(function(index, altDomain) {
            $(altDomain).val($(altDomain).data('domain') + '/' + resultLink);
        })
    }

    function copy(event, prefix = null) {
        var input = $(event.target).closest('.copy-wrapper').find('input');
        var text = prefix ? prefix + input.val() : input.val();
        navigator.clipboard.writeText(text)
    }

    let reloadAfterEdit = false;

    function reloadIt() {
        const preview = document.getElementById('preview');
        var fileInput = document.getElementById('edit_images');
        var cancelButton = document.getElementById('buttonCancel');

        cancelButton.style.display = 'none';
        preview.style.display = 'none';
        fileInput.value = '';
    }

    function openEdit(dataID) {
        $('#modalEdit').modal('show')
        let url = `{{ route('ajax.getDataById', ['id'=>'dataID']) }}`
        url = url.replace('dataID', dataID)
        axios.get(url)
            .then(res => {
                console.log(res);
                var imageUrl = res.data.gambar;
                var buttons = res.data.button;
                var popunders = res.data.popunders;
                var urlParts = imageUrl.split('/');
                var baseUrl = urlParts.slice(0, 5).join('/');
                console.log(imageUrl);
                console.log(baseUrl);
                $('#edit_data_id').val(res.data.id);
                $('#edit_microsite_name').val(res.data.title);
                $('#edit_description').text(res.data.description);
                if(imageUrl === baseUrl){
                    $('#image').css('display', 'none');
                    $('#deleteFoto').css('display', 'none');
                }else{
                    $('#image').css('display', 'block');
                    $('#deleteFoto').css('display', 'block');
                }
                $('#image').attr('src', imageUrl);
                $('.button-fields').empty();

                if (buttons.length > 0) {
                    buttons.forEach(button => {
                        var inputTombol = `
                            <div id="old-${button.id}">
                                <div class="mb-3 d-flex gap-3 align-items-end justify-content-between">
                                    <div class="w-100">
                                        <label for="">Caption</label>
                                        <input type="text" name="button_title[]" class="form-control rounded-0 alt-button-title" required value="${button.title}" placeholder="Button Caption">
                                    </div>
                                    <div class="w-100">
                                        <label for="">Button Link</label>
                                        <input type="text" name="button_url[]" class="form-control rounded-0 alt-button-url" required value="${button.url}" placeholder="https://">
                                    </div>
                                    <input type="hidden" name="" id="button_id" class="form-control rounded-0 alt-button-url" required value="${button.id}">
                                    <a href="#" id="" class="btn btn-outline-danger rounded-1 mb-1" onclick="deleteButton(${button.id})">&times;</a>
                                </div>
                            </div>`;
                        $('.button-fields').append(inputTombol);
                    });
                } else {
                    $('.button-fields').html('<p>No buttons found.</p>');
                }

                if (popunders.length > 0) {
                    popunders.forEach(popunder => {
                        var inputPopUnder = `
                            <div id="old-popunder-${popunder.id}">
                                <div class="mb-3 d-flex gap-3 align-items-end justify-content-between">
                                    <div class="w-100">
                                        <label for="">Url</label>
                                        <input type="text" name="popunder_link[]" class="form-control rounded-0 alt-button-title" required value="${popunder.url}" placeholder="Link">
                                    </div>
                                    <div class="w-100">
                                        <label for="">Percentage</label>
                                        <input type="text" value= "${popunder.percentage}" required name="percentage[]" maxlength="3" class="form-control percentage-input" placeholder="percentage" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                    </div>
                                    <input type="hidden" name="id_popunder" id="popunder_id" class="form-control rounded-0 alt-button-url" required value="${popunder.id}">
                                    <a href="#" id="" class="btn btn-outline-danger rounded-1 mb-1" onclick="deletePopunder(${popunder.id})">&times;</a>
                                </div>
                            </div>`;
                        $('.popunder-fields').append(inputPopUnder);
                    });
                } else {
                    $('.popunder-fields').html('<p>No Pop Under found.</p>');
                }
                $('#edit_short_link').val(res.data.short_url);
                $('.alt-domain').each(function(index, altDomain) {
                    $(altDomain).val($(altDomain).data('domain') + '/' + res.data.short_url);
                })
            })
            .catch(err => {
                console.error('terjadi kesalahan', err)
            })
    }

    function deleteProfilePhoto() {
    const profileId = $('#edit_data_id').val();
    var fileInput = document.getElementById('edit_images');
    Swal.fire({
        title: 'Apakah anda yakin?',
        text: 'Image akan dihapus secara permanen!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus Image!',
        cancelButtonText: 'Batalkan',
        customClass: {
            confirmButton: 'btn btn-link me-5',
            cancelButton: 'btn btn-primary ms-5',
        },
        buttonsStyling: false,
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: ('{{ route("delete.profile.photo", ":id") }}'.replace(':id', profileId)),
                type: 'POST',
                data: {
                    _method: 'delete',
                    _token: $('meta[name="csrf-token"]').attr('content'),
                }
            }).then((res) => {
                console.log('Respon dari server:', res);

                if (res.success === "Gambar berhasil dihapus") {
                    Swal.fire({
                        icon: 'success',
                        title: 'Ok',
                        text: 'Image berhasil dihapus',
                    }).then(() => {
                        console.log('Me-refresh halaman...');
                        if (fileInput && fileInput.value) {
                            $('#buttonCancel').css('display', 'block');
                         }else{
                            $('#buttonCancel').css('display', 'none');
                         }
                        $('#deleteFoto').css('display', 'none');
                        $('#image').css('display', 'none');
                        $('#image').attr('src', '');
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Terjadi kesalahan saat menghapus Image',
                    }).then(() => {
                        console.log('Me-refresh halaman...');
                    });
                }
            });
        }
    });
    event.preventDefault();
}
    function deleteButton(buttonId) {
    console.log(buttonId);
    Swal.fire({
        title: 'Apakah anda yakin?',
        text: 'Button akan dihapus secara permanen!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus Button!',
        cancelButtonText: 'Batalkan',
        customClass: {
            confirmButton: 'btn btn-link me-5',
            cancelButton: 'btn btn-primary ms-5',
        },
        buttonsStyling: false,
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: ('{{ route("deleteButton", ":id") }}'.replace(':id', buttonId)),
                type: 'POST',
                data: {
                    _method: 'delete',
                    _token: $('meta[name="csrf-token"]').attr('content'),
                }
            }).then((res) => {
                console.log('Respon dari server:', res);

                if (res.status === '200') {
                    Swal.fire({
                        icon: 'success'
                        , title: 'Ok'
                        , text: "Data berhasil dihapus"
                    })
                    .then(() => {
                        console.log('Me-refresh halaman...');
                        $('#old-' + res.id).hide();

                        // window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error'
                        , title: 'Oops...'
                        , text: "Terjadi kesalahan saat menghapus data: "
                    }).then(() => {
                        console.log('Me-refresh halaman...');
                        // window.location.reload();
                    });
                }
            });
        }
    });
    event.preventDefault();
    }

    function deletePopunder(popunderId) {
    console.log(popunderId);
    Swal.fire({
        title: 'Apakah anda yakin?',
        text: 'Button akan dihapus secara permanen!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus Pop Under!',
        cancelButtonText: 'Batalkan',
        customClass: {
            confirmButton: 'btn btn-link me-5',
            cancelButton: 'btn btn-primary ms-5',
        },
        buttonsStyling: false,
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: ('{{ route("deletePopunderId", ":id") }}'.replace(':id', popunderId)),
                type: 'POST',
                data: {
                    _method: 'delete',
                    _token: $('meta[name="csrf-token"]').attr('content'),
                }
            }).then((res) => {
                console.log('Respon dari server:', res);

                if (res.status === '200') {
                    Swal.fire({
                        icon: 'success'
                        , title: 'Ok'
                        , text: "Data berhasil dihapus"
                    })
                    .then(() => {
                        console.log('Me-refresh halaman...');
                        $('#old-popunder-' + res.id).hide();
                        $('#old-popunder-' + res.id).remove();
                        recalculateTotalPercentage();
                        // window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error'
                        , title: 'Oops...'
                        , text: "Terjadi kesalahan saat menghapus data: "
                    }).then(() => {
                        console.log('Me-refresh halaman...');
                        // window.location.reload();
                    });
                }
            });
        }
    });
    event.preventDefault();
    }

    function recalculateTotalPercentage() {
    var totalPercentage = 0;
    $('.percentage-input').each(function() {
        var value = parseInt($(this).val());
        if (!isNaN(value) && value >= 0) {
            totalPercentage += value;
        }
    });
    console.log('Total persentase setelah penghapusan:', totalPercentage);
    }

    $('#formEdit').submit(function(e) {
        e.preventDefault();
        var popUnderAdded = false;
        var totalPercentage = 0;

        $('.percentage-input').each(function() {
            var value = parseInt($(this).val());
            if (!isNaN(value) && value >= 0) {
                totalPercentage += value;
                if ($(this).val() !== "") {
                    popUnderAdded = true;
                }
            }
        });

        if (!popUnderAdded) {
            sendFormData();
            return;
        }

        console.log(totalPercentage);
        if (totalPercentage === 100) {
            sendFormData();
        } else if (totalPercentage > 100) {
            var notyf = new Notyf({
                position: {
                    x: 'right'
                    , y: 'top'
                , }
            });
            notyf.error('Total persentase tidak boleh melebihi 100');
        } else {
            var notyf = new Notyf({
                position: {
                    x: 'right'
                    , y: 'top'
                , }
            });
            if ($('.percentage-input').length === 1) {
                notyf.error('Persentase harus 100');
            } else {
                notyf.error('Total persentase harus 100');
            }
        }
    });

    function sendFormData() {
    $('.custom-preload').show().addClass('d-flex');
    let dataToPatch = new FormData($('#formEdit')[0]); // Menggunakan $('#formEdit')[0] untuk merujuk ke elemen formulir
    let selectedFile = $('#edit_images')[0].files[0];
    let idPopunders = [];
    for (let entry of dataToPatch.entries()) {
        if (entry[0].includes('id_popunder')) {
            idPopunders.push(entry[1]);
        }
    }
    let url = `{{ route('ajax.updateData') }}`
    axios.post(url, dataToPatch, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        })
        .then(res => {
            console.log(res)
            if(res.data.status === "success"){
                $('.custom-preload').hide().removeClass('d-flex');
                $('#modalEdit').modal('hide');
                $('#preview').css('display', 'none');
                $('#buttonCancel').css('display', 'none');
                $('#buttons-form').empty();
                $('#popunder-form').empty();
                idPopunders.forEach(id => {
                    $('#old-popunder-' + id).hide();
                    $('#old-popunder-' + id).remove();
                });
                $('#formEdit')[0].reset();
                var notyf = new Notyf({
                    position: {
                        x: 'right'
                        , y: 'top'
                    , }
                });
                notyf.success('Microsite updated successfully.');
                reloadAfterEdit = true
            }
        })
        .catch(err => {
            console.log(err)
            console.error('Terjadi kesalahan', err.message)
            $('.custom-preload').hide().removeClass('d-flex');
            $('#modalEdit').modal('hide');
            $('#buttonCancel').css('display', 'none');
            $('#preview').css('display', 'none');
            $('#formEdit')[0].reset(); // Menggunakan $('#formEdit')[0].reset() untuk mereset formulir
            var notyf = new Notyf({
                position: {
                    x: 'right'
                    , y: 'top'
                , }
            });
            notyf.error(err.response.data.message);
            reloadAfterEdit = false
        })
    }
</script>
@endsection
