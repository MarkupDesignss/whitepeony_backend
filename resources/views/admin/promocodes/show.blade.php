@extends('admin.layouts.app')
@section('title', 'Promo Code Details')

@section('content')
<div class="container-fluid mt-4">

    {{-- Page Header --}}
   
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0 fw-bold">Promo Code Details</h3>
        <a href="{{ route('admin.promocodes.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-1"></i>Back to List
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">

            {{-- Basic Info --}}
            <h6 class="text-muted fw-semibold mb-3">Basic Information</h6>
            <div class="row mb-3">
                <div class="col-md-4 fw-semibold">Title</div>
                <div class="col-md-8">{{ $promocode->title }}</div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-semibold">Description</div>
                <div class="col-md-8">{{ $promocode->description ?? '-' }}</div>
            </div>

            {{-- Coupon & Discount --}}
            <hr>
            <h6 class="text-muted fw-semibold mb-3">Discount Details</h6>

            <div class="row mb-3">
                <div class="col-md-4 fw-semibold">Coupon Code</div>
                <div class="col-md-8">
                    <span class="badge bg-light text-dark border">
                        {{ $promocode->coupon_code }}
                    </span>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-semibold">Discount</div>
                <div class="col-md-8">
                   {{ $promocode->discount_type === 'percentage' 
                        ? $promocode->discount_value . '%' 
                        : '$' . $promocode->discount_value 
                    }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-semibold">Max Discount</div>
                <div class="col-md-8">{{ $promocode->max_discount ?? '-' }}</div>
            </div>

            {{-- Customer --}}
            <hr>
            <h6 class="text-muted fw-semibold mb-3">Customer Targeting</h6>

            <div class="row mb-3">
                <div class="col-md-4 fw-semibold">Customer Type</div>
                <div class="col-md-8 text-uppercase">
                    {{ $promocode->customer_type }}
                </div>
            </div>

            @if($promocode->customer_type === 'b2b')
            <div class="row mb-3">
                <div class="col-md-4 fw-semibold">Tier</div>
                <div class="col-md-8">{{ $tier->type ?? '-' }}</div>
            </div>
            @endif

            {{-- Usage --}}
            <hr>
            <h6 class="text-muted fw-semibold mb-3">Usage & Validity</h6>

            <div class="row mb-3">
                <div class="col-md-4 fw-semibold">Usage Limit</div>
                <div class="col-md-8">
                    {{ $promocode->usage_limit ?? 'Unlimited' }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-semibold">Start Date</div>
                <div class="col-md-8">{{ $promocode->start_date }}</div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-semibold">End Date</div>
                <div class="col-md-8">{{ $promocode->end_date }}</div>
            </div>

            {{-- Status --}}
            <hr>
            <div class="row">
                <div class="col-md-4 fw-semibold">Status</div>
                <div class="col-md-8">
                    @if($promocode->status === 'active')
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-danger">Inactive</span>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
