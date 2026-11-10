@extends('admin.layouts.app')
@section('title', 'Event Details')

@section('content')
<div class="container-fluid mt-4">

    <div class="card shadow-sm border-0 rounded-4">
        {{-- Header --}}
        
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0 fw-bold">{{ $event->title }}</h3>
            <a href="{{ route('admin.events.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left me-1"></i>Back to List
            </a>
        </div>

        {{-- Body --}}
        <div class="card-body bg-light">
            <div class="row g-4">

                {{-- Left --}}
                <div class="col-md-6">
                    <p>
                        <strong><i class="bi bi-card-text me-2"></i>Description</strong><br>
                        <span class="text-muted">{{ $event->description }}</span>
                    </p>

                    <p>
                        <strong><i class="bi bi-geo-alt me-2"></i>Address</strong><br>
                        {{ $event->address }}
                    </p>

                    @if($event->venue_map_link)
                        <p>
                            <strong><i class="bi bi-link-45deg me-2"></i>Map Link</strong><br>
                            <a href="{{ $event->venue_map_link }}" target="_blank">
                                View Location
                            </a>
                        </p>
                    @endif

                    <p>
                        <strong><i class="bi bi-cash-coin me-2"></i>Price Per Seat</strong><br>
                        €{{ number_format($event->price_per_seat, 2) }}
                    </p>

                    <p>
                        <strong><i class="bi bi-person-video3 me-2"></i>Speaker</strong><br>
                        {{ $event->speaker }}
                    </p>

                    <p>
                        <strong><i class="bi bi-list-task me-2"></i>Agenda</strong><br>
                        {{ $event->agenda }}
                    </p>
                </div>

                {{-- Right --}}
                <div class="col-md-6">
                    <p>
                        <strong><i class="bi bi-calendar-date me-2"></i>Date & Time</strong><br>
                        {{ \Carbon\Carbon::parse($event->event_date)->format('d M Y, h:i A') }}
                    </p>

                    <p>
                        <strong><i class="bi bi-people me-2"></i>Capacity</strong><br>
                        {{ $event->capacity }}
                    </p>

                    <p>
                        <strong><i class="bi bi-geo me-2"></i>Coordinates</strong><br>
                        {{ $event->lat ?? '-' }}, {{ $event->long ?? '-' }}
                    </p>

                    @if($event->image)
                        <div class="mt-3">
                            <img src="{{ asset('storage/' . $event->image) }}"
                                 alt="Event Image"
                                 class="img-fluid rounded shadow-sm"
                                 style="max-width: 120px;">
                        </div>
                    @else
                        <p class="text-muted">No image available</p>
                    @endif
                </div>

            </div>
        </div>

        {{-- Footer --}}
        
    </div>

</div>
@endsection
