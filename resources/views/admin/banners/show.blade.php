@extends('admin.layouts.app')
@section('title', 'View Banner')

@section('content')
<div class="container-fluid mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0 fw-bold">Banner Details</h3>
        <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-1"></i>Back to List
        </a>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <div class="row g-4">
                {{-- Banner Info --}}
                <div class="col-md-7">
                    <h5 class="fw-semibold mb-3">{{ $banner->title }}</h5>

                    <div class="mb-2">
                        <strong>Status:</strong>
                        <span class="badge {{ $banner->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                            {{ ucfirst($banner->status) }}
                        </span>
                    </div>

                    <div class="mb-2">
                        <strong>Start Date:</strong>
                        {{ $banner->start_date ? \Carbon\Carbon::parse($banner->start_date)->format('d M Y') : 'N/A' }}
                    </div>

                    <div class="mb-2">
                        <strong>End Date:</strong>
                        {{ $banner->end_date ? \Carbon\Carbon::parse($banner->end_date)->format('d M Y') : 'N/A' }}
                    </div>
                    <div class="mb-2">
                        <strong>Product Type:</strong> {{ strtoupper($banner->product_type) }}
                    </div>
                    <div class="mb-2">
                        <strong>Associated Product:</strong>
                        @if($banner->product)
                        {{ $banner->product->name }}
                        @else
                        N/A
                        @endif
                    </div>

                    {{-- Banner Image --}}
                    <div class="col-md-5">
                        <label class="fw-semibold mb-2 d-block">Banner Image</label>
                        <img src="{{ asset('storage/' . $banner->image_url) }}" alt="{{ $banner->title }}"
                            class="img-fluid rounded border">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection