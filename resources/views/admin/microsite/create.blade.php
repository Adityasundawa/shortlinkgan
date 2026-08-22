@extends('admin.components.main')

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
                            <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0)">Microsite</a></li>
                            <li class="breadcrumb-item" aria-current="page">Create</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h2 class="mb-0">Dashboard Create Microsites</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3>Create new Microsite</h3>
                        <p class="mb-0">Please fill the field below to complete installation of your
                            new Microsite.</p>
                    </div>
                    <div class="card-body">
                        <div class="row d-flex flex-wrap justify-content-between">
                            <div class="col-md-5">
                                <form action="" method="POST" id="addForm" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-12">
                                            <h4>Microsite Content</h4>
                                        </div>
                                    </div>
                                    <div class="row d-flex mb-3">
                                        <div class="col-md-3">
                                            <strong class="mt-3 d-block">Project Title</strong>
                                        </div>
                                        <div class="col-md-9"><input type="text" name="title" class="form-control" required=""></div>
                                    </div>
                                    <div class="row d-flex mb-3">
                                        <div class="col-md-3">
                                            <strong class="mt-3 d-block">Description</strong>
                                        </div>
                                        <div class="col-md-9">
                                            <textarea name="description" id="" cols="30" rows="5" class="form-control"></textarea>
                                        </div>
                                    </div>

                                    {{-- <div class="row d-flex mb-3">
                                        <div class="col-md-3">
                                            <strong class="mt-3 d-block">Pop Under (optional)</strong>
                                        </div>
                                        <div class="col-md-9"><input type="text" name="popunder" class="form-control"></div>
                                    </div> --}}

                                    <div class="row d-flex mb-3">
                                        <div class="col-md-3">
                                            <strong class="mt-3 d-block">Image Profile</strong>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="form-file mb-3">
                                                <input type="file" name="photo" class="form-control" required="">
                                            </div>
                                        </div>
                                    </div>


                                    <div class="row d-flex mb-3">
                                        <div class="col-md-3">
                                            <strong class="mt-3 d-block">Image Background</strong>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="form-file mb-3">
                                                <input type="file" name="background" class="form-control" required="">
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="use_play_button"
                                                    name="use_play_button" value="1" checked>
                                                <label class="form-check-label" for="use_play_button">Use Play Button
                                                    Icon on Background</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-5 mb-4">
                                        <div class="col-12">
                                            <h4>Button Links</h4>

                                            <div id="buttons-form">
                                            </div>


                                            <div class="text-center">
                                                <button type="button" class="btn btn-success rounded-1 add-button"><i class="ti ti-plus"></i> Add Link</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-5 mb-4">
                                        <div class="col-12">
                                            <h4>Pop Under (opsional)</h4>

                                            <div id="pop-under-form">
                                            </div>


                                            <div class="text-center">
                                                <button type="button" class="btn btn-success rounded-1 add-pop-under"><i class="ti ti-plus"></i> Add Pop Under</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary btn-lg w-100 rounded-2"><i class="ti ti-save"></i> Save Microsite</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="col-md-4">
                                Preview
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('admin.microsite') }}" class="btn btn-secondary rounded-1"><i class="ti ti-arrow-left"></i> back</a>
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>
<!-- [ Main Content ] end -->
@endsection

@section('custom-js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.6.7/axios.min.js" integrity="sha512-NQfB/bDaB8kaSXF8E77JjhHG5PM6XVRxvHzkZiwl3ddWCEPBa23T76MuWSwAJdMGJnmQqM0VeY9kFszsrBEFrQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>



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
    $(document).on('click', ' .add-pop-under', function() {
        let code = `
            <div class="mb-3 d-flex gap-3 align-items-end justify-content-between">
                <div class="w-100">
                    <label for="">Link</label>
                    <input type="text" name="popunder_link[]" class="form-control" placeholder="Link">
                </div>
                <div class="w-100">
                    <label for="">Percentage</label>
                    <input type="text" name="percentage[]" maxlength="3" class="form-control percentage-input" placeholder="percentage" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                </div>
                <button type="button" class="btn btn-outline-danger rounded-1 mb-1 remove-pop-under">&times;</button>
            </div>
        `;
        $('#pop-under-form').append(code)
    })

    $(document).on('click', '.remove-pop-under', function() {
        $(this).closest('div').remove();
    })
</script>

<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
<script>
    $('#addForm').submit(function(e) {
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
        let dataForm = new FormData($('#addForm')[0]);
        axios.post(`{{ route('admin.microsite.create') }}`, dataForm, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })
            .then(res => {
                var notyf = new Notyf({
                    position: {
                        x: 'right'
                        , y: 'top'
                    , }
                });
                notyf.success('Microsite berhasil dibuat.');
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            })
            .catch(err => {
                console.error('Terjadi kesalahan', err.response)
                if (err.response.data.status === "error") {
                    var notyf = new Notyf({
                        position: {
                            x: 'right'
                            , y: 'top'
                        , }
                    });
                    if (err.response.data.message === "The file size should not exceed 1 MB.") {
                        notyf.error("The file size should not exceed 1 MB.");
                    } else if (err.response.data.message.photo[0] === "The photo failed to upload.") {
                        notyf.error("The file size should not exceed 1 MB.");
                    } else {
                        notyf.error("Ops! Terjadi Kesalahan");
                    }
                }
            });
    }
</script>
@endsection
