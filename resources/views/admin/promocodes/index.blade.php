@extends('admin.layouts.app')
@section('title', 'Promo Codes')
@section('content')

<div class="container mt-4">

    {{-- Alert --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3 listing_name">
        <h3 class="mb-0 fw-bold">{{ strtoupper($activeType) }} Promo Codes</h3>

        <a href="{{ route('admin.promocodes.create', ['type' => $activeType]) }}"
           class="btn btn-outline-primary">
            <i class="fa fa-plus me-1"></i> Add {{ strtoupper($activeType) }} Promo
        </a>
    </div>

    {{-- Tabs --}}
    <ul class="nav nav-pills mb-3">
        <li class="nav-item">
            <a class="nav-link {{ $activeType === 'b2b' ? 'active' : '' }}"
               href="{{ route('admin.promocodes.index', ['type' => 'b2b']) }}">
                B2B
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $activeType === 'b2c' ? 'active' : '' }}"
               href="{{ route('admin.promocodes.index', ['type' => 'b2c']) }}">
                B2C
            </a>
        </li>
    </ul>

    {{-- Decide Data --}}
    @php
        $promos = ($activeType === 'b2b') ? $b2bPromocodes : $b2cPromocodes;
    @endphp

    {{-- Table Card --}}
    <div class="card shadow-sm">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="text-uppercase" style="background:#f1f5f9;">
                        <tr>
                            <th scope="col" style="width: 50px;  background-color: #f1f5f9;">Title</th>
                            <th scope="col" style="width: 50px;  background-color: #f1f5f9;">Coupon</th>
                            <th scope="col" style="width: 50px;  background-color: #f1f5f9;">Discount</th>
                            <th scope="col" style="width: 50px;  background-color: #f1f5f9;">Tier</th>
                            <th scope="col" style="width: 50px;  background-color: #f1f5f9;">Status</th>
                            <th scope="col" style="width: 50px;  background-color: #f1f5f9;">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($promos as $promo)
                            <tr>
                                <td class="fw-semibold">{{ $promo->title }}</td>

                                <td>
                                    <span class="badge bg-dark">
                                        {{ $promo->coupon_code }}
                                    </span>
                                </td>

                               <td>
                                    <span class="badge bg-primary">
                                        {{ $promo->discount_type == 'percentage' ? $promo->discount_value . '%' : '$' . $promo->discount_value }}
                                    </span>
                                </td>

                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ $promo->tier?->type ?? '-' }}
                                    </span>
                                </td>

                                <td>
                                    <span class="badge bg-{{ $promo->status === 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($promo->status) }}
                                    </span>
                                </td>

                                <td>
                                    <a href="{{ route('admin.promocodes.show', ['promocode' => $promo->id, 'type' => $activeType]) }}"
                                       class="btn btn-info btn-sm">
                                        View
                                    </a>

                                    <a href="{{ route('admin.promocodes.edit', ['promocode' => $promo->id, 'type' => $activeType]) }}"
                                       class="btn btn-warning btn-sm">
                                        Edit
                                    </a>

                                    <form action="{{ route('admin.promocodes.destroy', ['promocode' => $promo->id, 'type' => $activeType]) }}"
                                          method="POST"
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure?')">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    No promo codes found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-end mt-3">
                {{ $promos->appends(['type' => $activeType])->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>
</div>
@endsection
