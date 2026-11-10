@extends('admin.layouts.app')
@section('title','Add Feature Product')
@section('content')

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0 fw-bold">Add Feature Product</h3>
        <a href="{{ route('admin.feature_products.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-1"></i>Back to List
        </a>
    </div>
    <div class="card">
        <div class="card-body">

            <form action="{{ route('admin.feature_products.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label>Product Type</label>
                        <select name="product_type" id="product_type" class="form-control">
                            <option value="">Select</option>
                            <option value="b2b">B2B</option>
                            <option value="b2c">B2C</option>
                        </select>
                        @error('product_type') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Product</label>
                        <select name="product_id" id="product_id" class="form-control">
                            <option value="">Select Product</option>
                        </select>
                        @error('product_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                </div>

                <div class="text-end mt-3">
                    <button class="btn btn-success">Submit</button>
                    <a href="{{ route('admin.feature_products.index') }}" class="btn btn-danger ms-2">Cancel</a>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
document.getElementById('product_type').addEventListener('change', function() {
    let type = this.value;
    fetch(`{{ url('admin/get-feature-products') }}/${type}`)
        .then(res => res.json())
        .then(data => {
            let opt = '<option value="">Select Product</option>';
            data.forEach(p => opt += `<option value="${p.id}">${p.name}</option>`);
            document.getElementById('product_id').innerHTML = opt;
        });
});
</script>
@endsection