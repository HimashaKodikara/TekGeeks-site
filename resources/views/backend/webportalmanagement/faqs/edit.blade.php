@extends('layouts.vertical', ['pageTitle' => 'FAQ'])

@section('css')
@endsection

@section('content')

<div class="mb-2 d-flex justify-content-between align-items-center">

    <h1 class="subheader-title">
        <span>{{ $mainTitle }}</span>
    </h1>

    <div>
        <a href="{{ route('faqs.faq-list') }}" class="btn btn-sm btn-outline-info">
            <i class="fa fa-list me-1"></i> View All
        </a>
    </div>
</div>

<div class="row">
    @if ($message = Session::get('success'))
    <div class="alert alert-success alert-dismissible fade show col-12" role="alert">
        {{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show col-12" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="panel panel-icon" id="panel-1">
            <div class="panel-hdr">
                {{-- <h2>
                    Edit <span class="fw-300"><i>{{ $title }}</i></span>
                </h2> --}}
            </div>
            <div class="panel-container show">
                <div class="panel-content">
                    <form id="faq-form" action="{{ route('faqs.update', ['id' => encrypt($faq->id)]) }}" method="POST" enctype="multipart/form-data" data-parsley-validate novalidate>
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="_from" value="{{ url()->current() }}">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Heading Name (English) <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="heading_en" value="{{ $faq->heading_en }}" data-parsley-maxlength="250" data-parsley-minlength="3" data-parsley-trigger="change" data-parsley-required-message="Heading name (English) is required" data-parsley-minlength-message="Heading name (English) must be at least 3 characters" data-parsley-required="true" />
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Heading Name (Sinhala) <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="heading_si" value="{{ $faq->heading_si }}" data-parsley-maxlength="250" data-parsley-minlength="3" data-parsley-trigger="change" data-parsley-required-message="Heading name (Sinhala) is required" data-parsley-minlength-message="Heading name (Sinhala) must be at least 3 characters" data-parsley-required="true" />
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Heading Name (Tamil) <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="heading_ta" value="{{ $faq->heading_ta }}" data-parsley-maxlength="250" data-parsley-minlength="3" data-parsley-trigger="change" data-parsley-required-message="Heading name (Tamil) is required" data-parsley-minlength-message="Heading name (Tamil) must be at least 3 characters" data-parsley-required="true" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Icon <span class="text-danger">(Max File Size: 10MB)</span></label>
                                <input class="form-control" id="customFile" type="file" name="icon_path" accept=".png" data-parsley-max-file-size="10240"/>

                                @if(isset($faq->icon_path))
                                    <img src="{{ asset('storage/'.$faq->icon_path) }}" alt="FAQ Icon" style="max-width: 100px; max-height: 100px;">
                                @endif
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Display Order </label>
                                <input class="form-control" id="display_order" name="display_order" placeholder="Display Order" step="1" min="0" type="number" value="{{ $faq->display_order }}" data-parsley-type="integer" data-parsley-type-message="Enter a valid whole number" />
                            </div>
                        </div>

                        <h2>
                            Descriptions
                        </h2>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Description (English) <span class="text-danger">*</span></label>
                                <textarea id="description_en" name="description_en" class="form-control">{{ $faq->description_en }}</textarea>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Description (Sinhala) <span class="text-danger">*</span></label>
                                <textarea id="description_si" name="description_si" class="form-control">{{ $faq->description_si }}</textarea>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Description (Tamil) <span class="text-danger">*</span></label>
                                <textarea id="description_ta" name="description_ta" class="form-control">{{ $faq->description_ta }}</textarea>
                            </div>
                        </div>

                        <h2>
                            Detail Page Contents
                        </h2>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Detail Page Content (English) </label>
                                <textarea id="detail_description_en" name="detail_description_en" class="form-control">{{ $faq->detail_description_en }}</textarea>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Detail Page Content (Sinhala) </label>
                                <textarea id="detail_description_si" name="detail_description_si" class="form-control">{{ $faq->detail_description_si }}</textarea>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Detail Page Content (Tamil) </label>
                                <textarea id="detail_description_ta" name="detail_description_ta" class="form-control">{{ $faq->detail_description_ta }}</textarea>
                            </div>
                        </div>

                        <input type="hidden" id="id" name="id" value="{{ encrypt($faq->id) }}">

                        <div class="panel-content border-faded border-start-0 border-end-0 border-bottom-0 d-flex flex-row">
                            <button id="faq-create" class="btn btn-primary ms-auto" type="submit">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function() {
    const $displayOrder = $('#display_order');
    const parsleyField = $displayOrder.parsley();
    const submitButton = $('#faq-create');
    const ignoreId = $('#id').val();

    // Enable/disable submit button
    function toggleSubmitButton(enabled) {
        submitButton.prop('disabled', !enabled);
    }

    // Debounce helper – wait for user to pause typing
    function debounce(fn, delay) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => fn.apply(this, args), delay);
        };
    }

    // AJAX check function
    function checkDisplayOrder() {
        parsleyField.removeError('displayOrderExist');

        const value = $.trim($displayOrder.val());

        // If empty, it's valid (nullable)
        if (value === '') {
            toggleSubmitButton(true);
            return;
        }

        // Check Parsley base rules first (only if not empty)
        if (!parsleyField.isValid()) {
            toggleSubmitButton(false);
            return;
        }

        // Send AJAX request to your backend route
        $.post("{{ route('faqs.check-display-order-existency') }}", {
                _token: '{{ csrf_token() }}'
                , display_order: value
            })
            .done(function(res) {
                // Remove any previous duplicate error
                parsleyField.removeError('displayOrderExist');

                if (res.exists) {
                    // ✅ Show message but DO NOT disable submit
                    parsleyField.addError('displayOrderExist', {
                        message: 'This display order number is already in use. Adding it again will create a duplicate.'
                        , updateClass: true
                    });
                }

                // Keep submit enabled unless Parsley found other issues
                toggleSubmitButton(true);
            })
            .fail(function() {
                // On network/server error, keep button disabled until valid check passes again
                toggleSubmitButton(true);
            });
    }

    // ✅ Trigger the check while typing (debounced)
    $displayOrder.on('input', debounce(checkDisplayOrder, 400));

    // Also check on blur (backup)
    $displayOrder.on('blur', checkDisplayOrder);

    // Clear message when typing
    $displayOrder.on('input', function() {
        parsleyField.removeError('displayOrderExist');
    });

    // Start enabled
    toggleSubmitButton(true);

    $('#description_en').summernote({
        placeholder: 'Enter description (English)...',
        height: 200,
        toolbar: [
            ['style', ['bold', 'italic', 'underline']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link']],
            ['view', ['fullscreen', 'codeview']]
        ]
    });

    $('#description_si').summernote({
        placeholder: 'Enter description (Sinhala)...',
        height: 200,
        toolbar: [
            ['style', ['bold', 'italic', 'underline']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link']],
            ['view', ['fullscreen', 'codeview']]
        ]
    });

    $('#description_ta').summernote({
        placeholder: 'Enter description (Tamil)...',
        height: 200,
        toolbar: [
            ['style', ['bold', 'italic', 'underline']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link']],
            ['view', ['fullscreen', 'codeview']]
        ]
    });

    $('#detail_description_en').summernote({
        placeholder: 'Enter detail page content (English)...',
        height: 200,
        toolbar: [
            ['style', ['bold', 'italic', 'underline']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link']],
            ['view', ['fullscreen', 'codeview']]
        ]
    });

    $('#detail_description_si').summernote({
        placeholder: 'Enter detail page content (Sinhala)...',
        height: 200,
        toolbar: [
            ['style', ['bold', 'italic', 'underline']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link']],
            ['view', ['fullscreen', 'codeview']]
        ]
    });

    $('#detail_description_ta').summernote({
        placeholder: 'Enter detail page content (Tamil)...',
        height: 200,
        toolbar: [
            ['style', ['bold', 'italic', 'underline']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link']],
            ['view', ['fullscreen', 'codeview']]
        ]
    });
})();
</script>
@endsection
