<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerDetail;

class CustomerDetailsController extends Controller
{
    public function store(Request $request)
    {
        // Validate request data
        $validatedData = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
           
        ]);
        
        $customerDetail = CustomerDetail::create($validatedData);
        
        return response()->json(['message' => 'Customer details submitted successfully', 'data' => $customerDetail]);
    }
    
    public function index()
    {
        $customerDetail = CustomerDetail::with('')::all();
        return response()->json(['data' => $customerDetail]);
    }
}
