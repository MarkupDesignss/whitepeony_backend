@extends('admin.layouts.app')
@section('title','Products')

@section('content')
<style>
    .category-box {
        border: 1px solid #28a745;
        padding: 4px 8px;
        border-radius: 6px;
        display: inline-block;
        background: #f6fff8;
    }

    .category-text {
        color: #28a745;
        font-weight: 500;
    }

    .category-comma {
        color: #333;
    }

    .drag-handle {
        cursor: move;
        font-weight: 600;
    }

    tr.sortable-chosen {
        background-color: #f1f5f9;
    }
</style>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3 listing_name">
        <h3 class="mb-0 fw-bold">Products List</h3>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered" width="100%">
                    <thead class="text-uppercase">
                        <tr>
                            <th style="background:#f1f5f9;">Product (Drag)</th>
                            <th style="background:#f1f5f9;">Categories</th>
                            <th style="background:#f1f5f9;">Product Type</th>
                            <th style="background:#f1f5f9;">Image</th>
                            <th style="background:#f1f5f9;">Actions</th>
                        </tr>
                    </thead>

                    {{-- 🔥 IMPORTANT --}}
                    <tbody id="sortable-products">
                        @foreach($products as $product)
                        <tr data-id="{{ $product->id }}">

                            {{-- Drag Handle --}}
                            <td class="drag-handle">
                                ☰ {{ $product->name }}
                            </td>

                            {{-- Categories --}}
                            <td>
                                @php
                                    $ids = json_decode($product->category_id, true) ?? [];
                                    $names = collect($ids)->map(fn($id) => $allCategories[$id] ?? null)
                                                ->filter()->toArray();
                                @endphp

                                @if(count($names))
                                    <div class="category-box">
                                        @foreach($names as $i => $cat)
                                            <span class="category-text">{{ $cat }}</span>
                                            @if($i < count($names)-1)
                                                <span class="category-comma">, </span>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    -
                                @endif
                            </td>

                            {{-- Product Type --}}
                            <td>{{ strtoupper($product->product_type) }}</td>

                            {{-- Image --}}
                            <td>
                                @if($product->front_image)
                                    <img src="{{ asset('storage/'.$product->front_image) }}"
                                         width="50" height="50" class="img-thumbnail">
                                @else
                                    <span class="text-muted">No Image</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td>
                                <a href="{{ route('admin.products.edit', $product->id) }}"
                                   class="btn btn-sm btn-warning">Edit</a>

                                <form action="{{ route('admin.products.destroy', $product->id) }}"
                                      method="POST" style="display:inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger"
                                            onclick="return confirm('Delete product?')">
                                        Delete
                                    </button>
                                </form>
                            </td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
new Sortable(document.getElementById('sortable-products'), {
    animation: 150,
    onEnd: function () {
        let order = [];
        document.querySelectorAll('#sortable-products tr').forEach(row => {
            order.push(row.dataset.id);
        });

        fetch("{{ route('admin.products.reorder') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ order: order })
        })
        .then(res => res.json())
        .then(() => console.log('Order saved'))
        .catch(() => alert('Reorder failed'));
    }
});
});
</script>
@endsection