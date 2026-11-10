@extends('admin.layouts.app')
@section('title','B2B Products')

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

    /* 🔥 drag UX same as B2C */
    .drag-handle {
        cursor: move;
        font-weight: 600;
    }

    tr.sortable-chosen {
        background: #f1f5f9;
    }
</style>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3 listing_name">
        <h3 class="mb-0 fw-bold">B2B Products</h3>
        <span>
            <a href="{{ route('admin.products.b2b.create') }}" class="btn btn-outline-primary">
                <i class="fa fa-plus me-1"></i><strong>ADD NEW</strong>
            </a>
            <a href="{{ route('admin.products.bulk.upload') }}" class="btn btn-outline-primary">
                <i class="fa fa-plus me-1"></i><strong>Bulk Upload Product</strong>
            </a>
        </span>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered align-middle" width="100%">
                    <thead class="text-uppercase">
                        <tr>
                            <th style="background-color:#f1f5f9;">Product (Drag)</th>
                            <th style="background-color:#f1f5f9;">Front Image</th>
                            <th style="background-color:#f1f5f9;">Categories</th>
                            <th style="background-color:#f1f5f9;">Actions</th>
                        </tr>
                    </thead>

                    <!-- 🔥 IMPORTANT -->
                    <tbody id="sortable">
                        @forelse($products as $list)
                            <tr data-id="{{ $list->id }}">

                                {{-- Drag handle --}}
                                <td class="drag-handle">
                                    ☰ {{ $list->name }}
                                </td>

                                {{-- Image --}}
                                <td>
                                    @if($list->front_image)
                                        <img src="{{ asset('storage/'.$list->front_image) }}"
                                             width="60" height="60" class="rounded">
                                    @else
                                        <span class="text-muted">No Image</span>
                                    @endif
                                </td>

                                {{-- Categories --}}
                                <td>
                                    @php
                                        $categories = json_decode($list->category_id, true) ?? [];
                                    @endphp

                                    @if(!empty($categories))
                                        <div class="category-box">
                                            @foreach($categories as $index => $catId)
                                                <span class="category-text">
                                                    {{ $allCategories[$catId] ?? 'N/A' }}
                                                </span>
                                                @if($index < count($categories) - 1)
                                                    <span class="category-comma">, </span>
                                                @endif
                                            @endforeach
                                        </div>
                                    @else
                                        -
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td>
                                    <a href="{{ route('admin.products.edit', $list->id) }}"
                                       class="btn btn-sm btn-warning">
                                        Edit
                                    </a>

                                    <form action="{{ route('admin.products.destroy', $list->id) }}"
                                          method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure?')">
                                            Delete
                                        </button>
                                    </form>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No B2B Products Found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $products->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    new Sortable(document.getElementById('sortable'), {
        animation: 150,
        onEnd: function () {
            let order = [];

            document.querySelectorAll('#sortable tr').forEach(row => {
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