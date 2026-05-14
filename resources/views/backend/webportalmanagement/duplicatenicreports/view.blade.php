@extends('layouts.vertical', ['pageTitle' => 'Duplicate NIC Complaints View'])

@section('css')
@endsection

@section('content')

<div class="mb-2 d-flex justify-content-between align-items-center">

    <h1 class="subheader-title">
        <span>{{ $mainTitle }}</span>
    </h1>

    <div>
        <a href="{{ route('nic-duplicate-records.nic-duplicate-record-list') }}" class="btn btn-sm btn-outline-info">
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
                <h2>
                    View <span class="fw-300"><i>{{ $title }}</i></span>
                </h2>
            </div>
            <div class="panel-container show">
                <div class="panel-content">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">National Id Number </label>
                            <input class="form-control" type="text" name="national_id_number" value="{{ $complaintDetail['national_id_number'] }}" readonly/>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Email </label>
                            <input class="form-control" type="text" name="email" value="{{ $complaintDetail['email'] }}" readonly/>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Contact Number </label>
                            <input class="form-control" type="text" name="contact_number" value="{{ $complaintDetail['country_code'].' '.$complaintDetail['mobile_number'] }}" readonly/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Complaint </label>
                            <textarea class="form-control" rows="3" name="comment" readonly>{{ $complaintDetail['comment'] }}</textarea>
                        </div>
                    </div>

                    <h4>
                        Feedback
                    </h4>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Feedback </label>
                            <textarea class="form-control" rows="3" name="feedback" data-parsley-minlength="3" data-parsley-trigger="change" data-parsley-required-message="Feedback is required" data-parsley-minlength-message="Feedback must be at least 3 characters" data-parsley-required="true" readonly>{{ $complaintDetail['feedback'] }}</textarea>
                        </div>
                    </div>

                    <div class="panel-content border-faded border-start-0 border-end-0 border-bottom-0 d-flex flex-row">
                        <a href="{{ route('nic-duplicate-records.nic-duplicate-record-list') }}"><button class="btn btn-primary ms-auto" type="button">Back </button></a>
                    </div>
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
