@extends('admin.layouts.app')
@section('title', isset($event) ? 'Edit Event' : 'Create Event')

@section('content')
<div class="container-fluid mt-4">

    {{-- Page Header --}}
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0 fw-bold">{{ isset($event) ? 'Edit Event' : 'Create Event' }}</h3>
        <a href="{{ route('admin.events.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-1"></i>Back to List
        </a>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form --}}
    <form 
        action="{{ isset($event) ? route('admin.events.update', $event->id) : route('admin.events.store') }}"
        method="POST"
        enctype="multipart/form-data"
    >
        @csrf
        @if(isset($event))
            @method('PUT')
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="row g-4">

                    {{-- Left Column --}}
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Title</label>
                            <input type="text" name="title" class="form-control"
                                   value="{{ old('title', $event->title ?? '') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="4" required>{{ old('description', $event->description ?? '') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Address</label>
                            <input type="text" name="address" class="form-control"
                                   value="{{ old('address', $event->address ?? '') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Map Link</label>
                            <input type="text" name="venue_map_link" class="form-control"
                                   value="{{ old('venue_map_link', $event->venue_map_link ?? '') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Price Per Seat</label>
                            <input type="number" name="price_per_seat" class="form-control"
                                   value="{{ old('price_per_seat', $event->price_per_seat ?? '') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Speaker</label>
                            <input type="text" name="speaker" class="form-control"
                                   value="{{ old('speaker', $event->speaker ?? '') }}" required>
                        </div>
                    </div>

                    {{-- Right Column --}}
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Latitude</label>
                            <input type="text" name="lat" class="form-control"
                                   value="{{ old('lat', $event->lat ?? '') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Longitude</label>
                            <input type="text" name="long" class="form-control"
                                   value="{{ old('long', $event->long ?? '') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Agenda</label>
                            <input type="text" name="agenda" class="form-control"
                                   value="{{ old('agenda', $event->agenda ?? '') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Event Date & Time</label>
                            <input type="datetime-local" name="event_date" class="form-control"
                                   value="{{ old('event_date', isset($event) ? \Carbon\Carbon::parse($event->event_date)->format('Y-m-d\TH:i') : '') }}"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Capacity</label>
                            <input type="number" name="capacity" class="form-control"
                                   value="{{ old('capacity', $event->capacity ?? '') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Event Image</label>
                            <input type="file" name="image" class="form-control" {{ isset($event) ? '' : 'required' }}>
                            
                            @if(isset($event) && $event->image)
                                <img src="{{ asset('storage/' . $event->image) }}"
                                     class="img-thumbnail mt-2"
                                     width="120">
                            @endif
                        </div>
                    </div>

                </div>

                {{-- Submit --}}
                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-success px-4">
                        {{ isset($event) ? 'Update Event' : 'Create Event' }}
                    </button>
                </div>

            </div>
        </div>
    </form>

</div>
@endsection
