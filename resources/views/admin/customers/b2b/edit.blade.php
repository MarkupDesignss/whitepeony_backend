@extends('admin.layouts.app')

@section('content')
<div class="container mt-4">
    <!--<h2 class="mb-4">Edit Customer</h2>-->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0 fw-bold">Edit Customer</h3>
        <a href="{{ route($customer->type === 'b2b' ? 'admin.customers.b2b' : 'admin.customers.b2c') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-1"></i>Back to List
        </a>
    </div>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

    <form action="{{ route('admin.customers.update', $customer->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="card mb-4">
            <h3 class="card-header">Customer Details</h3>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required value="{{ old('email', $customer->email) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" required value="{{ old('name', $customer->name) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $customer->phone) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Type</label>
                        <select name="type" class="form-control" required>
                            <option value="">-- Select Type --</option>
                            <option value="b2b" {{ old('type', $customer->type) == 'b2b' ? 'selected' : '' }}>B2B</option>
                            {{-- <option value="b2c" {{ old('type', $customer->type) == 'b2c' ? 'selected' : '' }}>B2C</option> --}}
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Profile Image</label>
                        <input type="file" name="profile_image" class="form-control">
                        @if ($customer->profile_image)
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $customer->profile_image) }}" alt="Profile" width="100" class="rounded">
                            </div>
                        @endif
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Business Name</label>
                        <input type="text" name="business_name" class="form-control" value="{{ old('business_name', $customer->business_name) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>VAT ID</label>
                        <input type="text" name="vat_id" class="form-control" value="{{ old('vat_id', $customer->vat_id) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Tier</label>
                        <select name="tier_id" class="form-control form-select">
                            <option value="1" {{ old('tier_id', $customer->tier_id) == 1 ? 'selected' : '' }}>Browns</option>
                            <option value="2" {{ old('tier_id', $customer->tier_id) == 2 ? 'selected' : '' }}>Silver</option>
                            <option value="3" {{ old('tier_id', $customer->tier_id) == 3 ? 'selected' : '' }}>Gold</option>
                            <option value="4" {{ old('tier_id', $customer->tier_id) == 4 ? 'selected' : '' }}>Platinium</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6 mb-3">
    <label>Full Address</label>
    <textarea name="full_address" class="form-control" rows="3">
{{ old('full_address', $customer->address->full_address ?? '') }}
    </textarea>
</div>

<div class="col-md-6 mb-3">
    <label>Address Type</label>
    <select name="address_type" class="form-control form-select">
        <option value="">-- Select Address Type --</option>

        <option value="home"
            {{ old('address_type', $customer->address->address_type ?? '') == 'home' ? 'selected' : '' }}>
            Home
        </option>

        <option value="office"
            {{ old('address_type', $customer->address->address_type ?? '') == 'office' ? 'selected' : '' }}>
            Office
        </option>

        <option value="hotel"
            {{ old('address_type', $customer->address->address_type ?? '') == 'hotel' ? 'selected' : '' }}>
            Hotel
        </option>

        <option value="other"
            {{ old('address_type', $customer->address->address_type ?? '') == 'other' ? 'selected' : '' }}>
            Other
        </option>
    </select>
</div>

<div class="col-md-6 mb-3">
    <label>Postal Code</label>
    <input type="text"
        name="postal_code"
        class="form-control"
        value="{{ old('postal_code', $customer->address->postal_code ?? '') }}">
</div>
                </div>
            </div>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-primary">Update Customer</button>
                <a href="{{ route($customer->type === 'b2b' ? 'admin.customers.b2b' : 'admin.customers.b2c') }}" 
                class="btn btn-danger ms-2">
                    Cancel
                </a>
        </div>
    </form>
</div>
@endsection