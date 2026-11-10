@extends('admin.layouts.app')
@section('title', 'Customer Details')

@section('content')
<div class="container-fluid mt-4">

    {{-- Page Header --}}
   
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0 fw-bold">Customer Details</h3>
        <a href="{{ route($customer->type === 'b2b' ? 'admin.customers.b2b' : 'admin.customers.b2c') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-1"></i>Back to List
        </a>
    </div>

    {{-- Customer Info --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h6 class="fw-semibold text-muted mb-3">Basic Information</h6>

            <div class="row">
                <div class="col-md-6 mb-2">
                    <strong>Name:</strong> {{ $customer->name }}
                </div>

                <div class="col-md-6 mb-2">
                    <strong>Email:</strong> {{ $customer->email }}
                </div>

                <div class="col-md-6 mb-2">
                    <strong>Phone:</strong> {{ $customer->phone ?? 'N/A' }}
                </div>

                <div class="col-md-6 mb-2">
                    <strong>VAT ID:</strong> {{ $customer->vat_id ?? 'N/A' }}
                </div>

                <div class="col-md-6 mb-2">
                    <strong>Customer Type:</strong>
                    <span class="badge bg-info text-dark">
                        {{ strtoupper($customer->customer_type) }}
                    </span>
                </div>

                <div class="col-md-6 mb-2">
                    <strong>Status:</strong>
                    <span class="badge {{ $customer->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                        {{ ucfirst($customer->status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Address Section --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h6 class="fw-semibold text-muted mb-3">Addresses</h6>

            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Full Address</th>
                            <th>Postal Code</th>
                            <th>Address Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($addresses as $address)
                            <tr>
                                <td>{{ $address->full_address }}</td>
                                <td>{{ $address->postal_code ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ ucfirst($address->address_type) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">
                                    No addresses found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
