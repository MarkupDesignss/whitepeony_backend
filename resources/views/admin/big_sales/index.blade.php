@extends('admin.layouts.app')
@section('title', 'Big Sale List')
@section('content')

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3 listing_name">
        <h3 class="mb-0 fw-bold">Big Sale List</h3>
        <span>
            <a href="{{ route('admin.big_sales.create') }}" class="btn btn-outline-primary">
                <i class="fa fa-plus me-1"></i><strong>ADD NEW</strong>
            </a>
        </span>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <div class="table-responsive">
                <table class="table table-bordered" width="100%">
                    <thead class="text-uppercase">
                        <tr>
                            <th>ID</th>
                            <th>Products</th>
                            <th>Product Type</th>
                            <th>Percentage</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bigSales as $sale)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                {{ $sale->product->name }}
                            </td>
                            <td>{{ strtoupper($sale->product_type) }}</td>
                            <td>{{ $sale->percentage }}%</td>
                            <td>{{ $sale->start_date }}</td>
                            <td>{{ $sale->end_date }}</td>
                            <td>
                            <span class="badge bg-{{ $sale->status=='active'?'success':'danger' }}">
                                {{ ucfirst($sale->status) }}
                            </span>
                            </td>
                            <td>                                
                                <form action="{{ route('admin.big_sales.delete',$sale->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="d-flex justify-content-end mt-3">
                {{ $bigSales->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
