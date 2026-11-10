@extends('admin.layouts.app')
@section('title','Add Big Sale')
@section('content')

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0 fw-bold">Add Big Sale</h3>
        <a href="{{ route('admin.big_sales.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-1"></i>Back to List
        </a>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.big_sales.store') }}" method="POST">
                @csrf
                <div class="row">

                    {{-- Start Date --}}
                    <div class="col-md-4 mb-3">
                        <label>Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}">
                        @error('start_date') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    {{-- End Date --}}
                    <div class="col-md-4 mb-3">
                        <label>End Date</label>
                        <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}">
                        @error('end_date') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    {{-- Status --}}
                    <div class="col-md-4 mb-3">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    {{-- Product Type --}}
                     <div class="col-md-4 mb-3">
                        <label>Product Type</label>
                        <select name="product_type" id="product_type" class="form-control">
                            <option value="">-- Select Product Type --</option>
                            <option value="b2b">B2B</option>
                            <option value="b2c">B2C</option>
                        </select>
                        @error('product_type') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    {{-- Products with % --}}
                    <div class="col-md-12">
                        <label>Select Products & Discount %</label>
                        <div id="productBox"></div>
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

    fetch(`{{ url('admin/big-sales/get-products') }}/${type}`)
        .then(res => res.json())
        .then(products => {

            let html = `<table class="table table-bordered">
                        <tr><th>Select</th><th>Product</th><th>Discount %</th></tr>`;

            products.forEach(p => {
                html += `
                <tr>
                    <td><input type="checkbox" name="products[]" value="${p.id}"></td>
                    <td>${p.name}</td>
                    <td>
                        <input type="number" name="percentages[${p.id}]" class="form-control"
                               min="1" max="100" placeholder="%">
                    </td>
                </tr>`;
            });

            html += `</table>`;
            document.getElementById('productBox').innerHTML = html;
        });
});
</script>

@endsection
