@extends('admin.layouts.app')

@section('title', 'Admin - Pages')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3 listing_name">
        <h3 class="mb-0 fw-bold"><i class="fas fa-file-alt me-2"></i>Page List</h3>
        <span>
            <a href="{{ route('admin.pages.create') }}" class="btn btn-outline-primary">
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
                <table class="table table-bordered table-hover table-striped align-middle" width="100%" cellspacing="0">
                    <thead class="text-uppercase">
                        <tr>
                            <th scope="col" style="width: 50px; background-color: #f1f5f9;">SN.</th>
                            <th scope="col" style="width: 250px; background-color: #f1f5f9;">Title</th>
                            <th scope="col" style="width: 150px; background-color: #f1f5f9;">Slug</th>
                            <th scope="col" style="width: 150px; background-color: #f1f5f9;">Image</th>
                            <!--<th scope="col" style="width: 150px; background-color: #f1f5f9;">Status</th>-->
                            <th scope="col" style="width: 200px; background-color: #f1f5f9;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pages as $page)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $page->title }}</td>
                            <td>{{ $page->slug }}</td>
                            <td>
                                @if($page->image)
                                    <!--<img src="{{ asset('uploads/pages/' . $page->image) }}" alt="Page Image" width="80">-->
                                    <img src="{{ asset('storage/' . $page->image) }}" alt="Page Image" width="80">
                                @else
                                    <span class="text-muted">No image</span>
                                @endif
                            </td>
                            <!--<td>-->
                            <!--    @if($page->status == 1)-->
                            <!--        <span class="badge bg-success">Active</span>-->
                            <!--    @else-->
                            <!--        <span class="badge bg-danger">Inactive</span>-->
                            <!--    @endif-->
                            <!--</td>-->
                            <td>
                                <a href="{{ route('admin.pages.edit', $page->id) }}" class="btn btn-warning btn-sm me-1">Edit</a>
                                <form action="{{ route('admin.pages.destroy', $page->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this page?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $pages->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
