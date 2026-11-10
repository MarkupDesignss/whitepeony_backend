@extends('admin.layouts.app')
@section('title','Events')
@section('content')

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3 listing_name">
        <h3 class="mb-0 fw-bold">Events List</h3>
        <span>
            <a href="{{ route('admin.events.create') }}" class="btn btn-outline-primary">
                <i class="fa fa-plus me-1"></i><strong>ADD NEW</strong>
            </a>
        </span>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @elseif(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <div class="table-responsive">
                <table class="table table-bordered table-striped" width="100%">
                    <thead class="text-uppercase">
                        <tr>
                            <th style="width: 50px; background-color: #f1f5f9;">SN.</th>
                            <th style="width: 200px; background-color: #f1f5f9;">Title</th>
                            <th style="width: 200px; background-color: #f1f5f9;">Address</th>
                            <th style="width: 150px; background-color: #f1f5f9;">Event Date</th>
                            <th style="width: 100px; background-color: #f1f5f9;">Capacity</th>
                            <th style="width: 150px; background-color: #f1f5f9;">Image</th>
                            <th style="width: 200px; background-color: #f1f5f9;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($events as $event)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $event->title }}</td>
                            <td>{{ $event->address }}</td>
                            <td>{{ date('M d, Y h:i A', strtotime($event->event_date)) }}</td>
                            <td>{{ $event->capacity }}</td>
                            <td>
                                @if($event->image)
                                <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}" width="80">
                                @else
                                <span class="text-muted">No image</span>
                                @endif
                            </td>
                            <td class="d-flex gap-2">
                                <a href="{{ route('admin.events.registeredevents') }}" class="btn btn-info btn-sm"
                                    title="Registered Events">
                                    <i class="bi bi-people-fill"></i>
                                </a>
                                <a href="{{ route('admin.events.show', $event->id) }}" class="btn btn-primary btn-sm"
                                    title="View">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                <a href="{{ route('admin.events.edit', $event->id) }}" class="btn btn-warning btn-sm"
                                    title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('admin.events.destroy', $event->id) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Delete this event?')" title="Delete">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No events found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $events->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection