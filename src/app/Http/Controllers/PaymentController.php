<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PurchaseRequest;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Product;
use App\Models\Order;


class PaymentController extends Controller
{
    public function checkout(PurchaseRequest $request, $id)
    {
        $product = Product::findOrFail($id);
        $user = Auth::user();

        // セッションに購入情報を保存（決済完了時に取得）
        session([
            'purchase_product_id' => $id,
            'purchase_method' => request('method'),
            'purchase_address' => session('temp_address', [
                'post' => $user->profile->post,
                'address' => $user->profile->address,
                'building' => $user->profile->building
            ])
        ]);

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => $product->name,
                    ],
                    'unit_amount' => $product->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('checkout.success'),
            'cancel_url' => route('checkout.cancel'),
        ]);

        return redirect($session->url);
    }

    public function success()
    {
        $user = Auth::user();
        $product_id = session('purchase_product_id');
        $method = session('purchase_method');
        $address = session('purchase_address');

        if (!$product_id || !$method || !$address) {
            return redirect()->route('product.index')->with('error', '決済情報が見つかりませんでした。');
        }

        // データベースに購入情報を保存
        $order = new Order();
        $order->user_id = $user->id;
        $order->product_id = $product_id;
        $order->method = $method;
        $order->post = $address['post'];
        $order->address = $address['address'];
        $order->building = $address['building'];
        $order->save();

        // セッションデータを削除
        session()->forget(['purchase_product_id', 'purchase_method', 'purchase_address']);
        return view('payment.success');
    }

    public function cancel()
    {
        return view('payment.cancel');
    }
}