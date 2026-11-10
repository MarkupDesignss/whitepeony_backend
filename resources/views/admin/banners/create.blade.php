@extends('admin.layouts.app')
@section('title','Add Banner')

@section('content')
<div class="container-fluid mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0 fw-bold">Add New Banner</h3>
        <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">

            <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">

                    <div class="col-md-4 mb-3">
                        <label class="fw-semibold">Banner Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" value="{{ old('title') }}" class="form-control">
                        @error('title') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="fw-semibold">Banner Image <span class="text-danger">*</span></label>
                        <input type="file" name="image" class="form-control" onchange="previewImage(event)">
                        @error('image') <small class="text-danger">{{ $message }}</small> @enderror
                        <img id="imagePreview" class="img-fluid mt-2 d-none" style="max-height:150px;">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="fw-semibold">Start Date</label>
                        <input type="date" name="start_date" value="{{ old('start_date') }}" class="form-control">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="fw-semibold">End Date</label>
                        <input type="date" name="end_date" value="{{ old('end_date') }}" class="form-control">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="fw-semibold">Product Type <span class="text-danger">*</span></label>
                        <select name="product_type" id="product_type" class="form-select">
                            <option value="">Select</option>
                            <option value="b2b" {{ old('product_type')=='b2b'?'selected':'' }}>B2B</option>
                            <option value="b2c" {{ old('product_type')=='b2c'?'selected':'' }}>B2C</option>
                        </select>
                        @error('product_type') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="fw-semibold">Product <span class="text-danger">*</span></label>
                        <select name="product_id" id="product_id" class="form-select">
                            <option value="">Select Product</option>
                        </select>
                        @error('product_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="fw-semibold">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select">
                            <option value="active" {{ old('status')=='active'?'selected':'' }}>Active</option>
                            <option value="inactive" {{ old('status')=='inactive'?'selected':'' }}>Inactive</option>
                        </select>
                        @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                </div>

                <div class="text-end">
                    <button class="btn btn-success">Save Banner</button>
                    <a href="{{ route('admin.banners.index') }}" class="btn btn-danger ms-2">Cancel</a>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
function previewImage(e) {
    let img = document.getElementById('imagePreview');
    img.src = URL.createObjectURL(e.target.files[0]);
    img.classList.remove('d-none');
}

document.getElementById('product_type').addEventListener('change', function() {
    let type = this.value;
    if (!type) return;

    fetch(`{{ url('admin/get-banner-products') }}/${type}`)
        .then(res => res.json())
        .then(data => {
            let opt = '<option value="">Select Product</option>';
            data.forEach(p => opt += `<option value="${p.id}">${p.name}</option>`);
            document.getElementById('product_id').innerHTML = opt;
        });
});
</script>
@endsection