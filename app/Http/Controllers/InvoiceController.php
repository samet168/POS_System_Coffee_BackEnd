<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::all();
        return response()->json([
            'status' => true,
            'message' => 'Invoices retrieved successfully',
            'data' => $invoices
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id'          => 'required|exists:orders,_id|unique:invoices,order_id',
            'payment_status_id' => 'required|exists:payment_statuses,_id',
            'payment_type_id'   => 'required|exists:payment_types,_id',
            'total_paid'        => 'required|numeric|min:0',
        ]);

        $order = \App\Models\Order::findOrFail($request->order_id);

        $invoice = Invoice::create([
            'order_id'          => $request->order_id,
            'invoice_no'        => 'INV-' . time() . rand(1000, 9999),
            'payment_status_id' => $request->payment_status_id,
            'payment_type_id'   => $request->payment_type_id,
            'total_paid'        => $request->total_paid,
            'change_amount'     => max(0, $request->total_paid - $order->total_amount),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Invoice created successfully',
            'data' => $invoice
        ], 201);
    }

    public function show($id)
    {
        $invoice = Invoice::with(['order.orderItems.item'])->find($id);
        if (!$invoice) return response()->json(['status' => false, 'message' => 'Invoice not found'], 404);
        return response()->json(['status' => true, 'data' => $invoice], 200);
    }
}