@extends('layouts.vertical', ['pageTitle' => 'Role'])

@section('css')
@endsection

@section('content')

<div class="mb-2 d-flex justify-content-between align-items-center">

    <h1 class="subheader-title">
        <span>Roles</span>
    </h1>

    <div>
        <a href="{{ route('roles.create') }}" class="btn btn-sm btn-success">
            <i class="fa fa-plus me-1"></i> Create New
        </a>
        <a href="{{ route('roles.roles-list') }}" class="btn btn-sm btn-outline-info">
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
                    Edit <span class="fw-300"><i>Role</i></span>
                </h2>
            </div>
            <div class="panel-container show">
                <div class="panel-content">
                    <form id="user-form" action="{{ route('roles.update') }}" method="POST" enctype="multipart/form-data" data-parsley-validate novalidate>
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="_from" value="{{ url()->current() }}">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Name <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="name" value="{{ $role->name }}" data-parsley-maxlength="150" data-parsley-minlength="3" data-parsley-trigger="change" data-parsley-required-message="Name is required" data-parsley-minlength-message="Name must be at least 3 characters" data-parsley-required="true"/>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">User Manual <span class="text-danger">(Max File Size: 35MB)</span></label>
                                <input class="form-control" id="customFile" type="file" name="user_manual" accept=".pdf" data-parsley-max-file-size="35840" data-parsley-max-total-file-size="35"/>

                                @if(isset($role->user_manual))
                                <a href="{{ asset('storage/'.$role->user_manual) }}" target="_blank">
                                    <i class="bi bi-file-earmark-pdf" style="font-size: 2rem"></i>User Manual
                                </a>
                                @endif
                            </div>
                        </div>

                        <hr>
                        <div class="row">
                            @foreach($dynamicMenu as $menu)
                                @if ($menu->is_parent == 1)
                                <h5 class="mb-3">{{ $menu->title }}</h5>
                                @endif

                                @if ($menu->parent_id != 0)
                                <div class="col-md-4 mb-2 d-flex">

                                    <div id="panel-1" class="panel panel-icon  flex-grow-1">
                                        @if ($menu->parent_id != 0)
                                        <div class="panel-hdr">
                                            <h2>
                                            {{ $menu->title }}
                                            </h2>
                                            <input type="hidden" name="formid[]" value="1">
                                        </div>
                                        @endif
                                        @if ($menu->is_parent == 0 || $menu->child_order != 0)
                                        <div class="panel-container">
                                            <div class="panel-content">
                                                @foreach ($permission as $value)
                                                @if ($value->dynamic_menu_id == $menu->id)
                                                <div class="form-check mb-2">
                                                    <input type="checkbox" class="form-check-input" id="defaultUnchecked{{ $value->id }}" name="permission[]" value="{{ $value->name }}" @if (in_array($value->id, $rolePermissions)) {{ 'checked="checked"' }} @endif>
                                                    <label class="form-check-label" for="defaultUnchecked">{{ $value->name }}</label>
                                                </div>
                                                @endif
                                                @endforeach
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        </div>

                        <input type="hidden" name="id" value="{{ encrypt($role->id) }}">

                        <div class="panel-content border-faded border-start-0 border-end-0 border-bottom-0 d-flex flex-row">
                            <button class="btn btn-primary ms-auto" type="submit">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@endsection
