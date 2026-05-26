<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
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


    public function list(Request $request)
{
    $query = Invoice::query();

    if ($request->date_from)
        $query->whereDate('created_at', '>=', $request->date_from);

    if ($request->date_to)
        $query->whereDate('created_at', '<=', $request->date_to);

    return response()->json([
        'status' => true,
        'data'   => $query->latest()->paginate(10),
    ]);
}

public function store(Request $request)
{
    $request->validate([
        'order_ids'       => 'required|array',
        'payment_type_id' => 'required|integer',
        'total_paid'      => 'required|numeric',
    ]);

    $orders      = OrderItem::whereIn('id', $request->order_ids)->get();
    $totalAmount = $orders->sum('sub_total');

    $invoice = Invoice::create([
        'invoice_no'      => 'INV-' . time(),
        'total_amount'    => $totalAmount,
        'total_paid'      => $request->total_paid,
        'payment_type_id' => $request->payment_type_id,
        'change_amount'   => $request->total_paid - $totalAmount,
    ]);

    // ✅ លុប order_items បន្ទាប់ពីបង្កើត invoice
    OrderItem::whereIn('id', $request->order_ids)->delete();

    return response()->json(['status' => true, 'data' => $invoice]);
}

    public function show($id)
    {
        $invoice = Invoice::find($id,['*']);
        
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
    $invoice = Invoice::find($id,['*']);

    if (!$invoice) {
        return response()->json([
            'message' => 'Invoice not found'
        ], 404);
    }

    $invoice->delete();

    return response()->json([
        'message' => 'Deleted successfully'
    ]);
}
}