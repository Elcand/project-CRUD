<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerStoreRequest;
use App\Models\Customer;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

use function Laravel\Prompts\search;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $customers = Customer::when($request->has('search'), function ($query) use ($request) {
            $search = $request->input('search');
            $query->where('first_name', 'LIKE', "%{$search}%")
                ->orWhere('last_name', 'LIKE', "%{$search}%")
                ->orWhere('phone', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%");
        })->orderBy('id', $request->has('order') && $request->order == 'asc' ? 'asc' : 'desc')->get();


        return view('customer.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('customer.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CustomerStoreRequest $request)
    {
        $customer = new Customer();

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = $image->store('', 'public');
            $filepath  = 'storage/uploads/' . $filename;
            $customer->image = $filepath;
        }

        $customer->first_name = $request->first_name;
        $customer->last_name = $request->last_name;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        $customer->bank_account_number = $request->bank_account_number;
        $customer->about = $request->about;
        $customer->save();

        return redirect()->route('customers.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $customer = Customer::findOrFail($id);
        return view('customer.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $customer = Customer::findOrFail($id);
        return view('customer.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CustomerStoreRequest $request, string $id)
    {
        $customer = Customer::findOrFail($id);

        if ($request->hasFile('image')) {
            File::delete(public_path($customer->image));

            $image = $request->file('image');
            $filename = $image->store('', 'public');
            $filepath  = '/uploads/' . $filename;
            $customer->image = $filepath;
        }

        $customer->first_name          = $request->first_name;
        $customer->last_name           = $request->last_name;
        $customer->email               = $request->email;
        $customer->phone               = $request->phone;
        $customer->bank_account_number = $request->bank_account_number;
        $customer->about               = $request->about;
        $customer->save();

        return redirect()->route('customers.index')->with('success', 'Berhasil mengubah data');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $customer = Customer::findOrFail($id);
        File::delete(public_path($customer->image));
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Berhasil menghapus data');
    }
}
