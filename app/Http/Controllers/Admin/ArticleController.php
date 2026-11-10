<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\ArticleImage;
use Illuminate\Support\Facades\Storage;


class ArticleController extends Controller
{
    // ✅ List all articles
public function index()
{
    $articles = Article::with(['author', 'images'])
        ->latest()
        ->paginate(10);

    return view('admin.articles.index', compact('articles'));
}

    // ✅ Show create form
    public function create()
    {
        return view('admin.articles.create');
    }

    // ✅ Store new article
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'title'   => 'required|string|max:255',
    //         'content' => 'required|string',
    //         'image_url'  => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
    //         // 'status'  => 'in:draft,published,archived'
    //     ]);
    //     $imagePath = null;
    //     if ($request->hasFile('image_url')) {
    //         $imagePath = $request->file('image_url')->store('articles', 'public');
    //     }
    //     Article::create([
    //         'author_id' => Auth::id(),
    //         'title'     => $request->title,
    //         'slug'      => Str::slug($request->title) . '-' . time(),
    //         'content'   => $request->content,
    //         'image'     => $imagePath,
    //         // 'status'    => $request->status ?? 'draft',
    //     ]);

    //     return redirect()->route('admin.articles.index')->with('success', 'Article created successfully.');
    // }
    
public function details($slug)
{
    // dd("hello");
    $article = Article::where('slug', $slug)
        ->firstOrFail();
     if (!$article) {
        return response()->json([
            'success' => false,
            'message' => 'Article not found'
        ], 200);
    }
    // optional: views increment
    $article->increment('views');

    return view('admin.articles.articledetails', compact('article'));
}

public function store(Request $request)
{
    $request->validate([
        'title'   => 'required|string|max:255',
        'content' => 'required|string',
        'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
    ]);

    $article = Article::create([
        'author_id' => Auth::id(),
        'title'     => $request->title,
        'slug'      => Str::slug($request->title) . '-' . time(),
        'content'   => $request->content,
    ]);

    // ✅ Store multiple images
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $index => $image) {
            $path = $image->store('articles', 'public');

            ArticleImage::create([
                'article_id' => $article->id,
                'image_path' => $path,
                'sort_order' => $index
            ]);
        }
    }

    return redirect()
        ->route('admin.articles.index')
        ->with('success', 'Article created successfully with images.');
}


    // ✅ Show single article
public function show($id)
{
    $article = Article::with(['author', 'images'])->findOrFail($id);

    return view('admin.articles.show', compact('article'));
}

    // ✅ Show edit form
public function edit($id)
{
    $article = Article::with('images')->findOrFail($id);

    return view('admin.articles.edit', compact('article'));
}
    
    
    public function update(Request $request, $id)
{
    $article = Article::findOrFail($id);

    $request->validate([
        'title'    => 'sometimes|string|max:255',
        'content'  => 'sometimes|string',
        'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
    ]);

    $article->update([
        'title'   => $request->title ?? $article->title,
        'slug'    => $request->title
                        ? Str::slug($request->title) . '-' . time()
                        : $article->slug,
        'content' => $request->content ?? $article->content,
    ]);

    // ✅ Add new images (old images stay)
    if ($request->hasFile('images')) {
        $currentCount = $article->images()->count();

        foreach ($request->file('images') as $index => $image) {
            $path = $image->store('articles', 'public');

            ArticleImage::create([
                'article_id' => $article->id,
                'image_path' => $path,
                'sort_order' => $currentCount + $index
            ]);
        }
    }

    return redirect()
        ->route('admin.articles.index')
        ->with('success', 'Article updated successfully.');
}

    // ✅ Update article
    // public function update(Request $request, $id)
    // {
    //     $article = Article::findOrFail($id);

    //     $request->validate([
    //         'title'   => 'sometimes|string|max:255',
    //         'content' => 'sometimes|string',
    //         'image_url'   => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
    //         // 'status'  => 'in:draft,published,archived'
    //     ]);

    //     if ($request->hasFile('image_url')) {
    //         $imagePath = $request->file('image_url')->store('articles', 'public');
    //     } else {
    //         $imagePath = $article->image;
    //     }

    //     $article->update([
    //         'title'   => $request->title ?? $article->title,
    //         'slug'    => $request->title ? Str::slug($request->title) . '-' . time() : $article->slug,
    //         'content' => $request->content ?? $article->content,
    //         'image'   => $imagePath,
    //         // 'status'  => $request->status ?? $article->status,
    //     ]);

    //     return redirect()->route('admin.articles.index')->with('success', 'Article updated successfully.');
    // }


    public function destroy($id)
{
    $article = Article::with('images')->findOrFail($id);

    // ✅ Delete image files from storage
    foreach ($article->images as $image) {
        if ($image->image_path && Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }
    }

    // ✅ Delete article (article_images rows auto delete due to FK cascade)
    $article->delete();

    return redirect()
        ->route('admin.articles.index')
        ->with('success', 'Article and all related images deleted successfully.');
}

    // ✅ Delete article
    // public function destroy($id)
    // {
    //     $article = Article::findOrFail($id);
    //     $article->delete();

    //     return redirect()->route('admin.articles.index')->with('success', 'Article deleted successfully.');
    // }
}