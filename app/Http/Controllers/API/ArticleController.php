<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    // public function index()
    // {
    //     // $articles = Article::with('author')->latest()->get();
    //     $articles = Article::latest()->get();

    //     // Get max views from table
    //     $maxViews = Article::max('views'); 

    //     // Add is_trending attribute
    //     $articles->map(function($article) use ($maxViews) {
    //         $article->is_trending = ($article->views == $maxViews && $maxViews > 0);
    //         return $article;
    //     });

    //     return response()->json([
    //         'success' => true,
    //         'message' => "Articles fetched successfully",
    //         'data' => $articles
    //     ]);
    // }
    
public function index()
{
    $articles = Article::with(['author', 'images'])
        ->latest()
        ->get();

    // Get max views once
    $maxViews = Article::max('views');

    $articles->transform(function ($article) use ($maxViews) {

        // Trending flag
        $article->is_trending = ($maxViews > 0 && $article->views == $maxViews);

        // Convert image_path to full URL
        $article->images->transform(function ($image) {
            $image->image_path = asset('storage/' . $image->image_path);
            return $image;
        });

        return $article;
    });

    return response()->json([
        'success' => true,
        'message' => 'Articles fetched successfully',
        'data'    => $articles
    ]);
}

public function show($id)
{
    $article = Article::with(['author', 'images'])->find($id);

    if (!$article) {
        return response()->json([
            'success' => false,
            'message' => 'Article not found'
        ], 200);
    }

    // Increment views
    $article->increment('views');
    $article->refresh();

    // Get updated max views
    $maxViews = Article::max('views');
    $article->is_trending = ($maxViews > 0 && $article->views == $maxViews);

    // Convert image_path to full URL
    $article->images->transform(function ($image) {
        $image->image_path = asset('storage/' . $image->image_path);
        return $image;
    });

    // ✅ Generate share URL (slug based)
    $article->share_url = url(
        '/article/' . $article->slug
    );

    return response()->json([
        'success' => true,
        'message' => 'Article fetched successfully',
        'data'    => $article
    ]);
}

public function showbyslug(string $slug)
{
    // Fetch article by slug with relations
    $article = Article::with(['author', 'images'])
        ->where('slug', $slug)
        ->firstOrFail();

    // Increment views
    $article->increment('views');
    $article->refresh();

    // Get max views to determine trending
    $maxViews = Article::max('views');
    $article->is_trending = ($maxViews > 0 && $article->views === $maxViews);

    // Convert image_path to full URL
    $article->images->transform(function ($image) {
        $image->image_path = asset('storage/' . $image->image_path);
        return $image;
    });

    // Generate share URL (slug based)
    $article->share_url = url('/article/' . $article->slug);

    return response()->json([
        'success' => true,
        'message' => 'Article fetched successfully',
        'data'    => $article
    ], 200);
}

// public function show($id)
// {
//     $article = Article::with(['author', 'images'])->find($id);

//     if (!$article) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Article not found'
//         ], 200);
//     }

//     // Increment views
//     $article->increment('views');
//     $article->refresh();

//     // Get updated max views
//     $maxViews = Article::max('views');

//     $article->is_trending = ($maxViews > 0 && $article->views == $maxViews);

//     // Convert image_path to full URL
//     $article->images->transform(function ($image) {
//         $image->image_path = asset('storage/' . $image->image_path);
//         return $image;
//     });

//     return response()->json([
//         'success' => true,
//         'message' => 'Article fetched successfully',
//         'data'    => $article
//     ]);
// }
    
}