<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerDetail;
use App\Notifications\ReceiptNotification;
use Illuminate\Support\Facades\Notification;

class CustomerDetailsController extends Controller
{
    public function store(Request $request)
    {
        // Validate request data
        $validatedData = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'company_name' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'email' => 'required|string',
            'phone' => 'required|string',
        ]);
        
        $customerDetail = CustomerDetail::create($validatedData);

        //Send email
        $name = $validatedData["first_name"]." ".$validatedData["last_name"];
        $notification = new ReceiptNotification($name);
        Notification::route('mail', $validatedData["email"])->notify($notification);
        
        return response()->json(['message' => 'Customer details submitted successfully', 'data' => $customerDetail]);
    }
    
    public function index()
    {
        $customerDetail = CustomerDetail::all();
        return response()->json(['data' => $customerDetail]);
    }
}
