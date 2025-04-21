<?php

namespace App\Http\Controllers;
use App\Models\Order;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function index(){

        $orderItems = Order::with('book')
            ->where('userid', auth()->id())
            ->get();

        return view('orders.index', compact('orderItems'));
        
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
       // return redirect()->route('order.index')->with('success', 'Order added successfully.');
       return redirect()->route('order.ordered');
    }

    public function download(Request $request)
    {
        // Logic to handle downloading an order or related file
        // For example, you might return a file response
        // return response()->download($filePath);

        return response()->json(['message' => 'Download initiated.']);
    }

    public function downloadOrder($userId, $orderNum)
    {
        // Fetch the grouped cart items
        $groupedCartItems = Order::with(['book', 'user'])->get()->groupBy('userid')->map(function ($orders) {
            $user = $orders->first()->user;
            return [
                'user' => $user,
                'orders' => $orders->groupBy('ordernum')
            ];
        });
    
        // Get the specific order
        $order = $groupedCartItems[$userId]['orders'][$orderNum];
    
        // Prepare the content for the text file with book IDs
        $url_ids = Array();
        $finalContent = "";
        foreach ($order as $cartItem) {
            $url_id = $cartItem->book->url_id . ".txt"; // 
            $booksNum = $cartItem->quantity;
            $orderDate = $cartItem->created_at;
            $orderNum = $cartItem->ordernum;

        
        $to_add = "#910: ^AU^1{".$booksNum."}^DХР^D{".$orderDate."}^E{цена экз.}^Y{".$orderNum."}\r\n";//это нужно добавить в начало каждой записи
        //foreach($url_ids as $url_id){
            $fileContent = Storage::get(trim($url_id)) . "\r\n";
            $finalContent .= $to_add . $fileContent;
                       
        }

        

        // Define the file name
        $fileName = 'order_' . $orderNum . '.txt';
    
        // Stream the content as a downloadable file
        return response()->stream(function () use ($finalContent) {
            echo $finalContent; // Output the content
        }, 200, [
            'Content-Type' => 'text/plain',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
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

     public function ordered()
     {
        return redirect()->route('order.ordered');
     }
}
