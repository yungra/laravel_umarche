<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Stock;
use App\Models\PrimaryCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;
use App\Jobs\SendThanksMail;

class ItemController extends Controller
{

    public function __construct()
    {
        // ログインしてるユーザか確認
        $this->middleware('auth:users');

        $this->middleware(function ($request, $next) {

            // 'item'パラメータを取得
            $id = $request->route()->parameter('item');
            // 販売してない商品を表示しないよう設定
            if (!is_null($id)) { // null判定
                $itemId = Product::availableItems()->where('products.id', $id)->exists();
                if (!$itemId) { //存在しなかったら
                    abort(404); // 404画面表示
                }
            }

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        // dd($request);

        //同期的に送信
        // Mail::to('test@example.com')
        // ->send(new TestMail());

        //非同期に送信
        // SendThanksMail::dispatch();

        $categories = PrimaryCategory::with('secondary')
            ->get();

        //検索設定により表示を変更
        $products = Product::availableItems()
        ->selectCategory($request->category ?? '0')
        ->searchKeyword($request->keyword)
        ->sortOrder($request->sort)
        ->paginate($request->pagination ?? '20');

        return view('user.index', compact('products', 'categories'));
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        // 在庫の残り数を計算
        $quantity = Stock::where('product_id', $product->id)
            ->sum('quantity');

        // 在庫数が9個以上なら表示は9
        if ($quantity > 9) {
            $quantity = 9;
        }

        return view('user.show', compact('product', 'quantity'));
    }
}
