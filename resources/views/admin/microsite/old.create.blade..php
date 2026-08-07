@extends('admin.components.main')

@section('custom-css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
<style>
    .card-footer-custom {
        background-color: var(--bs-card-cap-bg);
        border-top: var(--bs-card-border-width) solid var(--bs-card-border-color);
        height: 50px;
        line-height: 50px;
    }

    .themes {
        border: none;
        padding: 0;
        background: none;
        cursor: pointer;
    }

    .theme-button {
        width: 200px
    }

    .card.theme-button.selected {
        border: 2px solid red;
    }

    .card-body {
        background-color: #e5e7eb;
    }

</style>
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
                            <h2 class="mb-0">Dashboard Create Microsite</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->

        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3>Create new Microsite</h3>
                        <p class="mt-2 dark:text-gray-300">Please fill the field below to complete installation of your
                            new Microsite.</p>
                    </div>

                    <div class="card-body" id="select-theme">
                        <h5>Select Template & Theme</h5>
                        <div class="row">
                            @foreach ($themes as $item)
                            <div class="col-lg-4">
                                <button id="theme" data-value="{{ $item['id'] }}" class="themes">
                                    <div class="card theme-button" id="1">
                                        <img src="https://cdn-sdotid.adg.id/images/../assets/microsite-template-preview.svg" class="card-img-top" alt="Image">
                                        <div class="card-footer-custom text-center">
                                            {{ $item['name'] }}
                                        </div>
                                    </div>
                                </button>
                            </div>
                            @endforeach
                        </div>
                        <div class="row">
                            <div class="d-flex justify-content-end">

                                <button id="themes-button" disabled class="btn btn-primary d-inline-flex">Continue
                                    &nbsp; <i class="ti ti-arrow-narrow-right me-1"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" id="select-name" style="display: none">
                        <div class="row">
                            <div class="form-group">
                                <label class="form-label" for="micrositeName">Microsite Title</label>
                                <input type="text" class="form-control" id="micrositeName" name="statistic_name" aria-describedby="MictositeHelp" placeholder="Cool My Resume">
                            </div>
                            <div class="form-group">
                                <label for="">Description</label>
                                <textarea name="" id="micrositeDescription" cols="30" rows="3" class="form-control" placeholder="Microsite Description"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="d-flex justify-content-end">
                                <button id="back-select-name" class="btn btn-secondary d-inline-flex"><i class="ti ti-arrow-narrow-left me-1"></i> Kembali</button> &nbsp;
                                <button id="finish" type="submit" class="btn btn-success d-inline-flex">Finish&nbsp;<i class="ti ti-check me-1"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <img src="" id="preview" class="img-fluid" alt="">
            </div>
        </div>


    </div>
</div>
<!-- [ Main Content ] end -->
@endsection

@section('custom-js')
<script>
    $(document).ready(function() {
        $('.themes').click(function() {
            $('.card.theme-button').removeClass('selected');
            $(this).find('.card.theme-button').addClass('selected');
            $('button[disabled]').removeAttr('disabled');
            var dataValue = $(this).attr('data-value');
            $.ajax({
                url: `{{ route('ajax.get-preview') }}`
                , type: 'POST'
                , dataType: 'json'
                , data: {
                    _token: '{{ csrf_token() }}'
                    , dataValue: dataValue,

                }
                , success: function(response) {
                    console.log('AJAX success:', response.data.preview);
                    if (response.result === 'success') {
                        $('#preview').attr('src', response.data.preview);
                    } else {
                        console.error('TemplateMicrosite not found');
                    }
                }
                , error: function(xhr, status, error) {
                    console.error('AJAX error:', status, error);
                }
            });
            console.log('data-value:', dataValue);
        });
        $('#themes-button').click(function() {
            $('#select-theme').hide();
            $('#select-name').show();
        });
        $('#back-select-name').click(function() {
            $('#select-theme').show();
            $('#select-name').hide();
        });

        $('#finish').on('click', function(e) {
            e.preventDefault();
            var micrositeName = $('#micrositeName').val();
            let micrositeDescription = $('#micrositeDescription').val();
            var themeId = $('#theme').data('value');

            $.ajax({
                url: `{{ route('ajax.store-microsite') }}`
                , type: 'POST'
                , data: {
                    _token: '{{ csrf_token() }}'
                    , micrositeName: micrositeName
                    , micrositeDesc: micrositeDescription
                    , themeId: themeId
                }
                , success: function(response) {
                    var id = response.message.id;
                    var redirectUrl = `{{ route('admin.microsite.create.microsite', ['id' => 'dataID']) }}`;
                    redirectUrl = redirectUrl.replace('dataID', id)
                    window.location.href = redirectUrl;
                }
                , error: function(error) {
                    console.log(error);
                }
            });
        });

    });

</script>
@endsection
