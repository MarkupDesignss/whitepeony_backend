@extends('admin.layouts.app')
@section('title','Edit Banner')

@section('content')
<div class="container-fluid mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold mb-0">Edit Banner</h3>
        <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <form action="{{ route('admin.banners.update',$banner->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">

                    <div class="col-md-4 mb-3">
                        <label class="fw-semibold">Banner Title</label>
                        <input type="text" name="title" value="{{ old('title',$banner->title) }}" class="form-control"> 
                        @error('title') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="fw-semibold">Banner Image</label>
                        <input type="file" name="image" class="form-control" onchange="previewImage(event)">
                        @error('image') <small class="text-danger">{{ $message }}</small> @enderror
                        <img src="{{ asset('storage/'.$banner->image_url) }}" id="imagePreview" class="img-fluid mt-2"
                            style="max-height:150px;">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="fw-semibold">Start Date</label>
                        <input type="date" name="start_date" value="{{ old('start_date',$banner->start_date) }}"
                            class="form-control">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="fw-semibold">End Date</label>
                        <input type="date" name="end_date" value="{{ old('end_date',$banner->end_date) }}"
                            class="form-control">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="fw-semibold">Product Type</label>
                        <select name="product_type" id="product_type" class="form-select">
                            <option value="">Select type</option>
                            <option value="b2b" {{ old('product_type',$banner->product_type)=='b2b'?'selected':'' }}>B2B
                            </option>
                            <option value="b2c" {{ old('product_type',$banner->product_type)=='b2c'?'selected':'' }}>B2C
                            </option>
                        </select>
                        @error('product_type') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="fw-semibold">Product</label>
                        <select name="product_id" id="product_id" class="form-select">
                            <option value="">Select Product</option>
                        </select>
                         @error('product_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ old('status',$banner->status)=='active'?'selected':'' }}>Active
                            </option>
                            <option value="inactive" {{ old('status',$banner->status)=='inactive'?'selected':'' }}>
                                Inactive</option>
                        </select>
                        @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                </div>

                <div class="text-end">
                    <button class="btn btn-success">Update Banner</button>
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
}

function loadProducts(type, selected = null) {
    fetch(`{{ url('admin/get-banner-products') }}/${type}`)
        .then(res => res.json())
        .then(data => {
            let opt = '<option value="">Select</option>';
            data.forEach(p => {
                opt += `<option value="${p.id}" ${selected==p.id?'selected':''}>${p.name}</option>`;
            });
            document.getElementById('product_id').innerHTML = opt;
        });
}

// on load
loadProducts("{{ $banner->product_type }}", "{{ $banner->product_id }}");

// on change
document.getElementById('product_type').addEventListener('change', function() {
    loadProducts(this.value);
});
</script>
@endsection