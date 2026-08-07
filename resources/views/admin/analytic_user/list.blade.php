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
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0)">Project Analytic - User</a></li>
                            <li class="breadcrumb-item" aria-current="page">List Projects</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h2 class="mb-0">Project Analytics User</h2>
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
                                <button type="button" class="btn btn-lg btn-primary rounded-1" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                                    <i class="fa fa-plus"></i> New Short Link
                                </button>
                            </li>
                        </ul>
                        <ul class="list-inline ms-auto my-1">
                            <li class="list-inline-item">
                                <form action="" method="POST" class="form-search" id="searchForm">
                                    @csrf
                                    <i class="ti ti-search" onclick="searchForm()"></i>
                                    <input type="search" name="search" class="form-control" placeholder="Search Products" value="{{ $search ?? '' }}">
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>


            <!-- Modal -->
            <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modelNewShortLinkLabel">Add New Short Link</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="custom-preload" style="display: none">
                                <span class="loader"></span>
                            </div>

                            <form action="" method="POST" id="createLinkForm">
                                @csrf
                                <div class="form-group">
                                    <label for="">Campaign Name *</label>
                                    <input type="text" name="campaign_name" class="form-control" required placeholder="Project #1">
                                </div>
                                <div class="form-group">
                                    <label for="">Description (Optional)</label>
                                    <textarea name="description" id="" class="form-control" rows="3" placeholder="your project description"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="">Tags *</label>
                                    <input type="text" name="tags" class="form-control tags" required placeholder="RND, DST" value="{{ implode(',', $tags) }}">
                                </div>
                                <div class="form-group">
                                    <label for="">Original URL *</label>
                                    <input type="url" name="original_url" id="original_url" class="form-control" required placeholder="https://original-link.here">
                                </div>

                                <div class="form-group mt-4 d-flex align-items-center gap-2">
                                    <input type="checkbox" id="setUTM" value="setUTM">
                                    <label for="setUTM">Set UTM</label>
                                </div>

                                <div id="utm-section" style="display: none">
                                    <div class="form-group">
                                        <label for="" class="form-label">UTM Campaign</label>
                                        <input type="text" name="utm_campaign" id="utm_campaign" class="form-control" value="">
                                        <div class="text-sm text-muted mb-4">Parameter campaign menganalisis campaign apa saja yang menarik audiensmu. Parameter ini dapat digunakan untuk menganalisis kinerja campaign yang sedang berlangsung.</div>
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="form-label">UTM Medium</label>
                                        <input type="text" name="utm_medium" id="utm_medium" class="form-control" value="">
                                        <div class="text-sm text-muted mb-4">Parameter ini merujuk dari medium apa audiens menemukan kontenmu. Medium yang dimaksud misalnya email, media sosial, dan sebagainya.</div>
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="form-label">UTM Source</label>
                                        <input type="text" name="utm_source" id="utm_source" class="form-control" value="">
                                        <div class="text-sm text-muted mb-4">Parameter ini mengidentifikasi dari mana audiens menemukan kontenmu, apakah dari media sosial seperti Facebook, Twitter, atau dari berbagai jenis search engine seperti Google.</div>
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="form-label">UTM Content</label>
                                        <input type="text" name="utm_content" id="utm_content" class="form-control" value="">
                                        <div class="text-sm text-muted mb-4">Parameter content menganalisis konten apa saja yang menarik bagi audiens kamu. Analisis pada parameter ini akan menganalisis CTA (click to action) apa saja yang berhasil menarik audiens.</div>
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="form-label">UTM Term</label>
                                        <input type="text" name="utm_term" id="utm_term" class="form-control" value="">
                                        <div class="text-sm text-muted mb-4">Parameter term digunakan jika kamu memiliki konten berbayar. Parameter ini akan menganalisis konten berbayar apa saja yang menarik pengunjung paling banyak.</div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-lg btn-primary w-100 rounded-1 mt-4">Short
                                    Link</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal Custom Link --}}
            <div class="modal fade" id="modalCustomLink" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modelNewShortLinkLabel">Custom Short Link</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="reloadPage()"></button>
                        </div>
                        <div class="modal-body">
                            <div class="custom-preload" style="display: none">
                                <span class="loader"></span>
                            </div>

                            <form action="" method="POST" id="customLinkForm">
                                @csrf
                                <div class="form-group">
                                    <h4>Default : Main Domain</h4>
                                    <div class="d-flex align-items-center border copy-wrapper">
                                        <div class="ps-2">{{ ENV('APP_URL') }}/</div>
                                        <input type="text" name="result_link" value="YHBuasdgj" data-default="YHBuasdgj" data-id="0" class="w-90 border-0 result-link" readonly onkeyup="addParamLink(this)">
                                        <div class="d-flex ms-auto action-custom-link">
                                            <button type="button" class="btn btn-light rounded-0" onclick="copy(event, '{{ ENV('APP_URL') }}/')"><i class="fa fa-copy"></i></button>
                                            <button type="button" class="btn btn-secondary rounded-0 btn-custom-link">Custom
                                                Link</button>
                                        </div>
                                        <div class="d-flex ms-auto action-custom-link2 d-none">
                                            <button type="button" class="btn btn-outline-danger rounded-0 btn-cancel-custom">&times;
                                                Cancel</button>
                                            <button type="submit" class="btn btn-primary rounded-0 btn-custom-link">Update
                                                Link</button>
                                        </div>
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
                            </form>
                        </div>
                    </div>
                </div>
            </div>


            {{-- Modal Edit --}}
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

                            <form action="" method="POST" id="formEdit">
                                @csrf
                                <input type="hidden" name="edit_dataid" id="edit_data_id" value="">
                                <div class="form-group">
                                    <label for="">Campaign Name *</label>
                                    <input type="text" name="campaign_name" id="edit_campaign_name" class="form-control" required placeholder="Project #1">
                                </div>
                                <div class="form-group">
                                    <label for="">Description (Optional)</label>
                                    <textarea name="description" id="edit_description" class="form-control" rows="3" placeholder="your project description"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="">Tags *</label>
                                    <input type="text" name="tags" id="edit_tags" class="form-control" required placeholder="RND, DST">
                                </div>
                                <div class="form-group">
                                    <label for="">Original URL *</label>
                                    <input type="url" name="original_url" id="edit_original_url" class="form-control" required placeholder="https://original-link.here">
                                </div>
                                <div class="form-group">
                                    <label for="">Shorted Link</label>
                                    <div class="input-group">
                                        <div class="input-group-text">
                                            {{ ENV('APP_URL') }}
                                        </div>
                                        <input type="text" name="result_link" id="edit_result_link" value="YHBuasdgj" data-default="YHBuasdgj" data-id="0" required class="form-control result-link" onkeyup="addParamLink(this)">
                                    </div>
                                </div>

                                <div class="form-group mt-4 d-flex align-items-center gap-2">
                                    <input type="checkbox" id="editSetUTM" value="setUTM">
                                    <label for="editSetUTM">Set UTM</label>
                                </div>

                                <div id="edit-utm-section" style="display: none">
                                    <div class="form-group">
                                        <label for="" class="form-label">UTM Campaign</label>
                                        <input type="text" name="utm_campaign" id="edit_utm_campaign" class="form-control" value="">
                                        <div class="text-sm text-muted mb-4">Parameter campaign menganalisis campaign apa saja yang menarik audiensmu. Parameter ini dapat digunakan untuk menganalisis kinerja campaign yang sedang berlangsung.</div>
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="form-label">UTM Medium</label>
                                        <input type="text" name="utm_medium" id="edit_utm_medium" class="form-control" value="">
                                        <div class="text-sm text-muted mb-4">Parameter ini merujuk dari medium apa audiens menemukan kontenmu. Medium yang dimaksud misalnya email, media sosial, dan sebagainya.</div>
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="form-label">UTM Source</label>
                                        <input type="text" name="utm_source" id="edit_utm_source" class="form-control" value="">
                                        <div class="text-sm text-muted mb-4">Parameter ini mengidentifikasi dari mana audiens menemukan kontenmu, apakah dari media sosial seperti Facebook, Twitter, atau dari berbagai jenis search engine seperti Google.</div>
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="form-label">UTM Content</label>
                                        <input type="text" name="utm_content" id="edit_utm_content" class="form-control" value="">
                                        <div class="text-sm text-muted mb-4">Parameter content menganalisis konten apa saja yang menarik bagi audiens kamu. Analisis pada parameter ini akan menganalisis CTA (click to action) apa saja yang berhasil menarik audiens.</div>
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="form-label">UTM Term</label>
                                        <input type="text" name="utm_term" id="edit_utm_term" class="form-control" value="">
                                        <div class="text-sm text-muted mb-4">Parameter term digunakan jika kamu memiliki konten berbayar. Parameter ini akan menganalisis konten berbayar apa saja yang menarik pengunjung paling banyak.</div>
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


                                <button type="submit" class="btn btn-lg btn-primary w-100 rounded-1 mt-4">Update Data</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th width="70px">#</th>
                                            <th>Campaign Name</th>
                                            <th>Short Code</th>
                                            <th>Original URL</th>
                                            <th>Author</th>
                                            <th>Tags</th>
                                            <th>Total Visitor</th>
                                            <th>Created Date</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($datas as $data)
                                        @if (!$data->user->isAdmin())
                                            <tr>
                                                <td>{{ ($datas->currentPage() - 1) * $datas->perPage() + $loop->iteration }}</td>
                                                <td>
                                                    <strong>{{ $data->campaign_name ?? 'Untitled' }}</strong><br>
                                                    <small><a href="{{ ENV('APP_URL').'/'.$data->short_url }}" target="new" class="text-muted" style="max-width: 160px; display: inline-block; overflow: hidden; text-overflow: ellipsis;">{{ ENV('APP_URL').'/'.$data->short_url }}</a></small>
                                                </td>
                                                <td><strong>{{ $data->short_url }}</strong></td>
                                                <td>
                                                    <a href="{{ $data->original_url }}" target="new" style="display: inline-block; max-width: 300px; overflow: hidden; text-overflow: ellipsis;">{{ $data->original_url }}</a>
                                                </td>
                                                <td>
                                                    {{ $data->user->name }}
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary">{{ $data->user->user_label }}</span>
                                                </td>
                                                <td>
                                                    @php
                                                    $formattedVisitor = number_format($data->visitor);
                                                    @endphp
                                                    <i class="ti ti-accessible"></i> <strong>{{ $formattedVisitor }}</strong> Visitor
                                                </td>
                                                <td>{{ Carbon::parse($data->created_at)->format('d M Y') }}</td>
                                                <td>
                                                    <div class="text-end d-flex flex-wrap gap-2 justify-content-end">
                                                        <a href="javascript:void(0)" class="btn btn-sm btn-outline-danger rounded-1" onclick="deleteData({{ $data->id }})"><i class="ti ti-trash"></i> Delete</a>
                                                        <a href="javascript:void(0)" class="btn btn-sm btn-outline-primary rounded-1" onclick="openEdit({{ $data->id }})"><i class="ti ti-edit"></i> Edit</a>
                                                        <a href="{{ route('project_analytic.analytic', ['project_id' => $data->id]) }}" class="btn btn-sm btn-success rounded-1"><i class="ti ti-report-analytics"></i> View Analytic</a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>

                            <nav class="mt-5">
                                {{ $datas->links() }}
                            </nav>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
<!-- [ Main Content ] end -->
@endsection

@section('custom-js')
<!-- [Page Specific JS] start -->
<script src="{{ asset('assets') }}/js/plugins/choices.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
<script src="{{ asset('assets') }}/js/plugins/sweetalert2.all.min.js"></script>
<!-- [Page Specific JS] ends -->


<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tagsElements = document.getElementsByClassName('tags');

        Array.from(tagsElements).forEach(function(element) {
            new Choices(element, {
                delimiter: ','
                , editItems: true
                , maxItemCount: 5
                , removeItemButton: true
            });
        });
    });

</script>



<script>
    $('.btn-custom-link').click(function() {
        $('.result-link').removeAttr('readonly');
        $('.result-link').focus();
        $('.action-custom-link').addClass('d-none');
        $('.action-custom-link2').removeClass('d-none')
    })

    $('.btn-cancel-custom').click(function() {
        $('.result-link').attr('readonly', true);
        $('.action-custom-link').removeClass('d-none');
        $('.action-custom-link2').addClass('d-none');

        let dataDefault = $('.result-link').attr('data-default');
        $('.result-link').val(dataDefault);
        $('.alt-domain').each(function(index, altDomain) {
            $(altDomain).val($(altDomain).data('domain') + '/' + dataDefault); // Corrected to use data.shortLink
        });
    })

    function addParamLink(e) {
        var resultLink = $(e).val(); // Get the current value of .result-link
        $('.alt-domain').each(function(index, altDomain) {
            $(altDomain).val($(altDomain).data('domain') + '/' + resultLink);
        })
    }

</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.6.7/axios.min.js" integrity="sha512-NQfB/bDaB8kaSXF8E77JjhHG5PM6XVRxvHzkZiwl3ddWCEPBa23T76MuWSwAJdMGJnmQqM0VeY9kFszsrBEFrQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    $('#createLinkForm').submit(function(e) {
        e.preventDefault();
        $('.custom-preload').show().addClass('d-flex');
        axios.post(`{{ route('admin.short.store') }}`, {
                'campaign_name': $(this).find('input[name="campaign_name"]').val()
                , 'description': $(this).find('textarea[name="description"]').val()
                , 'tags': $(this).find('input[name="tags"]').val()
                , 'original_url': $(this).find('input[name="original_url"]').val()
                , 'utm_campaign': $(this).find('input[name="utm_campaign"]').val()
                , 'utm_source': $(this).find('input[name="utm_source"]').val()
                , 'utm_medium': $(this).find('input[name="utm_medium"]').val()
                , 'utm_content': $(this).find('input[name="utm_content"]').val()
                , 'utm_term': $(this).find('input[name="utm_term"]').val()
            })
            .then(res => {
                let data = res.data;
                $('#staticBackdrop').modal('hide');
                $('#modalCustomLink').modal('show');
                $('.custom-preload').hide().removeClass('d-flex');

                $('.result-link').val(data.shortLink.short_url); // Corrected to use data.shortLink
                $('.result-link').attr('data-default', data.shortLink.short_url)
                    .attr('data-id', data.shortLink.id);
                $('.alt-domain').each(function(index, altDomain) {
                    $(altDomain).val($(altDomain).data('domain') + '/' + data.shortLink.short_url); // Corrected to use data.shortLink
                });


                var notyf = new Notyf({
                    position: {
                        x: 'right'
                        , y: 'top'
                    , }
                });
                notyf.success('Short link berhasil dibuat.');
            })
            .catch(err => {
                console.error('terjadi kesalahan', err);
            });
    });

</script>

<script>
    function copy(event, prefix = null) {
        var input = $(event.target).closest('.copy-wrapper').find('input');
        var text = prefix ? prefix + input.val() : input.val();
        navigator.clipboard.writeText(text)
    }

    $('#customLinkForm').submit(function(e) {
        e.preventDefault();
        $('.custom-preload').show().addClass('d-flex');
        axios.post(`{{ route('admin.short.update') }}`, {
                'short_id': parseInt($(this).find('input[name="result_link"]').attr('data-id'))
                , 'short_url': $(this).find('input[name="result_link"]').val()
            , })
            .then(res => {
                $('.custom-preload').hide().removeClass('d-flex');
                if (res.data.status == 'success') {
                    $('.result-link').attr('readonly', true).attr('data-default', res.data.shortLink.short_url);
                    $('.action-custom-link').removeClass('d-none');
                    $('.action-custom-link2').addClass('d-none');
                    var notyf = new Notyf({
                        position: {
                            x: 'right'
                            , y: 'top'
                        , }
                    });
                    notyf.success('Short link berhasil diperbarui.');
                } else {
                    var notyf = new Notyf({
                        position: {
                            x: 'right'
                            , y: 'top'
                        , }
                    });
                    notyf.error('Short link sudah terpakai');
                }
            })
            .catch(err => {
                console.error('terjadi kesalahan', err)
            })
    })

</script>

<script>
    function reloadPage() {
        window.location.reload();
    }

    function copy(event, prefix = null) {
        var input = $(event.target).closest('.copy-wrapper').find('input');
        var text = prefix ? prefix + input.val() : input.val();
        navigator.clipboard.writeText(text)
    }

    function deleteDomain(dataID) {
        Swal.fire({
            title: "Are you sure?"
            , text: "Data will be delete permanently!"
            , icon: "warning"
            , showCancelButton: true
            , confirmButtonColor: "#FFFFFF"
            , confirmButtonText: "Yes, delete it!"
            , customClass: {
                confirmButton: 'btn-danger'
                , cancelButton: 'btn btn-outline-primary'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                let data = {
                    dataID: dataID
                }
                axios.delete(`{{ route('admin.domain_decentralized.delete') }}`, {
                        data: data
                    })
                    .then(res => {
                        Swal.fire({
                                title: "Deleted!"
                                , text: "Your file has been deleted."
                                , icon: "success"
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

<script>
    let reloadAfterEdit = false;

    function reloadIt() {
        if (reloadAfterEdit) {
            window.location.reload();
        }
    }

    function openEdit(dataID) {
        $('#modalEdit').modal('show')
        let url = `{{ route('view.get-dataById', ['id'=>'dataID']) }}`
        url = url.replace('dataID', dataID)
        axios.get(url)
            .then(res => {
                $('#edit_data_id').val(res.data.id);
                $('#edit_campaign_name').val(res.data.campaign_name);
                $('#edit_description').text(res.data.descriotion);
                if (res.data.labels.length > 0) {
                    // Mengambil semua label dari array labels
                    var allLabels = res.data.labels.map(function(label) {
                        return label.label_name;
                    });

                    // Menetapkan nilai elemen dengan ID choices-text-remove-button
                    $('#edit_tags').val(allLabels.join(','));
                }
                // Rebuild Choices untuk #edit_tags
                new Choices(document.getElementById('edit_tags'), {
                    delimiter: ','
                    , editItems: true
                    , maxItemCount: 5
                    , removeItemButton: true
                });


                $('#edit_original_url').val(res.data.original_url);

                let originalURL = res.data.original_url;
                let hasQuery = originalURL.indexOf('?') !== -1;
                $('#editSetUTM').prop('checked', hasQuery);
                if (hasQuery) {
                    $('#edit-utm-section').show();
                    $('#edit_utm_campaign').val(res.data.utm_campaign);
                    $('#edit_utm_source').val(res.data.utm_source);
                    $('#edit_utm_medium').val(res.data.utm_medium);
                    $('#edit_utm_content').val(res.data.utm_content);
                    $('#edit_utm_term').val(res.data.utm_term);
                } else {
                    $('#edit-utm-section').hide();
                    $('#edit_utm_campaign').val('');
                    $('#edit_utm_source').val('');
                    $('#edit_utm_medium').val('');
                    $('#edit_utm_content').val('');
                    $('#edit_utm_term').val('');
                }


                $('#edit_result_link').val(res.data.short_url);
                $('.alt-domain').each(function(index, altDomain) {
                    $(altDomain).val($(altDomain).data('domain') + '/' + res.data.short_url);
                })
            })
            .catch(err => {
                console.error('terjadi kesalahan', err)
            })
    }


    $('#formEdit').submit(function(e) {
        e.preventDefault();
        $('.custom-preload').show().addClass('d-flex');
        let dataID = $('#edit_data_id').val();
        let dataToPatch = {
            'data_id': dataID
            , 'name': $('#edit_campaign_name').val()
            , 'description': $('#edit_description').val()
            , 'tags': $('#edit_tags').val()
            , 'url': $('#edit_original_url').val()
            , 'link_code': $('#edit_result_link').val()
            , 'utm_campaign': $('#edit_utm_campaign').val()
            , 'utm_source': $('#edit_utm_source').val()
            , 'utm_medium': $('#edit_utm_medium').val()
            , 'utm_content': $('#edit_utm_content').val()
            , 'utm_term': $('#edit_utm_term').val()
        }
        let url = `{{ route('project_analytic.udpate') }}`
        axios.patch(url, dataToPatch)
            .then(res => {
                console.log(res.data)
                $('.custom-preload').hide().removeClass('d-flex');
                var notyf = new Notyf({
                    position: {
                        x: 'right'
                        , y: 'top'
                    , }
                });
                notyf.success(res.data.message);
                reloadAfterEdit = true
            })
            .catch(err => {
                console.error('Terjadi kesalahan', err.response.data)
                $('.custom-preload').hide().removeClass('d-flex');
                var notyf = new Notyf({
                    position: {
                        x: 'right'
                        , y: 'top'
                    , }
                });
                notyf.error(err.response.data.message);
            })
    });

</script>

<script>
    function deleteData(dataID) {
        Swal.fire({
            title: "Are you sure?"
            , text: "Data will be delete permanently!"
            , icon: "warning"
            , showCancelButton: true
            , confirmButtonColor: "#FFFFFF"
            , confirmButtonText: "Yes, delete it!"
            , customClass: {
                confirmButton: 'btn-danger'
                , cancelButton: 'btn btn-outline-primary'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                let data = {
                    dataID: dataID
                }
                axios.delete(`{{ route('project_analytic.delete') }}`, {
                        data: data
                    })
                    .then(res => {
                        Swal.fire({
                                title: "Deleted!"
                                , text: "Your data has been deleted."
                                , icon: "success"
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

<script>
    function searchForm() {
        $('#searchForm').submit();
    }

    function getQueryParamValue(url, paramName) {
        const searchParams = new URLSearchParams(url.search);
        return searchParams.get(paramName);
    }

    $('#setUTM').change(function() {
        if ($(this).prop('checked') == true) {
            $('#utm-section').show();
        } else {
            $('#utm-section').hide();
            let urlString = $('#original_url').val().split('?')
            $('#original_url').val(urlString[0])
            $('#utm_campaign').val('');
            $('#utm_source').val('');
            $('#utm_medium').val('');
            $('#utm_content').val('');
            $('#utm_term').val('');
        }
    });

    $('#editSetUTM').change(function() {
        if ($(this).prop('checked') == true) {
            $('#edit-utm-section').show();
        } else {
            $('#edit-utm-section').hide();
            let urlString = $('#edit_original_url').val().split('?')
            $('#edit_original_url').val(urlString[0])
            $('#edit_utm_campaign').val('');
            $('#edit_utm_source').val('');
            $('#edit_utm_medium').val('');
            $('#edit_utm_content').val('');
            $('#edit_utm_term').val('');
        }
    });


    $('#original_url').keyup(function() {
        let urlString = $(this).val();
        let hasQuery = urlString.indexOf('?') !== -1; // return : true or false
        $('#setUTM').prop('checked', hasQuery);
        if (hasQuery) {
            $('#utm-section').show();
        } else {
            $('#utm-section').hide();
        }

        let searchParam = new URL(urlString);
        $('#utm_campaign').val(getQueryParamValue(searchParam, 'utm_campaign'));
        $('#utm_source').val(getQueryParamValue(searchParam, 'utm_source'));
        $('#utm_content').val(getQueryParamValue(searchParam, 'utm_content'));
        $('#utm_term').val(getQueryParamValue(searchParam, 'utm_term'));
        $('#utm_medium').val(getQueryParamValue(searchParam, 'utm_medium'));
    });

    function addQueryParam(utm_key, utm_value) {
        let urlString = $('#original_url').val();

        try {
            let urlObject = new URL(urlString); // Parse the URL

            // Remove existing occurrences of utm_campaign parameter
            urlObject.searchParams.delete(utm_key);

            // Add the new utm_campaign parameter
            urlObject.searchParams.append(utm_key, utm_value);

            // Set the modified URL back to the input field
            $('#original_url').val(urlObject.toString());
        } catch (error) {
            alert("Original URL belum diisi");
        }
    }

    $('#utm_campaign').keyup(function() {
        addQueryParam('utm_campaign', $(this).val());
    });
    $('#utm_source').keyup(function() {
        addQueryParam('utm_source', $(this).val());
    });
    $('#utm_content').keyup(function() {
        addQueryParam('utm_content', $(this).val());
    });
    $('#utm_medium').keyup(function() {
        addQueryParam('utm_medium', $(this).val());
    });
    $('#utm_term').keyup(function() {
        addQueryParam('utm_term', $(this).val());
    });

    function addQueryParamEdit(utm_key, utm_value) {
        let urlString = $('#edit_original_url').val();
        try {
            let urlObject = new URL(urlString); // Parse the URL

            // Remove existing occurrences of utm_campaign parameter
            urlObject.searchParams.delete(utm_key);

            // Add the new utm_campaign parameter
            urlObject.searchParams.append(utm_key, utm_value);

            // Set the modified URL back to the input field
            $('#edit_original_url').val(urlObject.toString());
        } catch (error) {
            alert("Original URL belum diisi");
        }
    }

    $('#edit_utm_campaign').keyup(function() {
        addQueryParamEdit('utm_campaign', $(this).val());
    });
    $('#edit_utm_source').keyup(function() {
        addQueryParamEdit('utm_source', $(this).val());
    });
    $('#edit_utm_content').keyup(function() {
        addQueryParamEdit('utm_content', $(this).val());
    });
    $('#edit_utm_medium').keyup(function() {
        addQueryParamEdit('utm_medium', $(this).val());
    });
    $('#edit_utm_term').keyup(function() {
        addQueryParamEdit('utm_term', $(this).val());
    });

</script>
@endsection
