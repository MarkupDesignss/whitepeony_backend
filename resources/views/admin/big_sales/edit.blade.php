@extends('admin.layouts.app')
@section('title','Edit Big Sale')
@section('content')

<div class="container mt-4">
    <div class="d-flex justify-content-between mb-3">
        <h3>Edit Big Sale</h3>
        <a href="{{ route('admin.big_sales.index') }}" class="btn btn-outline-primary">Back</a>
    </div>

    <form action="{{ route('admin.big_sales.update',$sale->id) }}" method="POST">
        @csrf

        <input type="hidden" name="old_start" value="{{ $sale->start_date }}">
        <input type="hidden" name="old_end" value="{{ $sale->end_date }}">
        <input type="hidden" name="old_type" value="{{ $sale->product_type }}">

        <div class="row">

            <div class="col-md-4 mb-3">
                <label>Start Date</label>
                <input type="date" name="start_date" class="form-control" value="{{ $sale->start_date }}">
            </div>

            <div class="col-md-4 mb-3">
                <label>End Date</label>
                <input type="date" name="end_date" class="form-control" value="{{ $sale->end_date }}">
            </div>

            <div class="col-md-4 mb-3">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="active" {{ $sale->status=='active'?'selected':'' }}>Active</option>
                    <option value="inactive" {{ $sale->status=='inactive'?'selected':'' }}>Inactive</option>
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label>Product Type</label>
                <select name="product_type" id="product_type" class="form-control">
                    <option value="">Select</option>
                    <option value="b2b" {{ $sale->product_type=='b2b'?'selected':'' }}>B2B</option>
                    <option value="b2c" {{ $sale->product_type=='b2c'?'selected':'' }}>B2C</option>
                </select>
            </div>

            <div class="col-md-12">
                <label>Products & Discount</label>
                <div id="productBox">
                    <table class="table table-bordered">
                        <tr>
                            <th>Select</th>
                            <th>Product</th>
                            <th>Discount %</th>
                        </tr>
                        @foreach($sales as $s)
                        <tr>
                            <td><input type="checkbox" checked name="products[]" value="{{ $s->product_id }}"></td>
                            <td>{{ $s->product->name }}</td>
                            <td><input type="number" name="percentages[{{ $s->product_id }}]"
                                    value="{{ $s->percentage }}" class="form-control"></td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>

        </div>

        <button class="btn btn-success mt-3">Update Big Sale</button>
    </form>
</div>
@endsection