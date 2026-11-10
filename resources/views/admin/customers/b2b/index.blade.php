@extends('admin.layouts.app')
@section('title', 'B2B Customers')

@section('content')

<div class="container mt-4">

```
{{-- Header --}}
<div class="d-flex justify-content-between align-items-center mb-3 listing_name">
    <h3 class="mb-0 fw-bold">B2B Customers</h3>

    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#vatModal">
        Set B2B VAT %
    </button>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Alerts --}}
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@elseif(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

{{-- Table Card --}}
<div class="card shadow-sm">
    <div class="card-body">

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="text-uppercase" style="background:#f1f5f9;">
                    <tr>
                        <th style="width: 50px;background-color:#f1f5f9;">ID</th>
                        <th style="width: 50px;background-color:#f1f5f9;">Name</th>
                        <th style="width: 50px;background-color:#f1f5f9;">Email</th>
                        <th style="width: 50px;background-color:#f1f5f9;">Phone</th>
                        <th style="width: 50px;background-color:#f1f5f9;">Type</th>
                        <th style="width: 50px;background-color:#f1f5f9;">VAT ID</th>
                        <th style="width: 50px;background-color:#f1f5f9;">Status</th>
                        <th style="width: 150px;background-color:#f1f5f9;">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($customers as $customer)
                        <tr>
                            <td class="fw-semibold">#{{ $customer->id }}</td>

                            <td>{{ $customer->name }}</td>

                            <td>{{ $customer->email }}</td>

                            <td>{{ $customer->phone }}</td>

                            <td>
                                <span class="badge bg-info">
                                    {{ ucfirst($customer->type) }}
                                </span>
                            </td>

                            <td>{{ $customer->vat_id ?? 'N/A' }}</td>

                            <td>
                                <span class="badge bg-{{ $customer->status === 'active' ? 'success' : 'danger' }}">
                                    {{ ucfirst($customer->status) }}
                                </span>
                            </td>

                            <td>
                                <a href="{{ route('admin.customers.view', $customer->id) }}"
                                   class="btn btn-info btn-sm">
                                    View
                                </a>

                                <a href="{{ route('admin.customers.edit', $customer->id) }}"
                                   class="btn btn-warning btn-sm">
                                    Edit
                                </a>

                                <form action="{{ route('admin.customers.toggleStatus', $customer->id) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    <button type="submit"
                                            class="btn btn-sm {{ $customer->status === 'active' ? 'btn-danger' : 'btn-success' }}">
                                        {{ $customer->status === 'active' ? 'Block' : 'Unblock' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                No customers found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $customers->links('pagination::bootstrap-5') }}
            </div>

        </div>

    </div>
</div>
```

</div>

{{-- VAT Percentage Modal --}}

<div class="modal fade" id="vatModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

```
        <form action="{{ route('admin.customers.updateVat') }}" method="POST">
            @csrf

            <div class="modal-header">
                <h5 class="modal-title">Set B2B VAT Percentage</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <label class="form-label">VAT Percentage (%)</label>
                    <input type="number"
                           name="vat_percent"
                           class="form-control"
                           step="0.01"
                           min="0"
                           max="100"
                           placeholder="Enter VAT percentage"
                           required>
                </div>

                <small class="text-muted">
                    This VAT percentage will apply to all B2B customer orders.
                </small>

            </div>

            <div class="modal-footer">
                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                    Cancel
                </button>

                <button type="submit"
                        class="btn btn-success">
                    Save VAT
                </button>
            </div>

        </form>

    </div>
</div>
```

</div>

@endsection
