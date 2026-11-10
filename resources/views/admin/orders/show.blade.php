@extends('admin.layouts.app')
@section('title', 'Order Details')

@section('content')
<div class="container-fluid mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0 fw-bold">Order #{{ $order->id }}</h3>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-1"></i>Back to List
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">

            {{-- Order Summary --}}
            <div class="row mb-4">
                <div class="col-md-6">
                    <p class="mb-2">
                        <strong>Customer Email:</strong>
                        {{ $order->customer->email ?? 'N/A' }}
                    </p>

                    <p class="mb-2">
                        <strong>Status:</strong>
                        <span class="badge bg-info text-dark">
                            {{ ucfirst($order->status) }}
                        </span>
                    </p>

                    <p class="mb-2">
                        <strong>Payment Status:</strong>
                        <span
                            class="badge {{ $order->payment_status === 'paid' ? 'bg-success' : 'bg-warning text-dark' }}">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </p>
                </div>

                <div class="col-md-6 text-md-end">
                    <p class="mb-2">
                        <strong>Total Amount:</strong>
                        <span class="fs-5 fw-bold text-success">
                            €{{ number_format($order->total_amount, 2) }}
                        </span>
                    </p>
                </div>
            </div>

            {{-- Products --}}
            <h5 class="fw-semibold mb-3">Ordered Products</h5>

            <div class="list-group list-group-flush">
                @foreach($order->items as $item)
                <div class="list-group-item d-flex align-items-center gap-3">
                   
                    <div>
                        <div class="fw-semibold">
                            {{ $item->product->name ?? 'N/A' }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

        </div>
    </div>
</div>
@endsection