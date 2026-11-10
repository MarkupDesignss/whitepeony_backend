@extends('admin.layouts.app')
@section('title','Feature Products')
@section('content')

<div class="container mt-4">

    <div class="d-flex justify-content-between mb-3">
        <h3 class="fw-bold">Feature Products List</h3>
        <a href="{{ route('admin.feature_products.create') }}" class="btn btn-outline-primary">
            <i class="fa fa-plus me-1"></i><strong>ADD NEW</strong>
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">

            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th width="140">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($features as $key=>$f)
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>{{ $f->product->name ?? '-' }}</td>
                        <td>{{ strtoupper($f->product->product_type ?? '-') }}</td>
                        <td>
                            <span class="badge bg-{{ $f->status=='active'?'success':'danger' }}">
                                {{ ucfirst($f->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.feature_products.edit',$f->id) }}"
                                class="btn btn-sm btn-primary">Edit</a>
                            <a href="{{ route('admin.feature_products.delete',$f->id) }}"
                                onclick="return confirm('Delete this product?')"
                                class="btn btn-sm btn-danger">Delete</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">No Featured Product Found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="d-flex justify-content-end mt-3">
                {{ $features->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection