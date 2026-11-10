@extends('admin.layouts.app')
@section('title', 'B2C Customers')

@section('content')
<div class="container mt-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3 listing_name">
        <h3 class="mb-0 fw-bold">B2C Customers</h3>
    </div>

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
                            <th scope="col" style="width: 50px;  background-color: #f1f5f9;">ID</th>
                            <th scope="col" style="width: 50px;  background-color: #f1f5f9;">Name</th>
                            <th scope="col" style="width: 50px;  background-color: #f1f5f9;">Email</th>
                            <th scope="col" style="width: 50px;  background-color: #f1f5f9;">Phone</th>
                            <th scope="col" style="width: 50px;  background-color: #f1f5f9;">Type</th>
                            <th scope="col" style="width: 50px;  background-color: #f1f5f9;">VAT ID</th>
                            <th scope="col" style="width: 50px;  background-color: #f1f5f9;">Status</th>
                            <th scope="col" style="width: 150px;  background-color: #f1f5f9;">Action</th>
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
                                    <span class="badge bg-info">{{ ucfirst($customer->type) }}</span>
                                </td>
                                <td>{{ $customer->vat_id ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $customer->status === 'active' ? 'success' : 'danger' }}">
                                        {{ ucfirst($customer->status) }}
                                    </span>
                                </td>
                                <td class="action-buttons">
                                    <a href="{{ route('admin.customers.view', $customer->id) }}" class="btn btn-info btn-sm">View</a>

                                    <a href="{{ route('admin.customers.edit', $customer->id) }}" class="btn btn-warning btn-sm">Edit</a>

                                    <form action="{{ route('admin.customers.toggleStatus', $customer->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $customer->status === 'active' ? 'btn-danger' : 'btn-success' }}">
                                            {{ $customer->status === 'active' ? 'Block' : 'Unblock' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No customers found</td>
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

</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

<script>
$(document).ready(function() {
    // Tier update AJAX (optional)
    $('.tier-select').on('change', function() {
        var customerId = $(this).data('id');
        var tierId = $(this).val();
        var badge = $(this).siblings('.tier-badge');

        $.ajax({
            url: '{{ route("admin.customers.updatetier") }}',
            type: 'POST',
            data: {
                '_token': '{{ csrf_token() }}',
                'id': customerId,
                'tier_id': tierId
            },
            success: function(response) {
                if (response.success) {
                    badge.text(response.tier_name);
                    alert('Tier updated successfully.');
                }
            },
            error: function(xhr) {
                alert('Error updating tier: ' + xhr.responseJSON.message);
            }
        });
    });
});
</script>
@endsection
