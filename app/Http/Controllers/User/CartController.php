<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\User;
use App\Models\Stock;
use Illuminate\Support\Facades\Auth;
use App\Services\CartService;
use App\Jobs\SendThanksMail;
use App\Jobs\SendOrderdMail;

class CartController extends Controller
{
    public function index()
    {
        // ログインしてるユーザidでデータを取得
        $user = User::findOrFail(Auth::id());
        $products = $user->products;
        $totalPrice = 0;

        // 小計を計算
        foreach ($products as $product) {
            $totalPrice += $product->price * $product->pivot->quantity;
        }

        // dd($products, $totalPrice);

        return view(
            'user.cart',
            compact('products', 'totalPrice')
        );
    }

    // カートに入れるを実行した時の処理
    public function add(Request $request)
    {
        // 追加しようとしてる商品と一致するもの
        $itemInCart = Cart::where('product_id', $request->product_id)
            // ログインユーザと一致するもの
            ->where('user_id', Auth::id())->first();

        // 既にカートに存在すれば量だけ変更
        if ($itemInCart) {
            $itemInCart->quantity += $request->quantity;
            $itemInCart->save();
        } else {
            // まだ存在しない場合、createでインスタンス作成
            Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
            ]);
        }
        return redirect()->route('user.cart.index');
    }

    public function delete($id)
    {
        Cart::where('product_id', $id)
            ->where('user_id', Auth::id())
            ->delete();

        return redirect()->route('user.cart.index');
    }

    public function checkout()
    {

        $user = User::findOrFail(Auth::id());
        $products = $user->products;

        $lineItems = [];
        foreach ($products as $product) {
            //在庫を確認
            $quantity = "";
            $quantity = Stock::where('product_id', $product->id)->sum('quantity');

            if ($product->pivot->quantity > $quantity) {
                return redirect()->route('user.cart.index');
            } else {
                // $lineItem = [
                //     'name' => $product->name,
                //     'description' => $product->information,
                //     'amount' => $product->price,
                //     'currency' => 'jpy',
                //     'quantity' => $product->pivot->quantity,
                // ];
                // array_push($lineItems, $lineItem);

                $lineItem =
                    [
                        "price_data" => [
                            "unit_amount" => $product->price,
                            "currency" => 'jpy',
                            "product_data" => [
                                "name" => $product->name,
                                "description" => $product->information,
                            ],
                        ],
                        "quantity" => $product->pivot->quantity,
                    ];

                array_push($lineItems, $lineItem);
            }
        }
        // dd($lineItems);

        foreach ($products as $product) {
            Stock::create([
                'product_id' => $product->id,
                'type' => \Constant::PRODUCT_LIST['reduce'],
                'quantity' => $product->pivot->quantity * -1,
            ]);
        }

        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [$lineItems],
            'mode' => 'payment',
            'success_url' => route('user.cart.success'),
            'cancel_url' => route('user.cart.cancel'),
        ]);

        // $publicKey = env('STRIPE_PUBLIC_KEY');
        // return view(
        //     'user.checkout',
        //     compact('session', 'publicKey')
        // );

        // dd($lineItems);
        return redirect($session->url, 303);
    }

    public function success()
    {
        ////
        $items = Cart::where('user_id', Auth::id())->get();
        $products = CartService::getItemsInCart($items);
        $user = User::findOrFail(Auth::id());
        SendThanksMail::dispatch($products, $user);
        foreach ($products as $product) {
            SendOrderdMail::dispatch($product, $user);
        }
        // dd('ユーザーメール送信テスト');
        ////

        Cart::where('user_id', Auth::id())->delete();

        return redirect(route('user.items.index'));
    }

    public function cancel()
    {
        $user = User::findOrFail(Auth::id());
        foreach ($user->products as $product) {
            Stock::create([
                'product_id' => $product->id,
                'type' => \Constant::PRODUCT_LIST['reduce'],
                'quantity' => $product->pivot->quantity,
            ]);
        }

        return redirect(route('user.cart.index'));
    }
}
