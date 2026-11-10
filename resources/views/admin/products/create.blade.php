@extends('admin.layouts.app')
@section('title','Create Product')
@section('content')
<div class="container">

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
    <div class="d-flex justify-content-between mt-3">
        <h2>Add Product</h2>
        <a href="{{ route('admin.products.index') }}" class="btn btn-primary mb-3">Back</a>
    </div>

    <form method="post" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control"></textarea>
        </div>

        <div class="mb-3">
            <label>Product Type</label>
            <select name="product_type" class="form-control" required>
                <option value="">-- Select Type --</option>
                <option value="b2b">B2B</option>
                <option value="b2c">B2C</option>
            </select>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label>Front Image</label>
                <input type="file" name="front_image" class="form-control">
            </div>
            <div class="col-md-4 mb-3">
                <label>Back Image</label>
                <input type="file" name="back_image" class="form-control">
            </div>
            <div class="col-md-4 mb-3">
                <label>Side Image</label>
                <input type="file" name="side_image" class="form-control">
            </div>
        </div>

        <div class="mb-3">
            <label>Main Price</label>
            <input type="text" name="main_price" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Brewing Info</label>
            <textarea name="brewing_info" class="form-control"></textarea>
        </div>

        <div class="mb-3">
            <label>Stock Quantity</label>
            <input type="number" name="stock_quantity" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Categories</label>
            <div id="categories">
                @foreach($category as $id => $name)
                    <div class="form-check form-check-inline">
                        <input class="form-check-input"  type="checkbox" name="category_id[]" value="{{ $id }}">
                        <label class="form-check-label">{{ $name }}</label>
                    </div>
                @endforeach
            </div>
        </div>

        <button type="button" id="add-variant" class="btn btn-primary mb-3">Add Unit</button>

        <h4>Product Units / Variants</h4>
        <div id="variants-wrapper">
            <div class="row mb-2 variant-row">
                <div class="col-md-3">
                    <input type="text" name="variants[0][unit]" class="form-control" placeholder="Unit (e.g., 100g)">
                </div>
                <div class="col-md-3">
                    <input type="number" step="0.01" name="variants[0][price]" class="form-control" placeholder="Price">
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-danger remove-variant">Remove</button>
                </div>
            </div>
        </div>

        <button class="btn btn-success mt-3">Save</button>
    </form>
</div>
@endsection

<!-- TomSelect CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('categories')) {
        // If you want to use TomSelect for a select element, use a <select> instead of checkboxes.
        // Example:
        // new TomSelect("#categories", { plugins: ['remove_button'], placeholder: "Select categories" });
    }
});

let variantIndex = {{ old('variants') ? count(old('variants')) : 1 }};

document.addEventListener('DOMContentLoaded', function() {
    var addVariantBtn = document.getElementById('add-variant');
    if (addVariantBtn) {
        addVariantBtn.addEventListener('click', function() {
            let wrapper = document.getElementById('variants-wrapper');
            let row = document.createElement('div');
            row.classList.add('row', 'mb-2', 'variant-row');
            row.innerHTML = `
                <div class="col-md-3">
                    <input type="text" name="variants[${variantIndex}][unit]" class="form-control" placeholder="Unit (e.g., 100g)">
                </div>
                <div class="col-md-3">
                    <input type="number" step="0.01" name="variants[${variantIndex}][price]" class="form-control" placeholder="Price">
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-danger remove-variant">Remove</button>
                </div>
            `;
            wrapper.appendChild(row);
            variantIndex++;
        });
    }

    // Remove variant row
    document.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('remove-variant')) {
            e.target.closest('.variant-row').remove();
        }
    });
});
</script>
