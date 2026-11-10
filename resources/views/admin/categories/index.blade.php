@extends('admin.layouts.app')
@section('title','Categories')
@section('content')

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3 listing_name">
        <h3 class="mb-0 fw-bold">Categories List</h3>
        <span>
            <a href="{{ route('admin.categories.create') }}" class="btn btn-outline-primary">
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
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead class="text-uppercase">
                        <tr>
                            <th scope="col" style="width: 50px;  background-color: #f1f5f9;">SN.</th>
                            <th style="width: 80px; background:#f1f5f9;">Position</th>
                            <th scope="col" style="width: 250px; background-color: #f1f5f9;">Name</th>
                            <th scope="col" style="width: 150px; background-color: #f1f5f9;">Icon</th>
                            <th scope="col" style="width: 150px; background-color: #f1f5f9;">Image</th>
                            <th scope="col" style="width: 150px; background-color: #f1f5f9;">Description</th>
                            <th scope="col" style="width: 200px; background-color: #f1f5f9;">Type</th>
                            <th scope="col" style="width: 200px; background-color: #f1f5f9;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><span class="badge bg-primary">{{ $category->position }}</span></td>
                            <td>{{ $category->name }}</td>
                            <td>
                                @if($category->icon)
                                    <img src="{{ asset('storage/'.$category->icon) }}" height="40">
                                @else
                                    <span class="text-muted">No Icon</span>
                                @endif
                            </td>

                            <td>
                                @if($category->image)
                                    <img src="{{ asset('storage/'.$category->image) }}" height="50" class="rounded">
                                @else
                                    <span class="text-muted">No Image</span>
                                @endif
                            </td>
                            <td>{{ ($category->description) }}</td>
                            <td>{{ ($category->category_type) }}</td>
                            <td>
                                <a href="{{ route('admin.categories.edit', $category->id) }}"
                                    class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST"
                                    class="d-inline" onsubmit="return confirm('Delete this category?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No categories found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-3">
                    {{ $categories->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection