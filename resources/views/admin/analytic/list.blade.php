@extends('admin.components.main')

@section('custom-css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.css" />
@endsection

@section('main-content')
    <div class="pc-container">
        <div class="pc-content">
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item"><a href="javascript: void(0)">Short Link Analytic</a></li>
                                <li class="breadcrumb-item" aria-current="page">List Projects</li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h2 class="mb-0">Short Link Analytics</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="page-content mt-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="d-sm-flex align-items-center">
                            <ul class="list-inline me-auto my-1">
                                <li class="list-inline-item">
                                    <button type="button" class="btn btn-lg btn-primary rounded-1" data-bs-toggle="modal"
                                        data-bs-target="#staticBackdrop">
                                        <i class="fa fa-plus"></i> New Short Link
                                    </button>
                                </li>
                            </ul>
                            <ul class="list-inline ms-auto my-1">
                                <li class="list-inline-item">
                                    <form action="" method="GET" class="form-search" id="searchForm">
                                        <i class="ti ti-search" onclick="searchForm()"></i>
                                        <input type="search" name="search" class="form-control"
                                            placeholder="Search Products" value="{{ $search ?? '' }}">
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>




                {{-- Modal Custom Link --}}
                <div class="modal fade" id="modalCustomLink" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modelNewShortLinkLabel">Custom Short Link</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                    onclick="reloadPage()"></button>
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
                                            <input type="text" name="result_link" value="YHBuasdgj"
                                                data-default="YHBuasdgj" data-id="0" class="w-90 border-0 result-link"
                                                readonly onkeyup="addParamLink(this)">
                                            <div class="d-flex ms-auto action-custom-link">
                                                <button type="button" class="btn btn-light rounded-0"
                                                    onclick="copy(event, '{{ ENV('APP_URL') }}/')"><i
                                                        class="fa fa-copy"></i></button>
                                                <button type="button"
                                                    class="btn btn-secondary rounded-0 btn-custom-link">Custom
                                                    Link</button>
                                            </div>
                                            <div class="d-flex ms-auto action-custom-link2 d-none">
                                                <button type="button"
                                                    class="btn btn-outline-danger rounded-0 btn-cancel-custom">&times;
                                                    Cancel</button>
                                                <button type="submit"
                                                    class="btn btn-primary rounded-0 btn-custom-link">Update
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
                                                    <input type="text" data-domain="{{ $item['domain_url'] }}"
                                                        class="form-control rounded-0 fw-bold alt-domain"
                                                        value="{{ $item['domain_url'] }}/" readonly>
                                                    <button type="button"
                                                        class="btn btn-light border border-start-0 rounded-0"
                                                        onclick="copy(event)"><i class="fa fa-copy"></i></button>
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
                <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modelNewShortLinkLabel">Add New Short Link</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="custom-preload" style="display: none">
                                    <span class="loader"></span>
                                </div>

                                <form action="" method="POST" id="createLinkForm" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <label for="campaign_id">Campaign Name *</label>
                                        {{-- 'name' sekarang 'campaign_id', 'id' juga 'campaign_id' untuk 'label' --}}
                                        <select name="campaign_id" id="campaign_id" class="form-control" required>
                                            <option value="">-- Pilih Campaign --</option>
                                            @foreach ($campaigns as $campaign)
                                                <option value="{{ $campaign->id }}">{{ $campaign->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="">Description (Optional)</label>
                                        <textarea name="description" id="" class="form-control" rows="3"
                                            placeholder="your project description"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="">Tags *</label>
                                        <input type="text" name="tags" class="form-control tags" required
                                            placeholder="RND, DST" value="{{ implode(',', $tags) ?? '' }}">
                                    </div>

                                    {{-- Original URL Group: Diberi ID untuk kontrol visibility --}}
                                    <div class="form-group mt-3" id="original-url-group">
                                        <label for="original_url">Original URL *</label>
                                        <input type="url" name="original_url" id="original_url" class="form-control"
                                            required placeholder="https://original-link.here">
                                        <div class="text-sm text-muted mt-1">Ini adalah tujuan utama tautan pendek Anda.
                                        </div>
                                    </div>

                                    <hr class="mt-4 mb-4">

                                    <div class="form-group d-flex align-items-center gap-2">
                                        <input type="checkbox" id="setMultiRedirect" value="1"
                                            name="is_multi_redirect">
                                        <label for="setMultiRedirect">Multi Redirect?</label>
                                    </div>

                                    <div id="multi-redirect-section" style="display: none">
                                        <div class="row mt-4 mb-4">
                                            <div class="col-12">
                                                <h4>Multi Redirect Links *</h4>

                                                <div id="multi-redirect-form">
                                                    {{-- Baris redirect akan ditambahkan di sini oleh JavaScript --}}
                                                </div>

                                                <div class="text-center">
                                                    <button type="button"
                                                        class="btn btn-success rounded-1 add-multi-redirect"><i
                                                            class="ti ti-plus"></i> Add Redirect</button>
                                                    <div class="text-sm text-muted mt-2">Total Percentage: <strong
                                                            id="total-percentage">0</strong>% (Harus 100%)</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="mt-4 mb-4">

                                    {{-- ... Konten Form Lain (Image, Title, Description) ... --}}

                                    <div class="form-group mt-3">
                                        <label for="images_background" class="form-label">Image/Background File
                                            (Optional)</label>
                                        <input class="form-control" type="file" id="images_background"
                                            name="images_background" accept="image/*">
                                        <div class="text-sm text-muted mt-1">Upload an image for this short link's
                                            background or social media preview.</div>
                                    </div>

                                    <div class="form-group mt-3">
                                        <label for="custom_title" class="form-label">Custom Title (Optional)</label>
                                        <input type="text" name="custom_title" id="custom_title" class="form-control"
                                            placeholder="A compelling title for social sharing">
                                        <div class="text-sm text-muted mt-1">Set a custom title for this short link (e.g.,
                                            for SEO or social media cards).</div>
                                    </div>

                                    <div class="form-group mt-3">
                                        <label for="custom_description" class="form-label">Custom Description
                                            (Optional)</label>
                                        <textarea name="custom_description" id="custom_description" class="form-control" rows="2"
                                            placeholder="A short description for social sharing"></textarea>
                                        <div class="text-sm text-muted mt-1">Set a custom description (max 160 characters)
                                            for better social media previews.</div>
                                    </div>

                                    <hr class="mt-4 mb-4">

                                    {{-- ... Bagian UTM (Tidak Diubah) ... --}}
                                    <div class="form-group d-flex align-items-center gap-2">
                                        <input type="checkbox" id="setUTM" value="setUTM">
                                        <label for="setUTM">Set UTM</label>
                                    </div>

                                    <div id="utm-section" style="display: none">
                                        <div class="form-group">
                                            <label for="" class="form-label">UTM Campaign</label>
                                            <input type="text" name="utm_campaign" id="utm_campaign"
                                                class="form-control" value="">
                                            <div class="text-sm text-muted mb-4">...</div>
                                        </div>
                                        <div class="form-group">
                                            <label for="" class="form-label">UTM Medium</label>
                                            <input type="text" name="utm_medium" id="utm_medium" class="form-control"
                                                value="">
                                            <div class="text-sm text-muted mb-4">...</div>
                                        </div>
                                        <div class="form-group">
                                            <label for="" class="form-label">UTM Source</label>
                                            <input type="text" name="utm_source" id="utm_source" class="form-control"
                                                value="">
                                            <div class="text-sm text-muted mb-4">...</div>
                                        </div>
                                        <div class="form-group">
                                            <label for="" class="form-label">UTM Content</label>
                                            <input type="text" name="utm_content" id="utm_content"
                                                class="form-control" value="">
                                            <div class="text-sm text-muted mb-4">...</div>
                                        </div>
                                        <div class="form-group">
                                            <label for="" class="form-label">UTM Term</label>
                                            <input type="text" name="utm_term" id="utm_term" class="form-control"
                                                value="">
                                            <div class="text-sm text-muted mb-4">...</div>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-lg btn-primary w-100 rounded-1 mt-4">Short
                                        Link</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal Edit --}}
                <div class="modal fade" id="modalEdit" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Update Short Link</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                    onclick="reloadIt()">
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
                                        <label for="edit_campaign_name">Campaign Name *</label>
                                        <input type="text" name="campaign_name" id="edit_campaign_name"
                                            class="form-control" required placeholder="Project #1">
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_description">Description (Optional)</label>
                                        <textarea name="description" id="edit_description" class="form-control" rows="3"
                                            placeholder="your project description"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_tags">Tags *</label>
                                        <input type="text" name="tags" id="edit_tags" class="form-control"
                                            required placeholder="RND, DST">
                                    </div>

                                    {{-- Original URL Group di Modal Edit (PENTING: ID ini digunakan oleh JS) --}}
                                    <div class="form-group" id="edit-original-url-group">
                                        <label for="edit_original_url">Original URL *</label>
                                        <input type="url" name="original_url" id="edit_original_url"
                                            class="form-control" required placeholder="https://original-link.here">
                                    </div>

                                    <hr class="mt-4 mb-4">

                                    {{-- Checkbox Multi Redirect Modal Edit (PENTING: ID ini digunakan oleh JS) --}}
                                    <div class="form-group d-flex align-items-center gap-2">
                                        <input type="checkbox" id="editSetMultiRedirect" value="1"
                                            name="is_multi_redirect">
                                        <label for="editSetMultiRedirect">Multi Redirect?</label>
                                    </div>

                                    {{-- Multi Redirect Section Modal Edit (PENTING: ID ini digunakan oleh JS) --}}
                                    <div id="edit-multi-redirect-section" style="display: none">
                                        <div class="row mt-4 mb-4">
                                            <div class="col-12">
                                                <h4>Multi Redirect Links *</h4>

                                                <div id="edit-multi-redirect-form">
                                                    {{-- Baris redirect akan diisi oleh openEdit JS --}}
                                                </div>

                                                <div class="text-center">
                                                    <button type="button"
                                                        class="btn btn-success rounded-1 add-multi-redirect"><i
                                                            class="ti ti-plus"></i> Add Redirect</button>
                                                    <div class="text-sm text-muted mt-2">Total Percentage: <strong
                                                            id="edit-total-percentage">0</strong>% (Harus 100%)</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="mt-4 mb-4">


                                    {{-- ADDED: Image/Background File for consistency --}}
                                    <div class="form-group mt-3">
                                        <label for="edit_images_background" class="form-label">Image/Background File
                                            (Optional)</label>
                                        <input class="form-control" type="file" id="edit_images_background"
                                            name="images_background" accept="image/*">
                                        <div class="text-sm text-muted mt-1">Upload an image for this short link's
                                            background or social media preview.</div>
                                    </div>

                                    {{-- ADDED: Custom Title for consistency --}}
                                    <div class="form-group mt-3">
                                        <label for="edit_custom_title" class="form-label">Custom Title (Optional)</label>
                                        <input type="text" name="custom_title" id="edit_custom_title"
                                            class="form-control" placeholder="A compelling title for social sharing">
                                        <div class="text-sm text-muted mt-1">Set a custom title for this short link (e.g.,
                                            for SEO or social media cards).</div>
                                    </div>

                                    {{-- ADDED: Custom Description for consistency --}}
                                    <div class="form-group mt-3">
                                        <label for="edit_custom_description" class="form-label">Custom Description
                                            (Optional)</label>
                                        <textarea name="custom_description" id="edit_custom_description" class="form-control" rows="2"
                                            placeholder="A short description for social sharing"></textarea>
                                        <div class="text-sm text-muted mt-1">Set a custom description (max 160 characters)
                                            for better social media previews.</div>
                                    </div>

                                    <hr class="mt-4 mb-4">

                                    <div class="form-group">
                                        <label for="">Shorted Link</label>
                                        <div class="input-group">
                                            <div class="input-group-text">
                                                {{ ENV('APP_URL') }}/
                                            </div>
                                            <input type="text" name="result_link" id="edit_result_link"
                                                value="YHBuasdgj" data-default="YHBuasdgj" data-id="0" required
                                                class="form-control result-link" onkeyup="addParamLink(this)">
                                        </div>
                                    </div>

                                    <div class="form-group mt-4 d-flex align-items-center gap-2">
                                        <input type="checkbox" id="editSetUTM" value="setUTM">
                                        <label for="editSetUTM">Set UTM</label>
                                    </div>

                                    <div id="edit-utm-section" style="display: none">
                                        <div class="form-group">
                                            <label for="edit_utm_campaign" class="form-label">UTM Campaign</label>
                                            <input type="text" name="utm_campaign" id="edit_utm_campaign"
                                                class="form-control" value="">
                                            <div class="text-sm text-muted mb-4">Parameter campaign menganalisis campaign
                                                apa saja yang menarik audiensmu. Parameter ini dapat digunakan untuk
                                                menganalisis kinerja campaign yang sedang berlangsung.</div>
                                        </div>
                                        <div class="form-group">
                                            <label for="edit_utm_medium" class="form-label">UTM Medium</label>
                                            <input type="text" name="utm_medium" id="edit_utm_medium"
                                                class="form-control" value="">
                                            <div class="text-sm text-muted mb-4">Parameter ini merujuk dari medium apa
                                                audiens menemukan kontenmu. Medium yang dimaksud misalnya email, media
                                                sosial, dan sebagainya.</div>
                                        </div>
                                        <div class="form-group">
                                            <label for="edit_utm_source" class="form-label">UTM Source</label>
                                            <input type="text" name="utm_source" id="edit_utm_source"
                                                class="form-control" value="">
                                            <div class="text-sm text-muted mb-4">Parameter ini mengidentifikasi dari mana
                                                audiens menemukan kontenmu, apakah dari media sosial seperti Facebook,
                                                Twitter, atau dari berbagai jenis search engine seperti Google.</div>
                                        </div>
                                        <div class="form-group">
                                            <label for="edit_utm_content" class="form-label">UTM Content</label>
                                            <input type="text" name="utm_content" id="edit_utm_content"
                                                class="form-control" value="">
                                            <div class="text-sm text-muted mb-4">Parameter content menganalisis konten apa
                                                saja yang menarik bagi audiens kamu. Analisis pada parameter ini akan
                                                menganalisis CTA (click to action) apa saja yang berhasil menarik audiens.
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="edit_utm_term" class="form-label">UTM Term</label>
                                            <input type="text" name="utm_term" id="edit_utm_term"
                                                class="form-control" value="">
                                            <div class="text-sm text-muted mb-4">Parameter term digunakan jika kamu
                                                memiliki konten berbayar. Parameter ini akan menganalisis konten berbayar
                                                apa saja yang menarik pengunjung paling banyak.</div>
                                        </div>
                                    </div>

                                    {{-- Domain Section --}}
                                    <div class="pt-3 mt-3 border-top border-secondary">
                                        <h5 class="mb-3">Alternative Short Links</h5> {{-- Clarified title --}}
                                        @forelse($domains as $item)
                                            <div class="form-group mb-3">
                                                <h5>{{ $item['domain_url'] }}</h5>
                                                <div class="input-group copy-wrapper">
                                                    <input type="text" data-domain="{{ $item['domain_url'] }}"
                                                        class="form-control rounded-0 fw-bold alt-domain"
                                                        value="{{ $item['domain_url'] }}/" readonly>
                                                    <button type="button"
                                                        class="btn btn-light border border-start-0 rounded-0"
                                                        onclick="copy(event)"><i class="fa fa-copy"></i></button>
                                                </div>
                                            </div>

                                        @empty
                                            <p>Tidak ada data.</p>
                                        @endforelse
                                    </div>

                                    <button type="submit" class="btn btn-lg btn-primary w-100 rounded-1 mt-4">Update
                                        Data</button>
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
                                    <table class="table" id="example">
                                        <thead>
                                            <tr>
                                                <th width="70px">#</th>
                                                <th>Campaign Name</th>
                                                <th>Short Code</th>
                                                <th>Description</th>
                                                <th>Original URL</th>
                                                <th>Author</th>
                                                <th>Tags</th>
                                                <th>Created Date</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($datas as $data)
                                                <tr>
                                                    <td>{{ $datas->firstItem() + $loop->index }}</td>
                                                    <td>
                                                        <strong>{{ $data->campaign->name ?? 'Untitled' }}</strong><br>
                                                        <small><a href="{{ ENV('APP_URL') . '/' . $data->short_url }}"
                                                                target="new" class="text-muted"
                                                                style="max-width: 160px; display: inline-block; overflow: hidden; text-overflow: ellipsis;">{{ ENV('APP_URL') . '/' . $data->short_url }}</a></small>
                                                    </td>
                                                    <td><strong>{{ $data->short_url }}</strong></td>
                                                    <td>{{ $data->description }}</td>
                                                    <td>
                                                        <a href="{{ $data->original_url }}" target="new"
                                                            style="display: inline-block; max-width: 300px; overflow: hidden; text-overflow: ellipsis;">{{ $data->original_url }}</a>
                                                    </td>
                                                    <td>
                                                        {{ $data->user->name }}
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge bg-primary">{{ $data->user->user_label }}</span>
                                                    </td>
                                                    <td>{{ Carbon::parse($data->created_at)->format('d M Y') }}</td>
                                                    <td>
                                                        <div
                                                            class="text-end d-flex flex-wrap gap-2 justify-content-end">
                                                            <a href="javascript:void(0)"
                                                                class="btn btn-sm btn-outline-danger rounded-1"
                                                                onclick="deleteData({{ $data->id }})"><i
                                                                    class="ti ti-trash"></i> Delete</a>
                                                            <a href="javascript:void(0)"
                                                                class="btn btn-sm btn-outline-primary rounded-1"
                                                                onclick="openEdit({{ $data->id }})"><i
                                                                    class="ti ti-edit"></i> Edit</a>
                                                            <a href="{{ route('project_analytic.analytic', ['project_id' => $data->id]) }}"
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
                                <nav class="mt-4">
                                    {{ $datas->links() }}
                                </nav>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
    @endsection

@section('custom-js')
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.js"></script>
    <script src="{{ asset('assets') }}/js/plugins/choices.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <script src="{{ asset('assets') }}/js/plugins/sweetalert2.all.min.js"></script>
    <script>
        new DataTable('#example', {
            paging: false,
            searching: false,
            info: false,
            language: {
                emptyTable: 'Tidak ada data short link tersedia.'
            }
        });
        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi Notyf
            var notyf = new Notyf({
                position: {
                    x: 'right',
                    y: 'top',
                }
            });

            const form = document.getElementById('createLinkForm');
            const multiRedirectCheckbox = document.getElementById('setMultiRedirect');
            const multiRedirectSection = document.getElementById('multi-redirect-section');
            const originalUrlGroup = document.getElementById('original-url-group');
            const originalUrlInput = document.getElementById('original_url');
            const multiRedirectForm = document.getElementById('multi-redirect-form');
            const addMultiRedirectButton = document.querySelector('.add-multi-redirect');
            const totalPercentageDisplay = document.getElementById('total-percentage');

            // Template baris form Multi Redirect
            const multiRedirectRowTemplate = `
            <div class="mb-3 d-flex gap-3 align-items-end justify-content-between multi-redirect-row">
                <div class="w-100">
                    <label for="">Link</label>
                    <input type="url" name="redirect_link[]" class="form-control redirect-link" placeholder="Link" required>
                </div>
                <div class="w-100">
                    <label for="">Percentage</label>
                    <input type="text" name="redirect_percentage[]" maxlength="3" class="form-control percentage-input" placeholder="percentage" oninput="this.value = this.value.replace(/[^0-9]/g, ''); calculateTotalPercentage();" required>
                </div>
                <button type="button" class="btn btn-outline-danger rounded-1 mb-1 remove-multi-redirect" onclick="removeRedirectRow(this)">×</button>
            </div>
        `;

            // Fungsi untuk menghitung dan menampilkan total percentage
            function calculateTotalPercentage() {
                let total = 0;
                document.querySelectorAll('#multi-redirect-form .percentage-input').forEach(input => {
                    total += parseInt(input.value) || 0;
                });
                totalPercentageDisplay.textContent = total;
                totalPercentageDisplay.style.color = total === 100 ? 'green' : 'red';
                return total;
            }

            // Fungsi untuk menambah baris
            function addRedirectRow() {
                multiRedirectForm.insertAdjacentHTML('beforeend', multiRedirectRowTemplate);
                calculateTotalPercentage();
            }

            // Fungsi untuk menghapus baris (dipanggil dari onclick di template)
            window.removeRedirectRow = function(button) {
                button.closest('.multi-redirect-row').remove();
                calculateTotalPercentage();
            }

            // === 1. Toggle Tampilan Multi Redirect dan Required Fields ===
            multiRedirectCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    // Sembunyikan Original URL dan buat tidak wajib
                    originalUrlGroup.style.display = 'none';
                    originalUrlInput.disabled = true;
                    originalUrlInput.required = false;
                    originalUrlInput.value = '';

                    // Tampilkan Multi Redirect Section
                    multiRedirectSection.style.display = 'block';

                    // Tambahkan satu baris default jika kosong dan buat wajib
                    if (multiRedirectForm.children.length === 0) {
                        addRedirectRow();
                    } else {
                        // Set required untuk semua field redirect yang ada
                        multiRedirectForm.querySelectorAll('.redirect-link, .percentage-input').forEach(
                            el => el.required = true);
                    }
                } else {
                    // Tampilkan kembali Original URL dan buat wajib
                    originalUrlGroup.style.display = 'block';
                    originalUrlInput.disabled = false;
                    originalUrlInput.required = true;

                    // Sembunyikan Multi Redirect Section dan buat tidak wajib
                    multiRedirectSection.style.display = 'none';
                    multiRedirectForm.querySelectorAll('.redirect-link, .percentage-input').forEach(el => el
                        .required = false);
                }
                calculateTotalPercentage(); // Update status percentage
            });

            // Event listener untuk tombol 'Add Redirect'
            addMultiRedirectButton.addEventListener('click', addRedirectRow);

            // Event delegation untuk menangani input percentage (agar perhitungan selalu update)
            multiRedirectForm.addEventListener('input', function(e) {
                if (e.target.classList.contains('percentage-input')) {
                    calculateTotalPercentage();
                }
            });

            // === 2. Validasi Submit Form (Fix untuk error 'checked' pada baris ~1659) ===
            form.addEventListener('submit', function(e) {
                // *** PERBAIKAN: Menambahkan pemeriksaan null sebelum mengakses properti 'checked' ***
                if (multiRedirectCheckbox && multiRedirectCheckbox.checked) {
                    const total = calculateTotalPercentage();

                    // Cek apakah ada link yang kosong (walaupun sudah pakai required HTML, ini backup)
                    let hasEmptyLink = false;
                    multiRedirectForm.querySelectorAll('.redirect-link').forEach(input => {
                        if (input.value.trim() === '') {
                            hasEmptyLink = true;
                        }
                    });

                    if (hasEmptyLink) {
                        e.preventDefault();
                        notyf.error('Semua field Link harus diisi!');
                        return;
                    }

                    // Cek total percentage
                    if (total !== 100) {
                        e.preventDefault(); // Batalkan submit
                        let message = total < 100 ?
                            `Total percentage kurang dari 100%! Total saat ini: ${total}%` :
                            `Total percentage melebihi 100%! Total saat ini: ${total}%`;

                        notyf.error(message); // Tampilkan alert Notyf
                        return;
                    }

                    // Jika validasi Multi Redirect sukses, lanjutkan proses submit atau Notyf success simulasi
                    // notyf.success('Short link berhasil dibuat.'); // Uncomment ini jika ingin simulasi berhasil
                }
                // Jika Multi Redirect tidak dicentang, form akan disubmit normal dan Original URL wajib
            });

            // Panggil change event saat load untuk inisialisasi awal
            multiRedirectCheckbox.dispatchEvent(new Event('change'));
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var tagsElements = document.getElementsByClassName('tags');

            Array.from(tagsElements).forEach(function(element) {
                new Choices(element, {
                    delimiter: ',',
                    editItems: true,
                    maxItemCount: 5,
                    removeItemButton: true
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
                $(altDomain).val($(altDomain).data('domain') + '/' +
                    dataDefault); // Corrected to use data.shortLink
            });
        })

        function addParamLink(e) {
            var resultLink = $(e).val(); // Get the current value of .result-link
            $('.alt-domain').each(function(index, altDomain) {
                $(altDomain).val($(altDomain).data('domain') + '/' + resultLink);
            })
        }
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.6.7/axios.min.js"
        integrity="sha512-NQfB/bDaB8kaSXF8E77JjhHG5PM6XVRxvHzkZiwl3ddWCEPBa23T76MuWSwAJdMGJnmQqM0VeY9kFszsrBEFrQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $('#createLinkForm').submit(function(e) {
            e.preventDefault();

            // 1. Create a FormData object from the form element
            const formData = new FormData(this);

            // NOTE: If you need to add custom data not in the form, you can do:
            // formData.append('key', 'value');

            // Show loading indicator
            $('.custom-preload').show().addClass('d-flex');

            // 2. Use axios to send the FormData object
            // axios will automatically set the 'Content-Type' to 'multipart/form-data'
            axios.post(`{{ route('admin.short.store') }}`, formData)
                .then(res => {
                    let data = res.data;

                    // Hide main modal and show result modal
                    $('#staticBackdrop').modal('hide');
                    $('#modalCustomLink').modal('show');
                    $('.custom-preload').hide().removeClass('d-flex');

                    // Update result fields
                    $('.result-link').val(data.shortLink.short_url);
                    $('.result-link').attr('data-default', data.shortLink.short_url)
                        .attr('data-id', data.shortLink.id);

                    $('.alt-domain').each(function(index, altDomain) {
                        $(altDomain).val($(altDomain).data('domain') + '/' + data.shortLink.short_url);
                    });

                    // Display success notification
                    var notyf = new Notyf({
                        position: {
                            x: 'right',
                            y: 'top',
                        }
                    });
                    notyf.success('Short link berhasil dibuat.');

                    // Optional: Clear the form after successful submission
                    this.reset();
                })
                .catch(err => {
                    console.error('terjadi kesalahan', err);
                    $('.custom-preload').hide().removeClass('d-flex'); // Hide loader on error

                    // Display error notification (example)
                    var notyf = new Notyf({
                        position: {
                            x: 'right',
                            y: 'top',
                        }
                    });
                    // Check if error response has a message
                    const errorMessage = err.response && err.response.data && err.response.data.message ?
                        err.response.data.message :
                        'Gagal membuat short link. Silakan coba lagi.';
                    notyf.error(errorMessage);
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
                    'short_id': parseInt($(this).find('input[name="result_link"]').attr('data-id')),
                    'short_url': $(this).find('input[name="result_link"]').val(),
                })
                .then(res => {
                    $('.custom-preload').hide().removeClass('d-flex');
                    if (res.data.status == 'success') {
                        $('.result-link').attr('readonly', true).attr('data-default', res.data.shortLink
                            .short_url);
                        $('.action-custom-link').removeClass('d-none');
                        $('.action-custom-link2').addClass('d-none');
                        var notyf = new Notyf({
                            position: {
                                x: 'right',
                                y: 'top',
                            }
                        });
                        notyf.success('Short link berhasil diperbarui.');
                    } else {
                        var notyf = new Notyf({
                            position: {
                                x: 'right',
                                y: 'top',
                            }
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
                    axios.delete(`{{ route('admin.domain_decentralized.delete') }}`, {
                            data: data
                        })
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

    <script>
        let reloadAfterEdit = false;

        function reloadIt() {
            if (reloadAfterEdit) {
                window.location.reload();
            }
        }

        // --- FUNGSI MULTI REDIRECT UNTUK EDIT MODAL ---

        // Template baris form Multi Redirect untuk modal EDIT
        const editMultiRedirectRowTemplate = `
        <div class="mb-3 d-flex gap-3 align-items-end justify-content-between multi-redirect-row">
            <div class="w-100">
                <label for="">Link</label>
                <input type="url" name="redirect_link[]" class="form-control edit-redirect-link" placeholder="Link" required>
            </div>
            <div class="w-100">
                <label for="">Percentage</label>
                <input type="text" name="redirect_percentage[]" maxlength="3" class="form-control edit-percentage-input" placeholder="percentage" oninput="this.value = this.value.replace(/[^0-9]/g, ''); editCalculateTotalPercentage();" required>
            </div>
            <button type="button" class="btn btn-outline-danger rounded-1 mb-1 remove-multi-redirect" onclick="editRemoveRedirectRow(this)">×</button>
        </div>
    `;

        var notyf = new Notyf({
            position: {
                x: 'right',
                y: 'top',
            }
        });

        function editCalculateTotalPercentage() {
            let total = 0;
            document.querySelectorAll('#edit-multi-redirect-form .edit-percentage-input').forEach(input => {
                total += parseInt(input.value) || 0;
            });
            const totalDisplay = document.getElementById('edit-total-percentage');
            if (totalDisplay) {
                totalDisplay.textContent = total;
                totalDisplay.style.color = total === 100 ? 'green' : 'red';
            }
            return total;
        }

        function editAddRedirectRow(link = '', percentage = '') {
            const container = document.getElementById('edit-multi-redirect-form');
            let newRow = document.createElement('div');
            newRow.innerHTML = editMultiRedirectRowTemplate.trim();

            const linkInput = newRow.querySelector('.edit-redirect-link');
            const percentageInput = newRow.querySelector('.edit-percentage-input');

            linkInput.value = link;
            percentageInput.value = percentage;

            container.appendChild(newRow.firstElementChild);
            editCalculateTotalPercentage();
        }

        window.editRemoveRedirectRow = function(button) {
            button.closest('.multi-redirect-row').remove();
            editCalculateTotalPercentage();
        }

        function editToggleMultiRedirect() {
            const checkbox = document.getElementById('editSetMultiRedirect');
            const section = document.getElementById('edit-multi-redirect-section');
            const originalUrlGroup = document.getElementById('edit-original-url-group');
            const originalUrlInput = document.getElementById('edit_original_url');
            const formInputs = document.querySelectorAll(
                '#edit-multi-redirect-form .edit-redirect-link, #edit-multi-redirect-form .edit-percentage-input');

            if (checkbox.checked) {
                // Pastikan elemen ada sebelum disembunyikan
                if (originalUrlGroup) originalUrlGroup.style.display = 'none';
                originalUrlInput.disabled = true;
                originalUrlInput.required = false;

                section.style.display = 'block';
                formInputs.forEach(el => el.required = true);

                // Tambahkan satu baris jika kosong saat toggle diaktifkan
                if (document.getElementById('edit-multi-redirect-form').children.length === 0) {
                     editAddRedirectRow();
                }

            } else {
                if (originalUrlGroup) originalUrlGroup.style.display = 'block';
                originalUrlInput.disabled = false;
                originalUrlInput.required = true;
                // Mengembalikan nilai Original URL yang tersimpan
                originalUrlInput.value = originalUrlInput.dataset.originalValue || '';

                section.style.display = 'none';
                formInputs.forEach(el => el.required = false);
            }
            editCalculateTotalPercentage();
        }

        // **Listener untuk Checkbox Multi Redirect di modal Edit**
        document.getElementById('editSetMultiRedirect').addEventListener('change', editToggleMultiRedirect);

        // **Listener untuk tombol Add Redirect di modal Edit**
        const editMultiRedirectForm = document.getElementById('edit-multi-redirect-form');
        const modalEdit = document.getElementById('modalEdit');
        if (modalEdit) {
             modalEdit.addEventListener('click', function(e) {
                 // Menggunakan class yang sama dengan modal Create
                 if (e.target.closest('.add-multi-redirect')) {
                     e.preventDefault();
                     editAddRedirectRow();
                 }
             });
             // Event delegation untuk menangani input percentage di edit modal
             editMultiRedirectForm.addEventListener('input', function(e) {
                 if (e.target.classList.contains('edit-percentage-input')) {
                     editCalculateTotalPercentage();
                 }
             });
        }


        // --- FUNGSI OPEN EDIT DENGAN LOGIKA MULTI REDIRECT BARU ---
        function openEdit(dataID) {
            $('#modalEdit').modal('show');
            $('#edit_images_background').val('');
            $('#edit-multi-redirect-form').empty();
            $('#edit-original-url-group').show();
            $('#edit_original_url')
                .prop('disabled', false)
                .prop('required', true)
                .val('')
                .data('originalValue', '');
            $('#editSetMultiRedirect').prop('checked', false);
            $('#edit-multi-redirect-section').hide();
            $('#editSetUTM').prop('checked', false);
            $('#edit-utm-section').hide();
            $('#edit_utm_campaign, #edit_utm_source, #edit_utm_medium, #edit_utm_content, #edit_utm_term').val('');
            $('#edit_tags').val('');

            // Menggunakan JQuery untuk konsistensi pada input URL
            const $originalUrlInput = $('#edit_original_url');
            const multiRedirectCheckbox = $('#editSetMultiRedirect');


            let url = `{{ route('view.get-dataById', ['id' => 'dataID']) }}`;
            url = url.replace('dataID', dataID);

            axios.get(url)
                .then(res => {
                    const data = res.data;

                    $('#edit_data_id').val(data.id);
                    $('#edit_campaign_name').val(data.campaign_name);

                    // PERBAIKAN: Menggunakan .val() untuk textarea
                    $('#edit_description').val(data.description || '');

                    // PERBAIKAN LOAD CUSTOM FIELDS
                    $('#edit_custom_title').val(data.custom_title || '');
                    $('#edit_custom_description').val(data.custom_description || '');

                    // ----------------------------------------------------
                    // LOGIKA PENGISIAN MULTI REDIRECT DAN ORIGINAL URL
                    // ----------------------------------------------------
                    const redirects = Array.isArray(data.redirects) ? data.redirects :
                        (Array.isArray(data.pop_unders) ? data.pop_unders :
                            (Array.isArray(data.short_links_pop_unders) ? data.short_links_pop_unders : []));
                    const isMultiRedirect = Boolean(data.is_multi_redirect) || (data.is_popunder === 'yes') ||
                        redirects.length > 0;
                    const originalUrlValue = isMultiRedirect ? '' : (data.original_url_value || data.original_url || '');
                    multiRedirectCheckbox.prop('checked', isMultiRedirect);

                    if (isMultiRedirect) {
                        // Load data multi redirect
                        if (redirects.length > 0) {
                            redirects.forEach(redirect => {
                                editAddRedirectRow(redirect.url, redirect.precentage ?? redirect.percentage ?? '');
                            });
                        }
                    }

                    // **PERBAIKAN LOAD ORIGINAL URL:** Setel nilai URL dan simpan ke data-attribute
                    // Menggunakan variabel $originalUrlInput (jQuery object)
                    $originalUrlInput.val(originalUrlValue).data('originalValue', originalUrlValue);


                    // Panggil toggle untuk menampilkan/menyembunyikan section dan mengatur required
                    editToggleMultiRedirect();
                    // ----------------------------------------------------


                    const labels = Array.isArray(data.labels) ? data.labels.filter(Boolean) : [];
                    if (labels.length > 0) {
                        var allLabels = labels.map(function(label) {
                            return label.label_name;
                        }).filter(Boolean);
                        $('#edit_tags').val(allLabels.join(','));
                    }
                    // Hancurkan dan buat ulang instance Choices (PENTING untuk tags)
                    if (window.editTagsChoicesInstance) {
                        window.editTagsChoicesInstance.destroy();
                        window.editTagsChoicesInstance = null;
                    }
                    // Pastikan Anda mendapatkan elemen DOM, bukan objek jQuery
                    const editTagsElement = document.getElementById('edit_tags');
                    window.editTagsChoicesInstance = new Choices(editTagsElement, {
                        delimiter: ',',
                        editItems: true,
                        maxItemCount: 5,
                        removeItemButton: true
                    });


                    // Logika UTM
                    const hasUtm = data.utm_campaign || data.utm_source || data.utm_medium || data.utm_content || data.utm_term;

                    if (isMultiRedirect) {
                        // Jika Multi Redirect aktif, abaikan UTM
                        $('#editSetUTM').prop('checked', false);
                    } else {
                        // Cek berdasarkan data UTM yang ada
                        $('#editSetUTM').prop('checked', !!hasUtm);

                        if (hasUtm) {
                            $('#edit-utm-section').show();
                            $('#edit_utm_campaign').val(data.utm_campaign || '');
                            $('#edit_utm_source').val(data.utm_source || '');
                            $('#edit_utm_medium').val(data.utm_medium || '');
                            $('#edit_utm_content').val(data.utm_content || '');
                            $('#edit_utm_term').val(data.utm_term || '');
                        } else {
                             $('#edit-utm-section').hide();
                             $('#edit_utm_campaign').val('');
                             $('#edit_utm_source').val('');
                             $('#edit_utm_medium').val('');
                             $('#edit_utm_content').val('');
                             $('#edit_utm_term').val('');
                        }
                    }

                    $('#edit_result_link').val(data.short_url);
                    $('.alt-domain').each(function(index, altDomain) {
                        $(altDomain).val($(altDomain).data('domain') + '/' + data.short_url);
                    });
                })
                .catch(err => {
                    console.error('terjadi kesalahan', err);
                });
        }

        // --- 2. Perbarui Fungsi Form Submit (#formEdit) ---
        $('#formEdit').submit(function(e) {
            e.preventDefault();

            const multiRedirectCheckbox = document.getElementById('editSetMultiRedirect');
            const multiRedirectForm = document.getElementById('edit-multi-redirect-form');

            // Validasi Percentage 100% jika Multi Redirect aktif
            if (multiRedirectCheckbox && multiRedirectCheckbox.checked) {
                const total = editCalculateTotalPercentage();

                if (multiRedirectForm.children.length === 0) {
                    notyf.error('Multi Redirect diaktifkan, namun belum ada Link yang ditambahkan.');
                    return;
                }

                if (total !== 100) {
                    let message = total < 100 ?
                        `Total percentage kurang dari 100%! Total saat ini: ${total}%` :
                        `Total percentage melebihi 100%! Total saat ini: ${total}%`;

                    notyf.error(message);
                    return;
                }
            }

            $('.custom-preload').show().addClass('d-flex');

            // Gunakan FormData untuk mengirim data, termasuk file dan array Multi Redirect
            const formData = new FormData(this);
            formData.append('_method', 'PATCH');

            let url = `{{ route('project_analytic.udpate') }}`;

            axios.post(url, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                .then(res => {
                    console.log(res.data);
                    $('.custom-preload').hide().removeClass('d-flex');
                    notyf.success(res.data.message);
                    reloadAfterEdit = true;
                })
                .catch(err => {
                    console.error('Terjadi kesalahan', err.response.data);
                    $('.custom-preload').hide().removeClass('d-flex');
                    let errorMessage = err.response.data.message || 'An error occurred during update.';

                    // Tambahkan penanganan error validation dari Laravel (misalnya total percentage error)
                    if (err.response.data.errors) {
                        // Cek apakah ada error spesifik dari backend (misalnya validasi 100% dari controller)
                        errorMessage = Object.values(err.response.data.errors)[0][0] || errorMessage;
                    }

                    notyf.error(errorMessage);
                });
        });
    </script>
    <script>
        function deleteData(dataID) {
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
                    axios.delete(`{{ route('project_analytic.delete') }}`, {
                            data: data
                        })
                        .then(res => {
                            Swal.fire({
                                    title: "Deleted!",
                                    text: "Your data has been deleted.",
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

            try {
                let searchParam = new URL(urlString);
                $('#utm_campaign').val(getQueryParamValue(searchParam, 'utm_campaign'));
                $('#utm_source').val(getQueryParamValue(searchParam, 'utm_source'));
                $('#utm_content').val(getQueryParamValue(searchParam, 'utm_content'));
                $('#utm_term').val(getQueryParamValue(searchParam, 'utm_term'));
                $('#utm_medium').val(getQueryParamValue(searchParam, 'utm_medium'));
            } catch (e) {
                // Ignore if URL is invalid/incomplete during typing
            }
        });

        // **Listener untuk keyup original_url di modal Edit**
        $('#edit_original_url').keyup(function() {
            let urlString = $(this).val();
            let hasQuery = urlString.indexOf('?') !== -1;
            $('#editSetUTM').prop('checked', hasQuery);
            if (hasQuery) {
                $('#edit-utm-section').show();
            } else {
                $('#edit-utm-section').hide();
            }

            try {
                let searchParam = new URL(urlString);
                $('#edit_utm_campaign').val(getQueryParamValue(searchParam, 'utm_campaign'));
                $('#edit_utm_source').val(getQueryParamValue(searchParam, 'utm_source'));
                $('#edit_utm_content').val(getQueryParamValue(searchParam, 'utm_content'));
                $('#edit_utm_term').val(getQueryParamValue(searchParam, 'utm_term'));
                $('#edit_utm_medium').val(getQueryParamValue(searchParam, 'utm_medium'));
            } catch (e) {
                // Ignore if URL is invalid/incomplete during typing
            }
        });


        function addQueryParam(utm_key, utm_value) {
            let urlString = $('#original_url').val();

            try {
                let urlObject = new URL(urlString); // Parse the URL

                // Remove existing occurrences of utm_campaign parameter
                urlObject.searchParams.delete(utm_key);

                // Add the new utm_campaign parameter
                if (utm_value) {
                    urlObject.searchParams.append(utm_key, utm_value);
                }

                // Set the modified URL back to the input field
                $('#original_url').val(urlObject.toString());
            } catch (error) {
                // alert("Original URL belum diisi"); // Menghapus alert agar tidak mengganggu
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
                if (utm_value) {
                    urlObject.searchParams.append(utm_key, utm_value);
                }

                // Set the modified URL back to the input field
                $('#edit_original_url').val(urlObject.toString());
            } catch (error) {
                // alert("Original URL belum diisi"); // Menghapus alert agar tidak mengganggu
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
