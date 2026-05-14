@extends('layouts.master')

@section('title', 'Create Garage')

@section('content')
    <!-- the #js-page-content id is needed for some plugins to initialize -->
    <main id="js-page-content" role="main" class="page-content">

        <div class="subheader">
            <h1 class="subheader-title">
                <i class='subheader-icon fal fa-chart-area'></i> Update Ambulance <span class='fw-300'></span>
            </h1>

            <div class="row" style="margin-left:auto; margin-right:auto; gap: 12px">
                <a href=" {{ route('ambulance.create') }}">
                    <button type="button" class="btn btn-lg btn-primary">
                        <span class="mr-1 fal fa-plus"></span>
                        Add New
                    </button>
                </a>
                <a href=" {{ route('ambulance.index') }}">
                    <button type="button" class="btn btn-lg btn-primary">
                        <span class="mr-1 fal fa-list"></span>
                        View All
                    </button>
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <div id="panel-1" class="panel">
                    <div class="panel-hdr">
                        <h2>
                            Update <span class="fw-300"><i>ambulance</i></span>
                        </h2>
                        <div class="panel-toolbar">
                            <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip"
                                data-offset="0,10" data-original-title="Collapse"></button>
                            <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip"
                                data-offset="0,10" data-original-title="Fullscreen"></button>
                        </div>
                    </div>
                    <div class="panel-container show">
                        <div class="panel-content">

                            <form action="{{ route('ambulance.update', $ambulance->id) }}" enctype="multipart/form-data"
                                method="post" id="user-form" class="smart-form row" autocomplete="off"
                                data-parsley-validate>
                                @csrf
                                @method('put')
                                <div class="mb-3 col-6">
                                    <label class="form-label" for="locationId">Location <span
                                            style="color: red">*</span></label>
                                    <select class="form-control select2" id="locationId" name="locationId" required>
                                        <option value="" disabled {{ old('locationId') ? '' : 'selected' }}>Select
                                            Location</option>
                                        @if (isset($locations) && count($locations) > 0)
                                            @foreach ($locations as $location)
                                                <option value="{{ $location->id }}"
                                                    @if (isset($ambulance) && $ambulance->location_id == $location->id) selected @endif>
                                                    {{ $location->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <div class="invalid-feedback">Location is required, you missed this one.</div>
                                    @error('locationId')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3 col-6">
                                    <label class="form-label" for="ambulanceCategory">Ambulance Types <span
                                            style="color: red">*</span></label>
                                    <select class="form-control select2" id="ambulanceCategory" name="ambulanceCategory"
                                        required>
                                        <option value="" disabled {{ old('ambulanceCategory') ? '' : 'selected' }}>
                                            Select Ambulance Types</option>
                                        @if (isset($ambulanceCategories) && count($ambulanceCategories) > 0)
                                            @foreach ($ambulanceCategories as $ambulanceCategory)
                                                <option value="{{ $ambulanceCategory->id }}"
                                                    @if (isset($ambulance) && $ambulance->ambulance_type_id == $ambulanceCategory->id) selected @endif>
                                                    {{ $ambulanceCategory->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <div class="invalid-feedback">Police Station Types is required, you missed this one.
                                    </div>
                                    @error('ambulanceCategory')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3 col-6">
                                    <div class="form-group">
                                        <label class="form-label" for="simpleinput">Name <span
                                                style="color: red">*</span></label>
                                        <input type="text" id="name" name="name" class="form-control"
                                            autocomplete="off"
                                            @if (isset($ambulance->name)) value="{{ $ambulance->name }}" @endif
                                            required>
                                        <div class="invalid-feedback">Name is required, you missed this one.</div>
                                        @error('name')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3 col-6">
                                    <div class="form-group">
                                        <label class="form-label" for="example-email-2">Contact No <span
                                                style="color: red">*</span></label>
                                        <input type="tel" id="contactNo" name="contactNo" class="form-control" pattern="[\d\s\-\+\(\)]{10,15}"
                                            @if (isset($ambulance->contact_no)) value="{{ $ambulance->contact_no }}" @endif
                                            required>
                                        <div class="invalid-feedback">Contact no is required, you missed this one.</div>
                                        @error('contactNo')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>


                                <div class="col-12">
                                    <div
                                        class="flex-row panel-content border-faded border-left-0 border-right-0 border-bottom-0 d-flex">
                                        <button id="js-submit-btn" class="ml-auto btn btn-primary" type="submit">Submit
                                            form</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@stop
@section('footerScript')


    <script>
        $(document).ready(function() {
            $('select').select2({
                matcher: function(params, data) {
                    if ($.trim(params.term) === '') {
                        return data;
                    }

                    var term = params.term.toUpperCase();
                    var text = (data.text || '').toUpperCase();
                    var value = (data.id || '').toUpperCase();

                    if (!text && !value) {
                        return null; 
                    }

                    var words = text.split(/\s+/);
                    
                    var wordStartMatch = words.some(word => word.startsWith(term));
                    var startsWith = text.startsWith(term) || value.startsWith(term);

                    if (startsWith) {
                        return $.extend({}, data, { priority: 1 }); 
                    } else if (wordStartMatch) {
                        return $.extend({}, data, { priority: 2 }); 
                    }

                    return null; // Exclude all other matches
                },
                sorter: function(results) {
                    return results.sort((a, b) => (a.priority || 2) - (b.priority || 2));
                }
            });


            var contactNoRegex = /^[\d\s\-\+\(\)]{10,15}$/;


            function validateField(field, regex, emptyMessage, formatMessage) {
                var value = field.val().trim();
                var errorDiv = field.siblings(".invalid-feedback");

                if (!errorDiv.length) {
                    field.after('<div class="invalid-feedback"></div>');
                    errorDiv = field.siblings(".invalid-feedback");
                }

                if (value === '') {
                    errorDiv.text(emptyMessage).show();
                    field.addClass("is-invalid").removeClass("is-valid");
                } else if (!regex.test(value)) {
                    errorDiv.text(formatMessage).show();
                    field.addClass("is-invalid").removeClass("is-valid");
                } else {
                    errorDiv.hide();
                    field.removeClass("is-invalid").addClass("is-valid");
                }
            }

            $("#contactNo").on('input', function() {
                validateField($(this), contactNoRegex, "Contact number is required.",
                    "Contact number must be 10-15 digits.");
            });



            $("#js-submit-btn").click(function (event) {
                var form = $("#user-form");
                var isValid = true;

                form.find('.form-control').each(function () {
                    var field = $(this);
                    if (field.hasClass("is-invalid")) {
                        isValid = false;
                    }
                });

                if (!isValid || form[0].checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                } else {
                    form.submit();
                }

                form.addClass('was-validated');
            });
        });
    </script>

@stop
