@extends('admin.layouts.app')
@section('title', 'Best Selling Products')
@section('content')

<div class="container mt-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3 listing_name">
        <h3 class="mb-0 fw-bold">Top Selling Products</h3>
    </div>

    {{-- Alert --}}
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Table Card --}}
    <div class="card shadow-sm">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="text-uppercase" style="background:#f1f5f9;">
                        <tr>
                            <th scope="col" style="width: 50px;  background-color: #f1f5f9;">#</th>
                            <th scope="col" style="width: 50px;  background-color: #f1f5f9;">Product</th>
                            <th scope="col" style="width: 50px;  background-color: #f1f5f9;">Image</th>
                            <th scope="col" style="width: 50px;  background-color: #f1f5f9;">Sold</th>
                            <th scope="col" style="width: 50px;  background-color: #f1f5f9;">Avg Rating</th>
                            <th scope="col" style="width: 50px;  background-color: #f1f5f9;">Reviews</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($data as $index => $product)
                            <tr>
                                <td class="fw-semibold">{{ $index + 1 }}</td>

                                <td>
                                    <strong>{{ $product->name }}</strong>
                                </td>

                                <td>
                                    @if($product->front_image)
                                        <img src="{{ asset('storage/' . $product->front_image) }}"
                                             class="rounded border"
                                             width="60"
                                             height="60"
                                             style="object-fit:cover;">
                                    @else
                                        <span class="text-muted">No Image</span>
                                    @endif
                                </td>

                                <td>
                                    <span class="badge bg-primary">
                                        {{ $product->sold_count }}
                                    </span>
                                </td>

                                <td>
                                    <span class="badge bg-warning text-dark">
                                        {{ number_format($product->average_rating,1) }} ★
                                    </span>
                                </td>

                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ $product->review_count }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    No records found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

@endsection
