@extends('admin.layouts.app')
@section('title','Articles')
@section('content')

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3 listing_name">
        <h3 class="mb-0 fw-bold">Articles List</h3>
        <a href="{{ route('admin.articles.create') }}" class="btn btn-outline-primary">
            <i class="fa fa-plus me-1"></i><strong>ADD NEW</strong>
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="text-uppercase">
                        <tr>
                            <th style="width:60px;background:#f1f5f9;">#</th>
                            <th style="background:#f1f5f9;">Title</th>
                            <th style="width:140px;background:#f1f5f9;">Images</th>
                            <th style="width:120px;background:#f1f5f9;">Author</th>
                            <th style="width:180px;background:#f1f5f9;">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($articles as $article)
                            <tr>
                                <td>{{ $loop->iteration }}</td>

                                <td>
                                    <strong>{{ $article->title }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        {{ $article->images->count() }} image(s)
                                    </small>
                                </td>

                                {{-- Images --}}
                                <td>
                                    @if($article->images->count())
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($article->images->take(3) as $img)
                                                <img 
                                                    src="{{ asset('storage/' . $img->image_path) }}"
                                                    class="rounded border"
                                                    style="width:40px;height:40px;object-fit:cover;"
                                                >
                                            @endforeach

                                            @if($article->images->count() > 3)
                                                <span class="badge bg-secondary align-self-center">
                                                    +{{ $article->images->count() - 3 }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">No images</span>
                                    @endif
                                </td>

                                {{-- Author --}}
                                <td>
                                    {{ $article->author->name ?? 'N/A' }}
                                </td>

                                {{-- Actions --}}
                                <td>
                                    <a href="{{ route('admin.articles.show', $article->id) }}"
                                       class="btn btn-sm btn-info">
                                        View
                                    </a>

                                    <a href="{{ route('admin.articles.edit', $article->id) }}"
                                       class="btn btn-sm btn-warning">
                                        Edit
                                    </a>

                                    <form 
                                        action="{{ route('admin.articles.destroy', $article->id) }}" 
                                        method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Delete this article?')"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    No articles found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $articles->links() }}
                </div>

            </div>
        </div>
    </div>
</div>
@endsection