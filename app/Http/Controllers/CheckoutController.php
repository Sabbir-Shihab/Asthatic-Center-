<?php

namespace App\Http\Controllers;

use App\Models\{Order, PaymentMethod, Product};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function show() { return redirect()->route('shop.index'); }

    public function store(Request $request)
    {
        $activeMethods = PaymentMethod::query()->where('is_active', true)->whereNotNull('account_number')->where('account_number', '!=', '')->get();
        $rules = ['customer_name'=>['required','string','max:160'],'email'=>['required','email','max:190'],'phone'=>['required','string','max:40'],'shipping_address'=>['required','string','max:1000'],'city'=>['required','string','max:120']];
        if ($activeMethods->isNotEmpty()) {
            $rules['payment_method_id'] = ['required', 'integer', 'exists:payment_methods,id'];
            $rules['transaction_id'] = ['required', 'string', 'min:4', 'max:40', 'regex:/^[A-Za-z0-9]+$/'];
        } else {
            $rules['payment_method'] = ['required', 'in:cod'];
        }
        $data = $request->validate($rules);
        $method = null;
        if ($activeMethods->isNotEmpty()) {
            $method = $activeMethods->firstWhere('id', (int) $data['payment_method_id']);
            if (! $method) {
                throw \Illuminate\Validation\ValidationException::withMessages(['payment_method_id' => t('Please choose an available payment method.')]);
            }
        }
        $items = $request->validate(['items'=>['required','array','min:1','max:50'],'items.*.id'=>['required','integer','exists:products,id'],'items.*.qty'=>['required','integer','min:1','max:20']])['items'];
        $order = DB::transaction(function () use ($data, $items, $method) {
            $subtotal = 0;
            $lines = [];
            foreach ($items as $item) {
                $product = Product::where('is_active', true)->lockForUpdate()->findOrFail($item['id']);
                if ($product->stock < $item['qty']) throw \Illuminate\Validation\ValidationException::withMessages(['items' => t('Insufficient stock for :name.', [':name' => $product->name])]);
                $lineTotal = $product->price * $item['qty']; $subtotal += $lineTotal;
                $lines[] = [$product, $item['qty'], $lineTotal];
            }
            $order = Order::create([
                'customer_name' => $data['customer_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'shipping_address' => $data['shipping_address'],
                'city' => $data['city'],
                'order_number' => 'SK-'.Str::upper(Str::random(8)),
                'subtotal' => $subtotal,
                'shipping' => 0,
                'total' => $subtotal,
                'status' => 'pending',
                'payment_method' => $method?->slug ?? 'cod',
                'payment_method_id' => $method?->id,
                'payment_method_name' => $method?->name,
                'payment_account' => $method?->account_number,
                'advance_amount' => $method?->advance_amount ?? 0,
                'transaction_id' => isset($data['transaction_id']) ? strtoupper($data['transaction_id']) : null,
                'payment_status' => $method ? 'submitted' : 'pending',
            ]);
            foreach ($lines as [$product,$quantity,$lineTotal]) {
                $order->items()->create(['product_id'=>$product->id,'product_name'=>$product->name,'quantity'=>$quantity,'unit_price'=>$product->price,'line_total'=>$lineTotal]);
                $product->decrement('stock', $quantity);
            }
            return $order;
        });
        $message = $method
            ? t('Order successful. A representative from our team will contact you very soon after checking your transaction ID.')
            : t('Order successful. A representative from our team will contact you very soon to confirm delivery.');
        return response()->json(['message'=>$message,'order_number'=>$order->order_number,'total'=>$order->total,'transaction_id'=>$order->transaction_id]);
    }
}
