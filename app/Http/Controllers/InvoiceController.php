<?php

namespace App\Http\Controllers;

use App\Models\Counter;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function getAllInvoices(){
        $invoice = Invoice::with('customer')->orderBy('id', 'DESC')->get();
        return response()->json([
            'invoices' => $invoice
        ], 200);
    }

    public function search(Request $request)
    {
        $search = $request->get('s');
        if($search != null){
            $invoices = Invoice::with('customer')->where('id', 'LIKE', "%$search%")->get();
            return response()->json([
                'invoices' => $invoices
            ]);
        }else{
            return $this->getAllInvoices();
        }
    }

    public function create(Request $request){
        $counter = Counter::where('key', 'invoice')->first();
        $random = Counter::where('key', 'invoice')->first();

        $invoice = Invoice::orderBy('id', 'DESC')->first();
        if ($invoice) {
            $invoice = $invoice->id + 1;
            $counters = $counter->value + $invoice;
        }else{
            $counters = $counter->value;
        }

        $formData = [
            'number' => $counter->prefix.$counters,
            'customer_id' => null,
            'customer' => null,
            'date' => date('Y-m-d'),
            'due_date' => null,
            'reference' => null,
            'discount' => 0,
            'terms_and_conditions' => 'T&C apply',
            'items' => [
                [
                    'product_id' => null,
                    'product' => null,
                    'unit_price' => 0,
                    'quantity' => 1
                ]
            ]
        ];
        return response()->json($formData);
    }

    public function addInvoice(Request $request){
        $invoiceItem = $request->invoice_item;
        $invoiceData['sub_total'] = $request->subtotal;
        $invoiceData['total'] = $request->total;
        $invoiceData['customer_id'] = $request->customer_id;
        $invoiceData['number'] = $request->number;
        $invoiceData['date'] = $request->date;
        $invoiceData['due_date'] = $request->due_date;
        $invoiceData['discount'] = $request->discount;
        $invoiceData['reference'] = $request->reference;
        $invoiceData['terms_and_conditions'] = $request->terms_and_conditions;

        $invoice = Invoice::create($invoiceData);

        foreach (json_decode($invoiceItem) as $item) {
            $itemData['product_id'] = $item->id;
            $itemData['invoice_id'] = $invoice->id;
            $itemData['quantity'] = $item->quantity;
            $itemData['unit_price'] = $item->unit_price;

            InvoiceItem::create($itemData);
        }
    }

    public function showInvoice($id){
        $invoice = Invoice::with(['customer', 'invoice_items.product'])->find($id);
        return response()->json([
            'invoice' => $invoice
        ], 200);
    }

    public function deleteItem($id){
        $invoiceItem = InvoiceItem::findOrFail($id);
        $invoiceItem->delete();
    }

    public function updateInvoice(Request $request, $id){
        $invoice = Invoice::where('id', $id)->first();

        $invoice->sub_total = $request->subtotal;
        $invoice->total = $request->total;
        $invoice->customer_id = $request->customer_id;
        $invoice->number = $request->number;
        $invoice->date = $request->date;
        $invoice->due_date = $request->due_date;
        $invoice->discount = $request->discount;
        $invoice->reference = $request->reference;
        $invoice->terms_and_conditions = $request->terms_and_conditions;

        $invoice->update($request->all());
        $invoiceItem = $request->invoice_item;
        $invoice->invoice_items()->delete();

        foreach (json_decode($invoiceItem) as $item) {
            $itemData['product_id'] = $item->product_id;
            $itemData['invoice_id'] = $invoice->id;
            $itemData['quantity'] = $item->quantity;
            $itemData['unit_price'] = $item->unit_price;

            InvoiceItem::create($itemData);
        }
    }
}
