<?php

namespace App\Http\Controllers;
use App\Models\Order;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(){

        $orderItems = Order::with('book')
            ->where('userid', auth()->id())
            ->get();

        return view('orders.index', compact('orderItems'));

        //$orders = Order::all();
        //return view('orders.index', compact('orders'));
        
    }

    public function add(Request $request){
        // Validate the request data
        $validatedData = $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
        ]);

        // Logic to add the order
        // For example, you might save the order to the database
        // Order::create($validatedData);
        return redirect()->route('order.index')->with('success', 'Order added successfully.');
    }

    public function download(Request $request)
    {
        // Logic to handle downloading an order or related file
        // For example, you might return a file response
        // return response()->download($filePath);

        return response()->json(['message' => 'Download initiated.']);
    }

     // Remove an order
     public function remove(Request $request)
     {
         // Validate the request data
         $validatedData = $request->validate([
             'order_id' => 'required|integer',
         ]);
 
         // Logic to remove the order
         // For example, you might delete the order from the database
         // Order::destroy($validatedData['order_id']);
 
         return response()->json(['message' => 'Order removed successfully.']);
     }
}
