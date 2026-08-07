@extends('admin.components.main')

@section('page.title', 'Domain Decentralized')

@section('custom-css')
<link rel="stylesheet" href="{{ asset('assets') }}/css/plugins/notifier.css">
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
                            <li class="breadcrumb-item"><a
                                    href="https://ableproadmin.com/navigation/index.html">Home</a></li>
                            <li class="breadcrumb-item" aria-current="page">Domain Decentralized</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h2 class="mb-0">Domain Decentralized</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->


        <!-- [ Main Content ] start -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-4 pt-4">
                        <button type="button" class="btn btn-primary btn-lg rounded-2" data-pc-animate="blur"
                            data-bs-toggle="modal" data-bs-target="#animateModal"><i class="fa fa-plus"></i> Add
                            New Domain</button>

                        <div class="modal fade modal-animate" id="animateModal" data-bs-backdrop="static"
                            data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                            aria-hidden="true">
                            <div class=" modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Add New Domain</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close">
                                        </button>
                                    </div>
                                    <form action="{{ route('admin.domain_decentralized.store') }}" method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label for="">Domain URL</label>
                                                <input type="url" class="form-control" name="domain"
                                                    placeholder="https://yourdomain.com" required>
                                            </div>

                                            <div class="form-group d-flex gap-2 align-items-start">
                                                <input type="checkbox" name="status" class="form-check-input"
                                                    id="status" value="enable">
                                                <div>
                                                    <label for="status">Enable this domain?</label><br>
                                                    <small class="text-muted">Check this for active domain
                                                        redirect</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary shadow-2">Save Data</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">

                        <div class="row">
                            @foreach($domains as $domain)
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <h5 class="mb-0"><a href="{{ $domain->domain_url }}" target="new">{{
                                                        $domain->domain_url }}</a>
                                                </h5>
                                                <div class="d-flex gap-3 mt-2 align-items-center">
                                                    <div class="status">
                                                        @if( $domain->status == "enable" )
                                                        <small class="text-success"><i class="fa fa-check"></i>
                                                            Enabled</small>
                                                        @else
                                                        <small class="text-danger"><i class="fa fa-ban"></i>
                                                            Disabled</small>
                                                        @endif
                                                    </div>
                                                    <small>Created: {{ Carbon::parse($domain->created_at)->format('d M
                                                        Y') }}</small>
                                                </div>
                                            </div>
                                            <div class="dropdown">
                                                <a class="avtar avtar-s btn-link-secondary dropdown-toggle arrow-none"
                                                    href="#" data-bs-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">
                                                    <i class="ti ti-dots f-18"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a class="dropdown-item d-flex gap-2 align-items-center bg-gray-300"
                                                        href="javascript:void(0)"
                                                        onclick="switchStatus(this, {{ $domain->id }})">
                                                        <i class="fa fa-exchange-alt"></i> <span>Enable / Disable
                                                            Domain</span>
                                                    </a>
                                                    <a class="dropdown-item d-flex gap-2 align-items-center"
                                                        href="javascript:void(0)" onclick="openEdit({{ $domain->id }})">
                                                        <i class="fa fa-edit"></i> <span>Edit</span>
                                                    </a>
                                                    <a class="dropdown-item d-flex gap-2 align-items-center text-danger"
                                                        href="javascript:void(0)"
                                                        onclick="deleteDomain({{ $domain->id }})">
                                                        <i class="fa fa-trash"></i> <span>Delete</span>
                                                    </a>
                                                </div>
                                            </div>


                                            {{-- Modal Edit --}}
                                            <div class="modal fade modal-animate" id="modalEdit"
                                                data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                                                aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                                <div class=" modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Update Domain</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close">
                                                            </button>
                                                        </div>
                                                        <form action="{{ route('admin.domain_decentralized.update') }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" id="edit-id" name="id" value="">
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label for="">Domain URL</label>
                                                                    <input type="url" class="form-control"
                                                                        id="edit-domain" name="domain"
                                                                        placeholder="https://yourdomain.com" required>
                                                                </div>

                                                                <div class="form-group d-flex gap-2 align-items-start">
                                                                    <input type="checkbox" name="status"
                                                                        class="form-check-input" id="edit-status"
                                                                        value="enable">
                                                                    <div>
                                                                        <label for="edit-status">Enable this
                                                                            domain?</label><br>
                                                                        <small class="text-muted">Check this for active
                                                                            domain
                                                                            redirect</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-outline-secondary"
                                                                    data-bs-dismiss="modal">Close</button>
                                                                <button type="submit"
                                                                    class="btn btn-info shadow-2">Update Data</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                API Key :
                                                <div class="d-flex align-items-center justify-content-between border">
                                                    <pre
                                                        class="px-3 m-0 fs-5"><code class="html xml" id="api_key_{{ $domain->id }}">{{ $domain->api_key }}</code></pre>
                                                    <button type="button" data-clipboard-text="Hello"
                                                        class="btn btn-sm btn-outline-secondary rounded-0 btn-copy"><i
                                                            class="fa fa-copy"
                                                            data-clipboard-target="#api_key_{{ $domain->id }}"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/1.4.0/clipboard.min.js"
    integrity="sha512-iJh0F10blr9SC3d0Ow1ZKHi9kt12NYa+ISlmCdlCdNZzFwjH1JppRTeAnypvUez01HroZhAmP4ro4AvZ/rG0UQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="{{ asset('assets') }}/js/plugins/notifier.js"></script>
<script src="{{ asset('assets') }}/js/plugins/sweetalert2.all.min.js"></script>

<script>
    $('.btn-copy').click(function(){
        var copyText = $(this).closest('div').find('code');

        // Select the text field
        copyText.select();

        // Copy the text inside the text field
        navigator.clipboard.writeText(copyText.text());
        notifier.show('Copied!', 'API Key copied.', 'info', '', 2000);
    })
</script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.6.7/axios.min.js"
    integrity="sha512-NQfB/bDaB8kaSXF8E77JjhHG5PM6XVRxvHzkZiwl3ddWCEPBa23T76MuWSwAJdMGJnmQqM0VeY9kFszsrBEFrQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    function switchStatus(e, dataID){
        dataToPatch = {
            'dataID': dataID
        }
        axios.patch(`{{ route('admin.domain_decentralized.switchStatus') }}`, dataToPatch)
        .then( res => {
            if( res.data.set_status == "enable" ){
                $(e).closest('.card-body').find('.status').html(`<small class="text-success"><i class="fa fa-check"></i> Enabled</small>`);
            }else{
                $(e).closest('.card-body').find('.status').html(`<small class="text-danger"><i class="fa fa-ban"></i> Disabled</small>`);
            }
        })
        .catch( err => {
            console.error('Terjadi kesalahan', err.response.data)
        })
    }

    function openEdit(dataID){
        $('#modalEdit').modal('show')
        let url = `{{ route('admin.domain_decentralized.detail', ['id'=>'dataID']) }}`
        url = url.replace('dataID', dataID)
        axios.get(url)
        .then(res => {
            $('#edit-id').val(dataID)
            $('#edit-domain').val(res.data.domain)
            $('#edit-status').prop('checked', res.data.status=='enable' ? true : false)
        })
        .catch(err => {
            console.error('terjadi kesalahan', err)
        })
    }


    function deleteDomain(dataID){
        Swal.fire({
        title: "Are you sure?",
        text: "Data will be delete permanently!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#FFFFFF",
        confirmButtonText: "Yes, delete it!",
        customClass: {
            confirmButton: 'btn-danger',
            cancelButton: 'btn btn-outline-primary'
        }
        }).then((result) => {
        if (result.isConfirmed) {
            let data = {
                dataID: dataID
            }
            axios.delete(`{{ route('admin.domain_decentralized.delete') }}`, { data: data })
            .then(res => {
                Swal.fire({
                    title: "Deleted!",
                    text: "Your file has been deleted.",
                    icon: "success"
                })
                .then((result) => {
                    window.location.reload();
                });
            })
            .catch(err => {
                console.error('terjadi kesalahan', err)
            })
        }
        });
    }
</script>
@endsection
