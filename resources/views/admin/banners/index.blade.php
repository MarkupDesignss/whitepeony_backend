@extends('admin.layouts.app')
@section('title', 'Banners')
@section('content')

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3 listing_name">
        <h3 class="mb-0 fw-bold">Banner List</h3>
        <span>
            <a href="{{ route('admin.banners.create') }}" class="btn btn-outline-primary">
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
                            <th scope="col" style="width: 50px;  background-color: #f1f5f9;">ID</th>
                            <th scope="col" style="width: 50px;  background-color: #f1f5f9;">Title</th>
                            <th scope="col" style="width: 50px;  background-color: #f1f5f9;">Image</th>
                            <th scope="col" style="width: 20px;  background-color: #f1f5f9;">Status</th>
                            <th scope="col" style="width: 50px;  background-color: #f1f5f9;">Start Date</th>
                            <th scope="col" style="width: 20px;  background-color: #f1f5f9;">End Date</th>
                            <th scope="col" style="width: 120px;  background-color: #f1f5f9;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($banners as $banner)
                        <tr>
                            <td>{{ $banner->id }}</td>
                            <td>{{ $banner->title }}</td>
                            <td><img src="{{ asset('storage/' . $banner->image_url) }}" alt="{{ $banner->title }}"
                                    width="80"></td>
                            <td>{{ $banner->status }}</td>
                            <td>{{ $banner->start_date }}</td>
                            <td>{{ $banner->end_date }}</td>
                            <td>
                                <a href="{{ route('admin.banners.show', $banner->id) }}"
                                    class="btn btn-primary btn-sm">View</a>
                                <a href="{{ route('admin.banners.edit', $banner->id) }}"
                                    class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST"
                                    style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-3">

                </div>
            </div>
        </div>
    </div>
</div>
@endsection