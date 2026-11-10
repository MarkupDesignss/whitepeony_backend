<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Tier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    
    public function updateB2BVat(Request $request)
{
    // dd($request->all());
    $request->validate([
        'vat_percent' => 'required|numeric|min:0|max:100'
    ]);
    try {

        // Update VAT for all B2B customers
        Customer::where('type', 'b2b')->update([
            'vat_percentage' => $request->vat_percent
        ]);

        return redirect()->back()->with('success', 'VAT percentage updated for all B2B customers.');

    } catch (\Exception $e) {

        return redirect()->back()->with('error', 'Something went wrong.');
    }
}
    public function index() {
        $customers = Customer::with('tier')->get();
        $tiers = Tier::all();
        return view('admin.customers.index', compact('customers', 'tiers'));
    }

    // public function b2cIndex()
    // {
    //     $customers = Customer::where('type', 'b2c')->get();
    //     $tiers = DB::table('tiers')->pluck('type', 'id');
        
    //     return view('admin.customers.b2c.index', compact('customers'));
    // }

    public function b2cIndex()
    {
        // 10 customers per page
        $customers = Customer::where('type', 'b2c')->paginate(10); 

        $tiers = DB::table('tiers')->pluck('type', 'id');

        return view('admin.customers.b2c.index', compact('customers', 'tiers'));
    }

    public function b2bIndex()
    {
        // 10 customers per page
        $customers = Customer::where('type', 'b2b')->paginate(10);

        return view('admin.customers.b2b.index', compact('customers'));
    }


    public function view($id)
    {
        $customer = DB::table('customers')
            ->where('id', $id)
            ->first();

        if (!$customer) {
            abort(404, 'Customer not found.');
        }

        $addresses = DB::table('addresses')
            ->where('customer_id', $id)
            ->get();

        return view('admin.customers.show', compact('customer', 'addresses'));
    }

    public function create(){
        return view('admin.customers.create');
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            // Customer fields
            'email' => 'required|email|unique:customers,email',
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'type' => 'required|in:b2b,b2c',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'business_name' => 'nullable|string|max:255',
            // 'tier_id' => 'nullable|in:1,2,3,4',
            'vat_id' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            $validated['profile_image'] = $request->file('profile_image')->store('customers', 'public');
        }

        // Separate customer fields
        $customerData = collect($validated)->only([
            'email', 'name', 'phone', 'type', 'profile_image',
            'business_name', 'tier_id', 'vat_id', 'status'
        ])->toArray();

        // Create the customer
        $customer = Customer::create($customerData);

        return redirect()
            ->route('admin.customers.b2b')
            ->with('success', 'Customer created successfully.');
    }

    public function toggleStatus($id)
    {
        $customer = DB::table('customers')->where('id', $id)->first();
        if (!$customer) {
            return redirect()->back()->with('error', 'Customer not found.');
        }
        $newStatus = $customer->status === 'active' ? 'blocked' : 'active';

        DB::table('customers')->where('id', $id)->update([
            'status' => $newStatus,
            'updated_at' => now()
        ]);

        return redirect()->back()->with('success', "Customer status updated to {$newStatus}.");
    }

    public function updateTier(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:customers,id',
            'tier_id' => 'required|exists:tiers,id',
        ]);

        $customer = Customer::findOrFail($request->id);
        $customer->tier_id = $request->tier_id;
        $customer->save();

        $tierName = $customer->tier->name;

        return response()->json([
            'success' => true,
            'message' => 'Tier updated successfully',
            'tier_name' => $tierName
        ]);
    }

        public function edit($id)
    {
        $customer = Customer::with('address')->findOrFail($id);
        $address = Address::where('customer_id', $id)->first();
        return view('admin.customers.b2b.edit', compact('customer', 'address'));
    }

    // public function update(Request $request, $id)
    // {
       
    //     $customer = Customer::findOrFail($id);

    //     $validated = $request->validate([
    //         'email' => 'required|email|unique:customers,email,' . $customer->id,
    //         'name' => 'required|string|max:255',
    //         'phone' => 'nullable|string|max:20',
    //         'type' => 'required|in:b2b,b2c',
    //         'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    //         'business_name' => 'nullable|string|max:255',
    //         'tier_id' => 'nullable|in:1,2,3,4',
    //         'vat_id' => 'nullable|string|max:255',
            
    //         // Address fields
    //         'full_address' => 'nullable|string|max:500',
    //         'address_type' => 'nullable|in:home,office,hotel,other',
    //         'postal_code'  => 'nullable|string|max:20',
    //     ]);

    //     // Handle profile image update
    //     if ($request->hasFile('profile_image')) {
    //         // Delete old image if exists
    //         if ($customer->profile_image && Storage::disk('public')->exists($customer->profile_image)) {
    //             Storage::disk('public')->delete($customer->profile_image);
    //         }
    //         $validated['profile_image'] = $request->file('profile_image')->store('customers', 'public');
    //     }

    //     $customer->update($validated);
    //     $redirectRoute = $customer->type === 'b2b' 
    //         ? 'admin.customers.b2b' 
    //         : 'admin.customers.b2c';

    //     return redirect()
    //         ->route('admin.customers.b2b')
    //         ->with('success', 'Customer updated successfully.');
    // }
    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);
    
        $validated = $request->validate([
            'email' => 'required|email|unique:customers,email,' . $customer->id,
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'type' => 'required|in:b2b,b2c',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'business_name' => 'nullable|string|max:255',
            'tier_id' => 'nullable|in:1,2,3,4',
            'vat_id' => 'nullable|string|max:255',
    
            // Address fields
            'full_address' => 'nullable|string|max:500',
            'address_type' => 'nullable|in:home,office,hotel,other',
            'postal_code'  => 'nullable|string|max:20',
        ]);
    
        // Handle profile image
        if ($request->hasFile('profile_image')) {
            if ($customer->profile_image && Storage::disk('public')->exists($customer->profile_image)) {
                Storage::disk('public')->delete($customer->profile_image);
            }
    
            $validated['profile_image'] = $request->file('profile_image')
                ->store('customers', 'public');
        }
    
        /*
        |--------------------------------------------------------------------------
        | 1️⃣ Update Customer Table (only customer fields)
        |--------------------------------------------------------------------------
        */
        $customer->update([
            'email' => $validated['email'],
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'type' => $validated['type'],
            'profile_image' => $validated['profile_image'] ?? $customer->profile_image,
            'business_name' => $validated['business_name'],
            'tier_id' => $validated['tier_id'],
            'vat_id' => $validated['vat_id'],
        ]);
    
        /*
        |--------------------------------------------------------------------------
        | 2️⃣ Update / Create Address (addresses table)
        |--------------------------------------------------------------------------
        */
        Address::updateOrCreate(
            ['customer_id' => $customer->id], // condition
            [
                'full_address' => $validated['full_address'] ?? null,
                'address_type' => $validated['address_type'] ?? null,
                'postal_code'  => $validated['postal_code'] ?? null,
            ]
        );
    
        /*
        |--------------------------------------------------------------------------
        | Redirect
        |--------------------------------------------------------------------------
        */
        $redirectRoute = $customer->type === 'b2b'
            ? 'admin.customers.b2b'
            : 'admin.customers.b2c';
    
        return redirect()
            ->route($redirectRoute)
            ->with('success', 'Customer updated successfully.');
    }
}