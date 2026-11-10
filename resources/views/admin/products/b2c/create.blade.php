@extends('admin.layouts.app')
@section('title','Create Product')
@section('content')

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0 fw-bold">Add New Product</h3>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-1"></i>Back to List
        </a>
    </div>

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

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="post" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Product Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Product Type <span class="text-danger">*</span></label>
                        <select name="product_type" class="form-select" required>
                            <option value="b2c" selected>B2C</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Front Image</label>
                        <input type="file" name="front_image" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Back Image</label>
                        <input type="file" name="back_image" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Side Image</label>
                        <input type="file" name="side_image" class="form-control">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Brewing Info</label>
                    <textarea name="brewing_info" class="form-control" rows="2">{{ old('brewing_info') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Categories</label>
                    <div id="categories">
                        @foreach($category as $id => $name)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="category_id[]" value="{{ $id }}">
                            <label class="form-check-label">{{ $name }}</label>
                        </div>
                        @endforeach
                    </div>
                </div>

                <button type="button" id="add-variant" class="btn btn-primary mb-3"><i class="bi bi-plus-circle me-1"></i>Add Unit</button>

                <h5 class="fw-bold">Product Units / Variants</h5>
                <div id="variants-wrapper">
                    <div class="row mb-2 variant-row">
                        <div class="col-md-3">
                            <input type="text" name="variants[0][unit]" class="form-control" placeholder="Unit (e.g., 100g)">
                        </div>
                        <div class="col-md-3">
                            <input type="number" step="0.01" name="variants[0][price]" class="form-control" placeholder="Price">
                        </div>
                        <div class="col-md-3">
                            <input type="number" step="0.01" name="variants[0][stock]" class="form-control" placeholder="Stock">
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-danger remove-variant"><i class="bi bi-trash"></i> Remove</button>
                        </div>
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button class="btn btn-success"><i class="bi bi-check-circle me-1"></i>Save Product</button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-danger ms-2"><i class="bi bi-x-circle me-1"></i>Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let variantIndex = 1; // start from 1 because 0 already exists
    const wrapper = document.getElementById('variants-wrapper');
    const addVariantBtn = document.getElementById('add-variant');

    addVariantBtn.addEventListener('click', function() {
        const row = document.createElement('div');
        row.classList.add('row', 'mb-2', 'variant-row');
        row.innerHTML = `
            <div class="col-md-3">
                <input type="text" name="variants[${variantIndex}][unit]" class="form-control" placeholder="Unit (e.g., 100g)">
            </div>
            <div class="col-md-3">
                <input type="number" step="0.01" name="variants[${variantIndex}][price]" class="form-control" placeholder="Price">
            </div>
            <div class="col-md-3">
                <input type="number" step="0.01" name="variants[${variantIndex}][stock]" class="form-control" placeholder="Stock">
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-danger remove-variant"><i class="bi bi-trash"></i> Remove</button>
            </div>
        `;
        wrapper.appendChild(row);
        variantIndex++;
    });

    // Remove variant row
    wrapper.addEventListener('click', function(e) {
        if(e.target.closest('.remove-variant')){
            e.target.closest('.variant-row').remove();
        }
    });
});
</script>

@endsection
