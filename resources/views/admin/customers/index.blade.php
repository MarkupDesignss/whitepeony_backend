@extends('admin.layouts.app')
@section('title', 'Customers')
@section('content')
<style>
/* 🌟 Main container */
.table-container {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    overflow: hidden;
    padding: 10px;
}

/* 🧾 Table base style */
.table {
    width: 100%;
    font-size: 14px;
    border-collapse: collapse;
}

.table thead th {
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    white-space: nowrap;
    font-size: 14px;
}

.table tbody td {
    vertical-align: middle;
    white-space: nowrap;
}

/* ✨ Scrollbar for small screens */
.table-responsive {
    overflow-x: auto;
    border-radius: 10px;
}

.table-responsive::-webkit-scrollbar {
    height: 8px;
}
.table-responsive::-webkit-scrollbar-thumb {
    background-color: #bbb;
    border-radius: 10px;
}
.table-responsive::-webkit-scrollbar-thumb:hover {
    background-color: #888;
}

/* 🎛️ Buttons styling */
.action-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.action-buttons .btn {
    font-size: 13px;
    padding: 5px 10px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    gap: 4px;
    transition: all 0.2s ease;
}

.action-buttons .btn:hover {
    transform: translateY(-2px);
}

/* 💡 Tier select styling (kept from your original) */
.tier-select {
    width: 150px;
    display: inline-block;
    height: 35px;
    padding: 3px 8px;
    font-size: 14px;
    color: #000;
}
.tier-select option {
    color: #000;
    background-color: #fff;
}

/* 📱 Responsive breakpoints */
@media (max-width: 992px) {
    .table thead th {
        font-size: 13px;
    }
    .table tbody td {
        font-size: 13px;
    }
}

@media (max-width: 768px) {
    .table-container {
        padding: 6px;
    }

    .table {
        font-size: 13px;
    }

    .action-buttons {
        flex-direction: column;
        gap: 5px;
    }

    .action-buttons .btn {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .table thead th {
        font-size: 12px;
    }
    .table tbody td {
        font-size: 12px;
    }

    .action-buttons .btn {
        padding: 5px;
        font-size: 12px;
    }
}
</style>

<div class="container mt-4">
    <div class="d-flex justify-content-between">
        <h2>Customer List</h2>
        <a href="{{ route('admin.customers.create') }}" class="btn btn-primary mb-3">Add Customer</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
<div class="table-container mt-3">
    <div class="table-responsive">
    <table class="table table-bordered table-striped mt-3">
        <thead>
            <tr>
                <th>#ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Type</th>
                <th>VAT ID</th>
                <th>Status</th>
                {{-- <th>Tier</th> --}}
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customers as $customer)
                <tr>
                    <td>{{ $customer->id }}</td>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->email }}</td>
                    <td>{{ $customer->phone }}</td>
                    <td>{{ ucfirst($customer->customer_type) }}</td>
                    <td>{{ $customer->vat_id ?? 'N/A' }}</td>
                    <td>
                        <span class="badge {{ $customer->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                            {{ ucfirst($customer->status) }}
                        </span>
                    </td>
                    {{-- <td>
                        <select class="tier-select form-select" data-id="{{ $customer->id }}">
                            @foreach($tiers as $tier)
                                <option value="{{ $tier->id }}" {{ $customer->tier_id == $tier->id ? 'selected' : '' }}>
                                    {{ $tier->type }}
                                </option>
                            @endforeach
                        </select>
                        <span class="tier-badge badge-{{ strtolower(str_replace(' ', '-', $customer->tier->name)) }}">
                            {{ $customer->tier->name }}
                        </span>
                    </td> --}}
                    <td>
                        <a class="btn btn-primary btn-sm" href="{{ route('admin.customers.view', $customer->id) }}">View</a>
                        <form action="{{ route('admin.customers.toggleStatus', $customer->id) }}" method="POST" onsubmit="return confirm('Are you sure?');" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm {{ $customer->status === 'active' ? 'btn-danger' : 'btn-success' }}">
                                {{ $customer->status === 'active' ? 'Block' : 'Unblock' }}
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">No customers found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
    </div>
</div>
@endsection
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script>
$(document).ready(function() {
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
