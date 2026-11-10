<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeatureProduct;
use App\Models\Product;
use Illuminate\Http\Request;

class FeatureProductController extends Controller
{
    public function index()
    {
        $features = FeatureProduct::with('product')
                    ->latest()
                    ->paginate(10);

        return view('admin.feature_products.index', compact('features'));
    }

    public function create()
    {
        return view('admin.feature_products.create');
    }

    public function getProducts($type)
    {
        return Product::where('product_type',$type)->select('id','name')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_type' => 'required|in:b2b,b2c',
            'product_id'   => 'required|exists:products,id',
            'status'       => 'required|in:active,inactive'
        ]);

        // Same product duplicate block
        if(FeatureProduct::where('product_id',$request->product_id)->exists()){
            return back()->withErrors(['product_id'=>'Product already featured'])->withInput();
        }

        FeatureProduct::create($request->only('product_id','product_type','status'));

        return redirect()->route('admin.feature_products.index')->with('success','Featured Product Added');
    }

    public function edit($id)
    {
        $feature = FeatureProduct::with('product')->findOrFail($id);
        return view('admin.feature_products.edit', compact('feature'));
    }

    public function update(Request $request,$id)
    {
        $request->validate([
            'product_type' => 'required|in:b2b,b2c',
            'product_id'   => 'required|exists:products,id',
            'status'       => 'required|in:active,inactive'
        ]);

        if(FeatureProduct::where('product_id',$request->product_id)->where('id','!=',$id)->exists()){
            return back()->withErrors(['product_id'=>'Product already featured'])->withInput();
        }

        FeatureProduct::findOrFail($id)->update($request->only('product_id','product_type','status'));

        return redirect()->route('admin.feature_products.index')->with('success','Featured Product Updated');
    }

    public function destroy($id)
    {
        FeatureProduct::findOrFail($id)->delete();
        return back()->with('success','Deleted Successfully');
    }
}
