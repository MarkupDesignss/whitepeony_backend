@extends('admin.layouts.app')
@section('title', 'Registered Events')

@section('content')
<div class="container mt-4">
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0 fw-bold">Registered Events</h3>
        <a href="{{ route('admin.events.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-1"></i>Back to List
        </a>
    </div>

    @if($registrations->isEmpty())
        <div class="alert alert-info">No registered events found.</div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Registration ID</th>
                        <th>Attendee Email</th>
                        <th>Full Name</th>
                        <th>Event ID</th>
                        <th>Booked Seats</th>
                        <th>Per Seat Price</th>
                        <th>Total Price</th>
                        <th>Payment Status</th>
                        <th>Status</th>
                        <th>Registered On</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($registrations as $index => $reg)
                        <tr>
                            <td>{{ $registrations->firstItem() + $index }}</td>
                            <td>{{ $reg->registration_id ?? '-' }}</td>
                            <td>{{ $reg->attendee_email ?? '-' }}</td>
                            <td>{{ $reg->full_name ?? '-' }}</td>
                            <td>{{ $reg->event_id ?? '-' }}</td>
                            <td>{{ $reg->booked_seats ?? 0 }}</td>
                            <td>₹{{ number_format($reg->per_seat_price ?? 0, 2) }}</td>
                            <td>₹{{ number_format($reg->total_price ?? 0, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $reg->payment_status === 'paid' ? 'success' : 'warning' }}">
                                    {{ ucfirst($reg->payment_status ?? 'pending') }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $reg->status === 'cancelled' ? 'danger' : 'info' }}">
                                    {{ ucfirst($reg->status ?? '-') }}
                                </span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($reg->created_at)->format('d M Y, h:i A') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <!-- Pagination Links -->
        <div class="d-flex justify-content-center mt-3">
            {{ $registrations->links('pagination::bootstrap-4') }}
        </div>
        </div>

        
    @endif
</div>
@endsection
