@extends('admin.layouts.app')
@section('title','Create Article')

@section('content')
<div class="container-fluid mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0 fw-bold">Create New Article</h3>
        <a href="{{ route('admin.articles.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Oops!</strong> Please fix the following errors:
            <ul class="mb-0 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <form action="{{ route('admin.articles.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Title --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Article Title <span class="text-danger">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="title" 
                        class="form-control" 
                        placeholder="Enter article title"
                        value="{{ old('title') }}" 
                        required
                    >
                </div>

                {{-- Content --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Content <span class="text-danger">*</span>
                    </label>
                    <textarea 
                        name="content" 
                        rows="6" 
                        class="form-control" 
                        placeholder="Write article content here..."
                        required
                    >{{ old('content') }}</textarea>
                </div>

                {{-- Multiple Images --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Article Images
                    </label>

                    <input 
                        type="file" 
                        name="images[]" 
                        class="form-control" 
                        accept="image/*"
                        multiple
                        onchange="previewImages(event)"
                    >

                    <small class="text-muted">
                        You can upload multiple images (JPG, PNG, WEBP). Max 2MB each.
                    </small>

                    {{-- Preview Grid --}}
                    <div class="row mt-3" id="imagePreviewContainer"></div>
                </div>

                {{-- Submit --}}
                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-check-circle me-1"></i> Create Article
                    </button>
                    <a href="{{ route('admin.articles.index') }}" class="btn btn-danger ms-2">
                        Cancel
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>

{{-- Image Preview Script --}}
<script>
function previewImages(event) {
    const container = document.getElementById('imagePreviewContainer');
    container.innerHTML = '';

    Array.from(event.target.files).forEach(file => {
        const col = document.createElement('div');
        col.className = 'col-md-2 col-sm-3 col-4 mb-3';

        const img = document.createElement('img');
        img.src = URL.createObjectURL(file);
        img.className = 'img-fluid rounded border';
        img.style.height = '90px';
        img.style.objectFit = 'cover';

        col.appendChild(img);
        container.appendChild(col);
    });
}
</script>
@endsection