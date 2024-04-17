<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentDetail;

class PaymentDetailsController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'payment_method' => 'required|string',
        ]);
        
        $paymentDetail = PaymentDetail::create($validatedData);
        
        return response()->json(['message' => 'Payment details submitted successfully', 'data' => $paymentDetail]);
    }
    
    public function index($id)
    {
        $paymentDetail = PaymentDetail::all();
        return response()->json(['data' => $paymentDetail]);
    }
}
