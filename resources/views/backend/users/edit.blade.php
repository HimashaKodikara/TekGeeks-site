@extends('layouts.vertical', ['pageTitle' => 'User'])

@section('css')
@endsection

@section('content')

<div class="mb-2 d-flex justify-content-between align-items-center">

    <h1 class="subheader-title">
        <span>Users</span>
    </h1>

    <div>
        <a href="{{ route('users.create') }}" class="btn btn-sm btn-success">
            <i class="fa fa-plus me-1"></i> Create New
        </a>
        <a href="{{ route('users.users-list') }}" class="btn btn-sm btn-outline-info">
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
                    Edit <span class="fw-300"><i>User</i></span>
                </h2>
            </div>
            <div class="panel-container show">
                <div class="panel-content">
                    <form id="user-form" action="{{ route('users.update') }}" method="POST" data-parsley-validate novalidate>
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="_from" value="{{ url()->current() }}">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Name <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="name" value="{{ $user->name }}" data-parsley-maxlength="150" data-parsley-minlength="3" data-parsley-trigger="change" data-parsley-required-message="Name is required" data-parsley-minlength-message="Name must be at least 3 characters" data-parsley-required="true"/>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input class="form-control" id="email" name="email" placeholder="Email" type="email" value="{{ $user->email }}" data-parsley-maxlength="150" data-parsley-type="email" data-parsley-required-message="Email is required" data-parsley-type-message="Enter a valid email" data-parsley-required="true" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">NIC <span class="text-danger">*</span></label>
                                <input class="form-control" id="nic" name="nic" placeholder="NIC" type="text" value="{{ $user->nic }}" data-parsley-maxlength="12" data-parsley-required="true" data-parsley-required-message="NIC is required" />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Contact Number <span class="text-danger">*</span></label>
                                <input class="form-control" name="contact_number" placeholder="Contact Number" type="text" value="{{ $user->contact_number }}" data-parsley-maxlength="12" data-parsley-required="true" data-parsley-required-message="Contact number is required" />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="validationCustom04">Role <span class="text-danger">*</span></label>
                                <select class="form-select" name="roles" data-parsley-required="true" data-parsley-required-message="User role is required">
                                    @foreach ($roles as $x => $val)
                                    <option value="{{ $val }}" {{ $userRole == $val ? 'selected' : '' }}>{{ $val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label">Do you want to change the password?</label>
                                    <button id="changepwyes" type="button" class="btn btn-success ms-2">Yes</button>
                                    <button id="changepwno" type="button" class="btn btn-danger ms-2">No </button>

                            </div>
                        </div>

                        <div class="row" id="changepassword" style="display: none">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password <span class="text-danger">*</span></label>

                                <div class="password-wrapper position-relative">
                                    <input class="form-control" id="password" type="password" name="password" value="" data-parsley-strong-password />

                                    <button type="button" class="toggle-password btn p-0 border-0 bg-transparent" aria-label="Toggle password visibility">
                                        <i class="fal fa-eye"></i>
                                    </button>
                                </div>

                                <div id="password-error"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Confirm Password <span class="text-danger">*</span></label>

                                <div class="password-wrapper position-relative">
                                    <input class="form-control" type="password" name="confirm_password" value="" data-parsley-equalto="#password" data-parsley-errors-container="#confirm-password-error" data-parsley-maxlength="191" data-parsley-required-message="Confirm password is required" />

                                    <button type="button" class="toggle-password btn p-0 border-0 bg-transparent" aria-label="Toggle password visibility">
                                        <i class="fal fa-eye"></i>
                                    </button>
                                </div>

                                <div id="confirm-password-error"></div>
                            </div>
                        </div>

                        <input type="hidden" name="id" value="{{ encrypt($user->id) }}">

                        <div class="panel-content border-faded border-start-0 border-end-0 border-bottom-0 d-flex flex-row">
                            <button id="user-submit" class="btn btn-primary ms-auto" type="submit">Update</button>
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
  (function () {
    // Add validator once (in case of partial reloads)
    if (window.Parsley && !window.__strongPasswordOnce) {
      window.__strongPasswordOnce = true;
      window.Parsley.addValidator('strongPassword', {
        requirementType: 'string',
        validateString: function (value) {
          return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/.test(value);
        },
        messages: {
          en: 'Password must be 8+ chars, contain uppercase, lowercase, number, and special character.'
        }
      });
    }

    // Toggle handler (delegated so it works for both fields)
    $(document).on('click', '.toggle-password', function () {
      const $btn   = $(this);
      const $input = $btn.closest('.password-wrapper').find('input').first();
      const isPwd  = $input.attr('type') === 'password';

      $input.attr('type', isPwd ? 'text' : 'password');
      $btn.find('i').toggleClass('fa-eye fa-eye-slash');

      // Keep focus and caret at end for better UX
      const val = $input.val();
      $input.trigger('focus');
      if ($input[0].setSelectionRange) {
        $input[0].setSelectionRange(val.length, val.length);
      }
    });
    // Show/hide "change password" section
      $('#changepwyes').click(function () {
        $('#changepassword').show();
        $('.password, .confirmpassword')
          .prop('disabled', false)
          .attr('required', true)
          .attr('data-parsley-required', true)
          .attr('data-parsley-maxlength', '191')
          .attr('data-parsley-strong-password', '')
          .attr('data-parsley-required-message', 'Password is required')
          .attr('data-parsley-minlength-message', 'Password must be at least 6 characters');
      });

      $('#changepwno').click(function () {
        $('#changepassword').hide();
        $('.password, .confirmpassword')
          .prop('disabled', true)
          .removeAttr('required data-parsley-required')
          .val('');
      });

       // -----------------------------
    // Availability checks + helpers
    // -----------------------------
    let emailValid = true;
    let nicValid   = true;

    function toggleSubmitButton() {
      const disabled = !(emailValid && nicValid);
      $('#user-submit').prop('disabled', disabled);
    }

    // Helper: safely post with CSRF
    function postJSON(url, data, onDone, onFail) {
      $.post(url, data)
        .done(onDone)
        .fail(onFail || function () {
          // On network/server error, fail safe: block submit until next valid check
          emailValid = false;
          nicValid   = false;
          toggleSubmitButton();
        });
    }

    // Current user id (to exclude them from "unique" check server-side)
    const currentUserId = $('#userId').val();

    // ✅ Email availability check (on blur)
    $('#email').on('blur', function () {
      const field = $(this).parsley();
      field.removeError('emailExists');

      // Show built-in errors first (required/format)
      if (!field.isValid()) {
        emailValid = false;
        toggleSubmitButton();
        return;
      }

      postJSON("{{ route('users.check-email-availability') }}", {
        _token: '{{ csrf_token() }}',
        email: $(this).val(),
        id: currentUserId // so the endpoint can ignore the current record
      }, function (res) {
        field.removeError('emailExists');
        if (res && res.exists) {
          field.addError('emailExists', {
            message: 'This email is already registered.',
            updateClass: true
          });
          emailValid = false;
        } else {
          emailValid = true;
        }
        toggleSubmitButton();
      });
    });

    // ✅ NIC availability check (on blur)
    $('#nic').on('blur', function () {
      const field = $(this).parsley();
      field.removeError('nicExists');

      // Show built-in errors first (required/format)
      if (!field.isValid()) {
        nicValid = false;
        toggleSubmitButton();
        return;
      }

      postJSON("{{ route('users.check-nic-availability') }}", {
        _token: '{{ csrf_token() }}',
        nic: $(this).val(),
        id: currentUserId // so the endpoint can ignore the current record
      }, function (res) {
        field.removeError('nicExists');
        if (res && res.exists) {
          field.addError('nicExists', {
            message: 'This NIC is already registered.',
            updateClass: true
          });
          nicValid = false;
        } else {
          nicValid = true;
        }
        toggleSubmitButton();
      });
    });

    // Initialize state once on page load
    toggleSubmitButton();

  })();
</script>
@endsection
