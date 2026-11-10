@extends('admin.layouts.app')
@section('title','Orders')
@section('content')

<div class="container mt-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3 listing_name">
        <h3 class="mb-0 fw-bold">Orders List</h3>
    </div>

    {{-- Alert --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Filter Card --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">

                <div class="col-md-2">
                    <label class="form-label fw-semibold">Order Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="placed" {{ request('status')=='placed'?'selected':'' }}>Placed</option>
                        <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Processing</option>
                        <option value="shipped" {{ request('status')=='shipped'?'selected':'' }}>Shipped</option>
                        <option value="delivered" {{ request('status')=='delivered'?'selected':'' }}>Delivered</option>
                        <option value="cancelled" {{ request('status')=='cancelled'?'selected':'' }}>Cancelled</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Product</label>
                    <select name="product_id" class="form-select">
                        <option value="">All Products</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ request('product_id')==$product->id?'selected':'' }}>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold">Start Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold">End Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
                </div>

                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button class="btn btn-primary w-100">
                        <i class="fa fa-filter me-1"></i> Apply
                    </button>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary w-100">
                        Reset
                    </a>
                </div>

            </form>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="text-uppercase" style="background:#f1f5f9;">
                        <tr>
                            <th scope="col" style="width: 50px;  background-color: #f1f5f9;">Order #</th>
                            <th scope="col" style="width: 50px;  background-color: #f1f5f9;">Email</th>
                            <th scope="col" style="width: 50px;  background-color: #f1f5f9;">Status</th>
                            <th scope="col" style="width: 50px;  background-color: #f1f5f9;">Payment</th>
                            <th scope="col" style="width: 50px;  background-color: #f1f5f9;">Total</th>
                            <th scope="col" style="width: 50px;  background-color: #f1f5f9;">Products</th>
                            <th scope="col" style="width: 150px;  background-color: #f1f5f9;">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td><strong>#{{ $order->id }}</strong></td>

                            <td>{{ $order->customer->email ?? 'N/A' }}</td>

                            <td>
                                <span class="badge bg-{{ 
                                    $order->status == 'delivered' ? 'success' :
                                    ($order->status == 'cancelled' ? 'danger' : 'warning')
                                }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>

                            <td>
                                <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </td>

                            <td class="fw-semibold">€{{ number_format($order->total_amount, 2) }}</td>

                            <td>
                                @foreach($order->items as $item)
                                    <span class="badge bg-light text-dark border me-1">
                                        {{ $item->product->name ?? '' }}
                                    </span>
                                @endforeach
                            </td>

                            <td>
                                <a href="{{ route('admin.orders.show', $order->id) }}"
                                   class="btn btn-info btn-sm">
                                    View
                                </a>

                                <form action="{{ route('admin.orders.destroy', $order->id) }}"
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Are you sure?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                No orders found
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-end mt-3">
                {{ $orders->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

</div>

@endsection
