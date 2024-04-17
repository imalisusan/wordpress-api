<?php

namespace App\Http\Controllers;

use App\Models\OrderDetail;
use Illuminate\Http\Request;

class OrderDetailsController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'customer_id' => 'required|integer',
            'product' => 'required|string',
            'quantity' => 'required',
            'order_total' => 'required',
        ]);
        
        $orderDetail = OrderDetail::create($validatedData);
        
        return response()->json(['message' => 'Order details submitted successfully', 'data' => $orderDetail]);
    }
    
    public function index()
    {
        $orderDetail = OrderDetail::all();
        return response()->json(['data' => $orderDetail]);
    }
}
