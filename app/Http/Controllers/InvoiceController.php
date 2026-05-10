<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
public function index()
    {

        $invoices = Invoice::all();
        $invoices->map(function ($invoice) {
            if (isset($invoice->order_ids) && is_array($invoice->order_ids)) {
                $invoice->orders_details = Order::whereIn('_id', $invoice->order_ids)
                    ->select(['_id', 'table_number', 'total_amount', 'created_at'])
                    ->get();
            }
            return $invoice;
        });

        return response()->json([
            'status' => true,
            'message' => 'Invoices retrieved successfully',
            'data' => $invoices
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_ids'         => 'required|array|min:1', 
            'order_ids.*'       => 'exists:orders,_id',
            'payment_status_id' => 'required',
            'payment_type_id'   => 'required',
            'total_paid'        => 'required|numeric|min:0',
        ]);

        try {
        
            $orders = Order::whereIn('_id', $request->order_ids)->get();
            

            $totalAmountAllOrders = $orders->sum('total_amount');

            $changeAmount = max(0, $request->total_paid - $totalAmountAllOrders);

        
            $invoice = Invoice::create([
                'order_ids'         => $request->order_ids,
                'invoice_no'        => 'INV-' . time() . rand(100, 999),
                'payment_status_id' => $request->payment_status_id,
                'payment_type_id'   => $request->payment_type_id,
                'total_amount'      => $totalAmountAllOrders,
                'total_paid'        => $request->total_paid,
                'change_amount'     => $changeAmount,
            ]);

    
            Order::whereIn('_id', $request->order_ids)->update([
                'order_status_id' => $request->payment_status_id 
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Invoice created successfully for ' . $orders->count() . ' orders',
                'data' => $invoice
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $invoice = Invoice::find($id, ['*']);
        
        if (!$invoice) {
            return response()->json(['status' => false, 'message' => 'Invoice not found'], 404);
        }

       
        $orders = Order::with(['orderItems.item', 'orderItems.size'])
                    ->whereIn('_id', $invoice->order_ids)
                    ->get();

        return response()->json([
            'status' => true,
            'data' => [
                'invoice' => $invoice,
                'orders'  => $orders
            ]
        ], 200);
    }

    public function destroy($id)
    {
        $invoice = Invoice::find($id, ['*']);
        if (!$invoice) return response()->json(['status' => false, 'message' => 'Invoice not found'], 404);

        $invoice->delete();
        return response()->json(['status' => true, 'message' => 'Invoice deleted successfully'], 200);
    }
}