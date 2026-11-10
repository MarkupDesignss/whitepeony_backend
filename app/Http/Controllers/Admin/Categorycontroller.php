<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class Categorycontroller extends Controller
{
    public function index()
    {
        $categories = Category::latest()->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|max:255',
            'category_type' => 'required|in:primary,secondary,other',
            'position'      => 'required|integer|min:1',
            'icon'          => 'required|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
            'image'         => 'required|image|mimes:png,jpg,jpeg,webp|max:5000',
            'description'   => 'nullable'
        ]);

        $data = $request->only('name','category_type','position', 'description');

        $data['icon']  = $request->file('icon')->store('categories/icons','public');
        $data['image'] = $request->file('image')->store('categories/images','public');

        Category::create($data);

        return redirect()->route('admin.categories.index')->with('success','Category Created Successfully');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name'          => 'required|max:255',
            'category_type' => 'required|in:primary,secondary,other',
            'position'      => 'required|integer|min:1',
            'icon'          => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
            'image'         => 'nullable|image|mimes:png,jpg,jpeg,webp|max:5000',
            'description'   => 'nullable'
        ]);

        $data = $request->only('name','category_type','position','description');

        if ($request->hasFile('icon')) {
            Storage::disk('public')->delete($category->icon);
            $data['icon'] = $request->file('icon')->store('categories/icons','public');
        }

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($category->image);
            $data['image'] = $request->file('image')->store('categories/images','public');
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success','Category Updated Successfully');
    }

    public function destroy(Category $category)
    {
        Storage::disk('public')->delete([$category->icon, $category->image]);
        $category->delete();
        return back()->with('success','Category Deleted Successfully');
    }
}