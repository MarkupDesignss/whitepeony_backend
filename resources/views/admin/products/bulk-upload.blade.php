@extends('admin.layouts.app')

@section('title', 'Bulk Upload Products')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Bulk Upload Products</h4>
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left me-1"></i> Back to List
            </a>
        </div>

        <div class="card-body">
            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your input.
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Sample Download Buttons -->
            <div class="mb-4 d-flex gap-2">
                <a href="{{ asset('sample/bulk_products_sample.xlsx') }}" download class="btn btn-sm btn-success">
                    <i class="bi bi-file-earmark-excel me-1"></i> Download Sample Excel
                </a>
                <a href="{{ asset('sample/bulk_products_sample.zip') }}" download class="btn btn-sm btn-info">
                    <i class="bi bi-file-zip me-1"></i> Download Sample Images ZIP
                </a>
            </div>

            <form action="{{ route('admin.products.bulk.upload.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="file" class="form-label">Select Excel File (.xlsx) <span class="text-danger">*</span></label>
                        <input type="file" name="file" id="file" class="form-control @error('file') is-invalid @enderror" accept=".xlsx" required>
                        @error('file')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="images_zip" class="form-label">Select Images ZIP <span class="text-danger">*</span></label>
                        <input type="file" name="images_zip" id="images_zip" class="form-control @error('images_zip') is-invalid @enderror" accept=".zip" required>
                        @error('images_zip')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-upload me-1"></i> Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
