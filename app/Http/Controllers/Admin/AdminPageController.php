<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Page;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminPageController extends Controller
{
    public function index()
    {
        $pages = Page::latest()->paginate(10);
        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.pages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'slug' => 'required|string|unique:pages,slug',
        ]);

        try {
            $pageData = $request->only(['title', 'content', 'slug']);

            if ($request->hasFile('image')) {
                $pageData['image'] = $request->file('image')->store('pages', 'public');
            }

            Page::create($pageData);

            return redirect()->route('admin.pages.index')->with('success', 'Page created successfully');
        } catch (\Exception $e) {
            Log::error('Page Creation Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to create page: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $page = Page::findOrFail($id);
        return view('admin.pages.show', compact('page'));
    }

    public function edit($id)
    {
        $page = Page::findOrFail($id);
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, $id)
    {
        $page = Page::findOrFail($id);
    
        $request->validate([
            'title' => 'required|max:255',
            'slug' => 'required|unique:pages,slug,' . $id,
            'content' => 'required',
            'content_de' => 'nullable|string',
            'content_cz' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
    
        $data = $request->only(['title', 'slug', 'content', 'content_de', 'content_cz']);
    
        if ($request->hasFile('image')) {
            if ($page->image) {
                Storage::disk('public')->delete($page->image);
            }
            $data['image'] = $request->file('image')->store('pages', 'public');
        }
    
        $page->update($data);
    
        return redirect()->route('admin.pages.index')->with('success', 'Page Updated Successfully');
    }

    public function destroy($id)
    {
        try {
            $page = Page::findOrFail($id);
            if ($page->image) {
                Storage::disk('public')->delete($page->image);
            }
            $page->delete();
            return redirect()->route('admin.pages.index')->with('success', 'Page deleted successfully');
        } catch (\Exception $e) {
            Log::error('Page Deletion Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete page: ' . $e->getMessage());
        }
    }
}
