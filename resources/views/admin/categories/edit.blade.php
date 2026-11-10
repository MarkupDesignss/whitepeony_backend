@extends('admin.layouts.app')
@section('title','Edit Category')
@section('content')

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0 fw-bold">Edit Category</h3>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-1"></i>Back to List
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- Name -->
                    <div class="col-md-4 mb-3">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control"
                            value="{{ old('name', $category->name) }}" required>
                        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label>Position</label>
                        <input type="number" name="position" class="form-control"
                            value="{{ old('position', $category->position) }}" required>
                        @error('position') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Icon -->
                    <div class="col-md-4 mb-3">
                        <label>Icon</label>
                        <input type="file" name="icon" class="form-control">
                        @error('icon') <small class="text-danger">{{ $message }}</small> @enderror

                        @if($category->icon)
                            <div class="mt-2">
                                <img src="{{ asset('storage/'.$category->icon) }}" height="40" class="rounded border">
                            </div>
                        @endif
                    </div>

                    <!-- Image -->
                    <div class="col-md-4 mb-3">
                        <label>Image</label>
                        <input type="file" name="image" class="form-control">
                        @error('image') <small class="text-danger">{{ $message }}</small> @enderror

                        @if($category->image)
                            <div class="mt-2">
                                <img src="{{ asset('storage/'.$category->image) }}" height="70" class="rounded border">
                            </div>
                        @endif
                    </div>

                    <!-- Category Type -->
                    <div class="col-md-4 mb-3">
                        <label>Category Type</label>
                        <select name="category_type" class="form-control form-select" required>
                            <option value="">-- Select Type --</option>
                            <option value="primary"
                                {{ old('category_type', $category->category_type) == 'primary' ? 'selected' : '' }}>
                                Primary
                            </option>
                            <option value="secondary"
                                {{ old('category_type', $category->category_type) == 'secondary' ? 'selected' : '' }}>
                                Secondary
                            </option>
                            <option value="other"
                                {{ old('category_type', $category->category_type) == 'other' ? 'selected' : '' }}>
                                Other
                            </option>
                        </select>
                        @error('category_type') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Description -->
                    <div class="col-md-6 mb-3">
                        <label>Description</label>
                        <textarea name="description" class="form-control"
                            rows="3">{{ old('description', $category->description) }}</textarea>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-success">Update</button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-danger ms-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
