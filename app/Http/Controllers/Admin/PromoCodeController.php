<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promocode;
use App\Models\Tier;
use Illuminate\Http\Request;

class PromoCodeController extends Controller
{
    public function index(Request $request)
    {
        $activeType = $request->get('type', );
        $b2bPromocodes = Promocode::with('tier')->where('customer_type', 'b2b')
            ->latest()->paginate(10, ['*'], 'b2b_page');
        
        $b2cPromocodes = Promocode::with('tier')->where('customer_type', 'b2c')
            ->latest()->paginate(10, ['*'], 'b2c_page');

        return view('admin.promocodes.index', compact('b2bPromocodes', 'b2cPromocodes', 'activeType'));
    }

    public function create()
    {
        return view('admin.promocodes.create');
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'title' => 'required|string|max:255',
            'discount_type' => 'required|in:percentage,fixed',
            'customer_type' => 'required|in:b2b,b2c',
            'tier_id' => 'nullable',
            'discount_value' => 'required|numeric',
            'max_discount' => 'required|numeric',
            'usage_limit' => 'required|numeric',
            'coupon_code' => 'required|string|unique:promocodes,coupon_code',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);
    // dd($request->all());
        Promocode::create($request->all());

        return redirect()->route('admin.promocodes.index')->with('success', 'Promo code created successfully!');
    }

    public function edit(Promocode $promocode)
    {
        return view('admin.promocodes.edit', compact('promocode'));
    }

    public function update(Request $request, Promocode $promocode)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'discount_type' => 'required|in:percentage,fixed',
            'customer_type' => 'required|in:b2b,b2c',
            'tier_id' => 'nullable',
            'discount_value' => 'required|numeric',
            'usage_limit' => 'required|numeric',
            'max_discount' => 'required|numeric',
            'coupon_code' => 'required|string|unique:promocodes,coupon_code,' . $promocode->id,
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $promocode->update($request->all());
        
        return redirect()->route('admin.promocodes.index')->with('success', 'Promo code updated successfully!');
    }

    public function destroy(Promocode $promocode)
    {
        $promocode->delete();
        return redirect()->route('admin.promocodes.index')->with('success', 'Promo code deleted successfully!');
    }

    public function show(Promocode $promocode)
    {
        $tier = Tier::where('id',$promocode->tier_id)->first();
        return view('admin.promocodes.show', compact('promocode','tier'));
    }

}