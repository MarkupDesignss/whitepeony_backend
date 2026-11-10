<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function show($slug)
    {
        $page = Page::where('slug', $slug)->firstOrFail();
        
        // Use only English content for now
        $content = $page->content;
        
        return view('page', [
            'title' => $page->title,
            'content' => $content,
            'image' => $page->image ? asset('storage/' . $page->image) : null,
        ]);
    }
}