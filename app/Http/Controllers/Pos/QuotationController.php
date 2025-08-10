<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Models\QuotationDetail;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Auth;
use DB;
use PDF;

class QuotationController extends Controller
{
    public function index()
    {
        $quotations = Quotation::with('customer')->latest()->get();
        return view('backend.quotation.index', compact('quotations'));
    }

    public function create()
    {
        $customers = Customer::all();
        $products = Product::all();
        $categories = Category::all();

        $last = Quotation::latest()->first();
        $quotation_no = $last ? $last->quotation_no + 1 : 1;

        return view('backend.quotation.create', compact('customers', 'products', 'categories', 'quotation_no'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'date' => 'required|date',
            'valid_until' => 'required|date',
            'product_id' => 'required|array',
            'quantity' => 'required|array',
            'unit_price' => 'required|array',
        ]);

        DB::transaction(function() use($request) {
            $totalAmount = 0;
            foreach ($request->quantity as $index => $qty) {
                $totalAmount += $qty * $request->unit_price[$index];
            }
            $totalAfterDiscount = $totalAmount - ($request->discount_amount ?? 0);

            $quotation = Quotation::create([
                'quotation_no' => $request->quotation_no,
                'customer_id' => $request->customer_id,
                'date' => $request->date,
                'valid_until' => $request->valid_until,
                'description' => $request->description,
                'total_amount' => $totalAfterDiscount,
                'discount_amount' => $request->discount_amount ?? 0,
                'status' => 'pending',
                'created_by' => Auth::id(),
            ]);

            foreach ($request->product_id as $index => $productId) {
                QuotationDetail::create([
                    'quotation_id' => $quotation->id,
                    'product_id' => $productId,
                    'category_id' => $request->category_id[$index],
                    'quantity' => $request->quantity[$index],
                    'unit_price' => $request->unit_price[$index],
                    'total_price' => $request->quantity[$index] * $request->unit_price[$index],
                ]);
            }
        });

        return redirect()->route('quotation.index')->with('success', 'تم حفظ عرض السعر بنجاح');
    }

    public function show($id)
    {
        $quotation = Quotation::with(['customer', 'quotationDetails.product'])->findOrFail($id);
        return view('backend.quotation.show', compact('quotation'));
    }

    public function edit($id)
    {
        $quotation = Quotation::with('quotationDetails.product')->findOrFail($id);
        $customers = Customer::all();
        $products = Product::all();
        $categories = Category::all();
        
        return view('backend.quotation.edit', compact('quotation', 'customers', 'products', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'date' => 'required|date',
            'valid_until' => 'required|date',
            'product_id' => 'required|array',
            'quantity' => 'required|array',
            'unit_price' => 'required|array',
        ]);

        DB::transaction(function() use($request, $id) {
            $quotation = Quotation::findOrFail($id);
            
            // حذف التفاصيل القديمة
            QuotationDetail::where('quotation_id', $quotation->id)->delete();
            
            $totalAmount = 0;
            foreach ($request->quantity as $index => $qty) {
                $totalAmount += $qty * $request->unit_price[$index];
            }
            $totalAfterDiscount = $totalAmount - ($request->discount_amount ?? 0);

            $quotation->update([
                'customer_id' => $request->customer_id,
                'date' => $request->date,
                'valid_until' => $request->valid_until,
                'description' => $request->description,
                'total_amount' => $totalAfterDiscount,
                'discount_amount' => $request->discount_amount ?? 0,
            ]);

            foreach ($request->product_id as $index => $productId) {
                QuotationDetail::create([
                    'quotation_id' => $quotation->id,
                    'product_id' => $productId,
                    'category_id' => $request->category_id[$index],
                    'quantity' => $request->quantity[$index],
                    'unit_price' => $request->unit_price[$index],
                    'total_price' => $request->quantity[$index] * $request->unit_price[$index],
                ]);
            }
        });

        return redirect()->route('quotation.index')->with('success', 'تم تحديث عرض السعر بنجاح');
    }

    public function destroy($id)
    {
        $quotation = Quotation::findOrFail($id);
        QuotationDetail::where('quotation_id', $quotation->id)->delete();
        $quotation->delete();
        
        return redirect()->route('quotation.index')->with('success', 'تم حذف عرض السعر بنجاح');
    }

    public function convertToOrder($id)
    {
        $quotation = Quotation::with('quotationDetails.product')->findOrFail($id);

        // إنشاء طلبية من عرض السعر
        $order = Order::create([
            'order_no' => 'ORD-' . time(),
            'customer_id' => $quotation->customer_id,
            'date' => now(),
            'description' => 'تحويل من عرض سعر #' . $quotation->quotation_no,
            'total_amount' => $quotation->total_amount,
            'discount_amount' => $quotation->discount_amount,
            'status' => 'pending',
            'created_by' => Auth::id(),
        ]);

        foreach ($quotation->quotationDetails as $detail) {
            OrderDetail::create([
                'order_id' => $order->id,
                'product_id' => $detail->product_id,
                'category_id' => $detail->category_id,
                'quantity' => $detail->quantity,
                'unit_price' => $detail->unit_price,
                'total_price' => $detail->total_price,
            ]);
        }

        $quotation->update(['status' => 'converted']);

        return redirect()->route('order.edit', $order->id)->with('success', 'تم تحويل عرض السعر إلى طلبية');
    }

    public function print($id)
    {
        $quotation = Quotation::with('customer', 'quotationDetails.product')->findOrFail($id);
        $pdf = PDF::loadView('backend.pdf.quotation_pdf', compact('quotation'));
        return $pdf->stream('quotation_'.$quotation->quotation_no.'.pdf');
    }

    public function approve($id)
    {
        $quotation = Quotation::findOrFail($id);
        $quotation->update(['status' => 'approved']);
        return redirect()->back()->with('success', 'تم اعتماد عرض السعر بنجاح');
    }

    public function reject($id)
    {
        $quotation = Quotation::findOrFail($id);
        $quotation->update(['status' => 'rejected']);
        return redirect()->back()->with('success', 'تم رفض عرض السعر بنجاح');
    }
}