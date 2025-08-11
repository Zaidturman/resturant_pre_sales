<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Category;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    /**
     * عرض قائمة الطلبيات مع دعم البحث عن الزبائن
     */
    public function index(Request $request)
    {
        $orders = Order::with('customer')->latest()->get();

        $customers = collect();
        $customerQuery = $request->get('customer_query');

        if ($customerQuery) {
            $customers = Customer::where('name', 'like', "%{$customerQuery}%")
                ->orWhere('mobile_no', 'like', "%{$customerQuery}%")
                ->get();
        }

        return view('backend.order.index', compact('orders', 'customers', 'customerQuery'));
    }

    /**
     * عرض نموذج إنشاء طلبية جديدة
     */
    public function create()
    {
        $customers = Customer::all();
        $products = Product::with(['category', 'unit'])->get();
        $categories = Category::all();

        $lastOrder = Order::latest()->first();
        $order_no = $lastOrder ? $lastOrder->order_no + 1 : 1;

        return view('backend.order.create', compact('customers', 'products', 'categories', 'order_no'));
    }

    /**
     * حفظ طلبية جديدة
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'date' => 'required|date',
            'product_id' => 'required|array',
            'product_id.*' => 'exists:products,id',
            'quantity' => 'required|array',
            'unit_price' => 'required|array',
            'category_id' => 'required|array',
        ]);

        DB::transaction(function () use ($request) {
            $totalAmount = 0;
            foreach ($request->quantity as $index => $qty) {
                $totalAmount += $qty * $request->unit_price[$index];
            }
            $totalAfterDiscount = $totalAmount - ($request->discount_amount ?? 0);

            $order = Order::create([
                'order_no' => $request->order_no,
                'customer_id' => $request->customer_id,
                'date' => $request->date,
                'description' => $request->description,
                'total_amount' => $totalAfterDiscount,
                'discount_amount' => $request->discount_amount ?? 0,
                'status' => 'pending',
                'created_by' => Auth::id(),
            ]);

            foreach ($request->product_id as $index => $productId) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'category_id' => $request->category_id[$index],
                    'quantity' => $request->quantity[$index],
                    'unit_price' => $request->unit_price[$index],
                    'total_price' => $request->quantity[$index] * $request->unit_price[$index],
                ]);
            }
        });

        return redirect()->route('order.index')->with('success', 'تم حفظ الطلبية بنجاح');
    }

    /**
     * عرض تفاصيل طلبية
     */
    public function show($id)
    {
        $order = Order::with(['customer', 'orderDetails.product.category', 'orderDetails.product.unit'])->findOrFail($id);
        return view('backend.order.show', compact('order'));
    }

    /**
     * عرض نموذج تعديل طلبية
     */
    public function edit($id)
    {
        $order = Order::with(['orderDetails.product', 'customer'])->findOrFail($id);
        $customers = Customer::all();
        $products = Product::with(['category', 'unit'])->get();
        $categories = Category::all();

        return view('backend.order.edit', compact('order', 'customers', 'products', 'categories'));
    }

    /**
     * تحديث طلبية
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'date' => 'required|date',
            'product_id' => 'required|array',
            'quantity' => 'required|array',
            'unit_price' => 'required|array',
        ]);

        DB::transaction(function () use ($request, $id) {
            $order = Order::findOrFail($id);
            OrderDetail::where('order_id', $order->id)->delete();

            $totalAmount = 0;
            foreach ($request->quantity as $index => $qty) {
                $totalAmount += $qty * $request->unit_price[$index];
            }
            $totalAfterDiscount = $totalAmount - ($request->discount_amount ?? 0);

            $order->update([
                'customer_id' => $request->customer_id,
                'date' => $request->date,
                'description' => $request->description,
                'total_amount' => $totalAfterDiscount,
                'discount_amount' => $request->discount_amount ?? 0,
            ]);

            foreach ($request->product_id as $index => $productId) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'category_id' => $request->category_id[$index],
                    'quantity' => $request->quantity[$index],
                    'unit_price' => $request->unit_price[$index],
                    'total_price' => $request->quantity[$index] * $request->unit_price[$index],
                ]);
            }
        });

        return redirect()->route('order.index')->with('success', 'تم تحديث الطلبية بنجاح');
    }

    /**
     * حذف طلبية
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        OrderDetail::where('order_id', $order->id)->delete();
        $order->delete();

        return redirect()->route('order.index')->with('success', 'تم حذف الطلبية بنجاح');
    }

    /**
     * طباعة الطلبية كـ PDF
     */
    public function print($id)
    {
        $order = Order::with(['customer', 'orderDetails.product'])->findOrFail($id);

        $filename = 'طلبية_' . str_replace(['/', '\\'], '-', $order->order_no) . '.pdf';

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'default_font' => 'dejavusans',
            'default_font_size' => 12,
            'directionality' => 'rtl'
        ]);

        $html = view('backend.order.order_pdf', compact('order'))->render();
        $mpdf->WriteHTML($html);

        return $mpdf->Output($filename, 'I'); // عرض في المتصفح
    }

    /**
     * تحويل الطلبية إلى فاتورة
     */
    public function convertToInvoice($id)
    {
        $order = Order::with('orderDetails.product')->findOrFail($id);

        $invoice = Invoice::create([
            'invoice_no' => 'INV-' . time(),
            'customer_id' => $order->customer_id,
            'date' => now(),
            'description' => 'تحويل من طلبية #' . $order->order_no,
            'total_amount' => $order->total_amount,
            'discount_amount' => $order->discount_amount,
            'status' => 'pending',
            'created_by' => Auth::user()->id,

        ]);

        foreach ($order->orderDetails as $detail) {
            InvoiceDetail::create([
                'invoice_id' => $invoice->id,
                'product_id' => $detail->product_id,
                'category_id' => $detail->category_id,
                'selling_qty' => $detail->quantity,
                'unit_price' => $detail->unit_price,
                'selling_price' => $detail->total_price,
            ]);
        }

        $order->update(['status' => 'converted']);

        return redirect()->route('invoice.edit', $invoice->id)->with('success', 'تم تحويل الطلبية إلى فاتورة');
    }

    /**
     * اعتماد الطلبية
     */
    public function approve($id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => 'approved']);
        return redirect()->back()->with('success', 'تم اعتماد الطلبية بنجاح');
    }

    /**
     * رفض الطلبية
     */
    public function reject($id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => 'rejected']);
        return redirect()->back()->with('success', 'تم رفض الطلبية بنجاح');
    }
}
