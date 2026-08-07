@extends('admin.components.main')

@section('main-content')
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
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h2 class="mb-0">Shorted Link</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->

        <!-- [ Main Content ] start -->
        <div class="row">
            <!-- [ sample-page ] start -->
            @foreach ($data as $item)
            <div class="col-sm-12">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between pb-4 border-bottom mb-4">
                            <div>
                                {{-- <h4 class="mb-0">{{ ENV('APP_URL') }}/{{ $item->short_url }}</h4> --}}
                                <h4 class="mb-0">{{ $item->campaign_name ?? 'Untitled' }}</h4>
                                @if (strlen($item->original_url) > 63)
                                <small class="card-subtitle">Original URL: <a href="{{$item->original_url}}">{{ substr($item->original_url, 0, 60) }}...</a></small>
                                @else
                                <small class="card-subtitle">Original URL: <a href="{{$item->original_url}}">{{$item->original_url}}</a></small>
                                @endif
                            </div>
                            <div class="dropdown">
                                <a class="avtar avtar-s btn-link-secondary dropdown-toggle arrow-none" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="ti ti-dots f-18"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item text-primary" type="button" onclick="openEditModal(this)" data-item-id="{{ $item->id }}"><i class="ti ti-edit"></i> Update </a>
                                    <a type="button" class="dropdown-item text-danger" onclick="removeData({{$item->id}})"><i class="ti ti-trash"></i>Hapus</a>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 justify-content-between align-items-center">
                            <span class="card-text" style="font-size: 12px">Shortened on: <strong>{{$item->created_at->format('d')}} {{$item->created_at->format('F')}} {{$item->created_at->format('Y')}} {{$item->created_at->format('H:i')}}</strong></span>
                            <a href="{{ route('view.shortedLink-statistic', ["id" => $item->id])}}" type="button" class="btn btn-sm btn-primary p-2">
                                <span class="pc-micon">
                                    <?xml version="1.0" ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" class="w-4 h-5 -mt-0.5">
                                        <path d="M18.375 2.25c-1.035 0-1.875.84-1.875 1.875v15.75c0 1.035.84 1.875 1.875 1.875h.75c1.035 0 1.875-.84 1.875-1.875V4.125c0-1.036-.84-1.875-1.875-1.875h-.75zM9.75 8.625c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-.75a1.875 1.875 0 01-1.875-1.875V8.625zM3 13.125c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v6.75c0 1.035-.84 1.875-1.875 1.875h-.75A1.875 1.875 0 013 19.875v-6.75z"></path>
                                    </svg>
                                    </svg>
                                    <span class="text-lg-start align-text-top">Statistics</span>
                                </span>
                            </a>
                        </div>
                        <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modelNewShortLinkLabel"> Edit Short Link </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="" id="campaign-form" method="POST">
                                            @csrf
                                            <div class="form-group">
                                                <label for="">Campaign Name *</label>
                                                <input type="text" name="campaign_name" id="campaign_name" class="form-control" required placeholder="" value="">
                                            </div>
                                            <div class="form-group">
                                                <label for="">Description (Optional)</label>
                                                <textarea name="description" id="description" class="form-control" rows="3" placeholder=""></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label for="">Tags *</label>
                                                <input type="text" name="tags" id="choices-text-remove-button" class="form-control" required placeholder="" value="">
                                            </div>
                                            <div class="form-group">
                                                <label for="">Original URL *</label>
                                                <input type="url" name="original_url" id="original_url" class="form-control" required placeholder="" value="">
                                            </div>
                                            <input type="hidden" name="id" id="id" value="">
                                            <button type="submit" id="submit" class="btn btn-lg btn-primary w-100 rounded-1 mt-4">Update</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Additional content or actions can be placed here -->
                    </div>
                </div>
            </div>
            @endforeach
            <!-- [ sample-page ] end -->
        </div>
        <!-- [ Main Content ] end -->
    </div>
</div>
@endsection

@section('custom-js')
<script src="{{ asset('assets') }}/js/plugins/choices.min.js"></script>
<script>
    $('#campaign-form').submit(function(e) {
        e.preventDefault();
        var campaignName = $('[name="campaign_name"]').val();
        var description = $('[name="description"]').val();
        var tags = $('[name="tags"]').val();
        var originalUrl = $('[name="original_url"]').val();
        var id = $('input[name="id"]').val();
        var url = `{{ route('view.update', ["id" => ":id"]) }}`
        url = url.replace(':id', id)
        $.ajax({
            url: url
            , type: 'POST'
            , data: {
                _token: '{{ csrf_token() }}'
                , campaign_name: campaignName
                , description: description
                , tags: tags
                , original_url: originalUrl
            }
            , success: function(data) {
                console.log(data);
                if (data.status === 'success') {
                    // Tampilkan SweetAlert untuk keberhasilan
                    Swal.fire({
                        icon: 'success'
                        , title: 'Berhasil'
                        , text: 'Short link berhasil diupdate!'
                    , }).then(() => {
                        console.log('Me-refresh halaman...');
                        window.location.reload();
                    });
                } else if (data && data.status === 'error') {
                    // Tampilkan SweetAlert untuk kesalahan khusus (nama kampanye sudah digunakan)
                    Swal.fire({
                        icon: 'error'
                        , title: 'Oops...'
                        , text: data.message
                    , });
                } else {
                    // Tampilkan SweetAlert untuk kesalahan umum
                    Swal.fire({
                        icon: 'error'
                        , title: 'Oops...'
                        , text: 'Short link gagal diupdate!'
                    , }).then(() => {
                        console.log('Me-refresh halaman...');
                        window.location.reload();
                    });
                }
            }
            , error: function() {
                Swal.fire({
                    icon: 'error'
                    , title: 'Oops...'
                    , text: 'Gagal mengirim data.'
                , });
            }
        });
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
                axios.delete(`{{ route('view.shortedLink-destroy') }}`, {
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
    function openEditModal(clickedElement) {
        var itemId = clickedElement.getAttribute('data-item-id');
        let url = `{{ route('view.get-dataById', ['id'=>':id']) }}`
        url = url.replace(':id', itemId)
        $.ajax({
            url: url
            , method: 'GET'
            , headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
            , success: function(data) {
                console.log('Data:', data);

                $('#campaign_name').val(data.campaign_name);
                $('#description').val(data.description);
                $('#original_url').val(data.original_url);
                $('#id').val(itemId);


                // Menetapkan nilai untuk elemen dengan ID choices-text-remove-button
                if (data.labels.length > 0) {
                    // Mengambil semua label dari array labels
                    var allLabels = data.labels.map(function(label) {
                        return label.label_name;
                    });

                    // Menetapkan nilai elemen dengan ID choices-text-remove-button
                    $('#choices-text-remove-button').val(allLabels.join(','));

                    // Inisialisasi Choices setelah elemen diisi
                    var textRemove = new Choices(document.getElementById('choices-text-remove-button'), {
                        delimiter: ','
                        , editItems: true
                        , maxItemCount: 5
                        , removeItemButton: true
                    });
                }


                var modal = new bootstrap.Modal(document.getElementById('staticBackdrop'));
                modal.show();
            }
            , error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
            }
        });
    }

</script>

@endsection
