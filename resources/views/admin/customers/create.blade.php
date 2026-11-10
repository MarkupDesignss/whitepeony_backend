@extends('admin.layouts.app')
@section('title', 'Create Customer')

@section('content')
<div class="container-fluid mt-4">

    {{-- Page Header --}}
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0 fw-bold">Add Customer</h3>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-1"></i>Back to Customers
        </a>
    </div>


    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> Please fix the errors below.
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.customers.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Customer Details --}}
         <div class="card mb-4">
            <h3 class="card-header">Customer Details</h3>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control"
                               value="{{ old('email') }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control"
                               value="{{ old('name') }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control"
                               value="{{ old('phone') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Customer Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" required>
                            <option value="">Select Type</option>
                            <option value="b2b" {{ old('type') == 'b2b' ? 'selected' : '' }}>B2B</option>
                            {{-- Future use --}}
                            {{-- <option value="b2c">B2C</option> --}}
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Profile Image</label>
                        <input type="file" name="profile_image" class="form-control">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Business Name</label>
                        <input type="text" name="business_name" class="form-control"
                               value="{{ old('business_name') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">VAT ID</label>
                        <input type="text" name="vat_id" class="form-control"
                               value="{{ old('vat_id') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                 </div>
            </div>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i>Save Customer</button>
                <a href="{{ route('admin.customers.index') }}" 
                class="btn btn-danger ms-2">
                    Cancel
                </a>
        </div>
    </form>
</div>

@endsection
