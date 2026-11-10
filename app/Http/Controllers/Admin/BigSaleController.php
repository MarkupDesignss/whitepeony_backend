<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BigSale;
use App\Models\Product;

class BigSaleController extends Controller
{
    public function index()
    {
        $bigSales = BigSale::with('product')
                    ->latest()
                    ->paginate(10);

        return view('admin.big_sales.index', compact('bigSales'));
    }

    public function create()
    {
        return view('admin.big_sales.create');
    }

    public function getProducts($type)
    {
        $products = Product::where('product_type', $type)->get();
        return response()->json($products);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_type' => 'required|in:b2b,b2c',
            'start_date'   => 'required|date',
            'end_date'     => 'required|date|after:start_date',
            'status'       => 'required',
            'products'     => 'required|array',
        ]);

        foreach ($request->products as $pid)
        {
            BigSale::create([
                'product_type' => $request->product_type,
                'product_id'   => $pid,
                'start_date'   => $request->start_date,
                'end_date'     => $request->end_date,
                'percentage'   => $request->percentages[$pid],
                'status'       => $request->status,
            ]);
        }

        return redirect()->route('admin.big_sales.index')->with('success','Big Sale Created Successfully');
    }

    public function destroy($id)
    {
        $bigSale = BigSale::findOrFail($id);
        $bigSale->delete();

        return redirect()->route('admin.big_sales.index')->with('success','Big Sale Deleted Successfully');
    }
}