@extends('admin.layouts.app')
@section('title', 'Add Promo Code')

@section('content')
<div class="container-fluid mt-4">

    {{-- Page Header --}}
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0 fw-bold">Add Promo Code</h3>
        <a href="{{ route('admin.promocodes.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-1"></i>Back to List
        </a>
    </div>

    {{-- Errors --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Oops!</strong> Please fix the following errors:
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $requestedType = request()->get('type', old('customer_type', ''));
        $isB2B = $requestedType === 'b2b' || old('customer_type') === 'b2b';
    @endphp

    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <form action="{{ route('admin.promocodes.store') }}" method="POST">
                @csrf

                {{-- Basic Info --}}
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Title</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Coupon Code</label>
                        <input type="text" name="coupon_code" class="form-control" value="{{ old('coupon_code') }}">
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label fw-semibold">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                </div>

                {{-- Discount --}}
                <div class="row g-3 mt-1">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Discount Type</label>
                        <select name="discount_type" class="form-select">
                            <option value="percentage" {{ old('discount_type') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                            <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>Fixed</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Discount Value</label>
                        <input type="number" name="discount_value" class="form-control" value="{{ old('discount_value') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Max Discount</label>
                        <input type="number" name="max_discount" class="form-control" value="{{ old('max_discount') }}">
                    </div>
                </div>

                {{-- Customer Type --}}
                <div class="row g-3 mt-1">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Customer Type</label>

                        @if($requestedType)
                            <select class="form-select" disabled>
                                <option selected>{{ strtoupper($requestedType) }}</option>
                            </select>
                            <input type="hidden" name="customer_type" value="{{ $requestedType }}">
                        @else
                            <select name="customer_type" id="customer_type" class="form-select">
                                <option value="">Select</option>
                                <option value="b2b" {{ old('customer_type') == 'b2b' ? 'selected' : '' }}>B2B</option>
                                <option value="b2c" {{ old('customer_type') == 'b2c' ? 'selected' : '' }}>B2C</option>
                            </select>
                        @endif
                    </div>

                    {{-- Tier --}}
                    <div class="col-md-6" id="tier_section" style="display: {{ $isB2B ? 'block' : 'none' }};">
                        <label class="form-label fw-semibold">Tier</label>
                        <select id="tier_id" name="tier_id" class="form-select" {{ $isB2B ? '' : 'disabled' }}>
                            <option value="">Select Tier</option>
                            <option value="1" {{ old('tier_id') == '1' ? 'selected' : '' }}>Browns</option>
                            <option value="2" {{ old('tier_id') == '2' ? 'selected' : '' }}>Silver</option>
                            <option value="3" {{ old('tier_id') == '3' ? 'selected' : '' }}>Gold</option>
                            <option value="4" {{ old('tier_id') == '4' ? 'selected' : '' }}>Diamond</option>
                        </select>
                    </div>
                </div>

                {{-- Limits & Dates --}}
                <div class="row g-3 mt-1">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Usage Limit</label>
                        <input type="number" name="usage_limit" class="form-control" value="{{ old('usage_limit') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Start Date</label>
                        <input type="datetime-local" name="start_date" class="form-control" value="{{ old('start_date') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">End Date</label>
                        <input type="datetime-local" name="end_date" class="form-control" value="{{ old('end_date') }}">
                    </div>
                </div>

                {{-- Status --}}
                <div class="mt-3 col-md-4">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select">
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                {{-- Buttons --}}
                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-check-circle me-1"></i> Save
                    </button>
                    <a href="{{ route('admin.promocodes.index') }}" class="btn btn-danger ms-2">
                        Cancel
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const customerTypeSelect = document.getElementById('customer_type');
    const tierSection = document.getElementById('tier_section');
    const tierSelect = document.getElementById('tier_id');

    function effectiveCustomerType() {
        const hidden = document.querySelector('input[name="customer_type"]');
        if (hidden && hidden.value) return hidden.value;
        return customerTypeSelect ? customerTypeSelect.value : '';
    }

    function updateTierVisibility() {
        const ct = effectiveCustomerType();
        if (ct === 'b2b') {
            tierSection.style.display = 'block';
            tierSelect.disabled = false;
        } else {
            tierSection.style.display = 'none';
            tierSelect.disabled = true;
            tierSelect.value = '';
        }
    }

    if (customerTypeSelect) {
        customerTypeSelect.addEventListener('change', updateTierVisibility);
    }

    updateTierVisibility();
});
</script>
@endpush
