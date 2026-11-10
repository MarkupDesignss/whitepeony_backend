@extends('admin.layouts.app')
@section('title','Show Article')

@section('content')
<div class="container-fluid mt-4">
    <div class="container">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0 fw-bold">View Article</h3>
            <a href="{{ route('admin.articles.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left me-1"></i> Back to List
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">

                {{-- Meta Info --}}
                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>Author:</strong>
                        {{ $article->author->name ?? 'N/A' }}
                    </div>
                    <div class="col-md-4">
                        <strong>Views:</strong>
                        {{ $article->views ?? 0 }}
                    </div>
                    <div class="col-md-4">
                        <strong>Created:</strong>
                        {{ $article->created_at->format('d M Y') }}
                    </div>
                </div>

                {{-- Article Images --}}
                @if($article->images->count())
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Article Images</label>

                        <div class="row">
                            @foreach($article->images as $img)
                                <div class="col-md-3 col-sm-4 col-6 mb-3">
                                    <img 
                                        src="{{ asset('storage/' . $img->image_path) }}"
                                        class="img-fluid rounded border"
                                        style="height:180px;object-fit:cover;"
                                    >
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Content --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Content</label>
                    <div class="border rounded p-3 bg-light">
                        {!! nl2br(e($article->content)) !!}
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection