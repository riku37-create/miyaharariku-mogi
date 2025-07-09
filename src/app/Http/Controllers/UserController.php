<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\AddressRequest;
use Illuminate\Support\Facades\Storage;
use App\Models\Profile;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Models\Chat;

class UserController extends Controller
{
    //プロフィール画面表示
    public function index(Request $request)
    {
        $page = $request->get('page', 'sell');
        $user = User::find(Auth::id());

        if ($page === 'sell') {
            $products = $user->products()
            ->select('id','name','image')->latest('products.created_at')->get();
        } elseif($page === 'buy') {
            $orders = $user->orders()->select('product_id')->get();
            $products = Product::whereIn('id', $orders)
            ->select('id','name','image')->get();
        } elseif($page === 'deal') {
            // 自分が購入した商品(購入者)
            $buyerProductsIds = Order::where('user_id', $user->id)
                ->pluck('product_id');

            // 自分が出品し、購入された商品(出品者)
            $sellerProductIds = Product::where('user_id', $user->id)
                ->whereIn('id', Order::pluck('product_id'))
                ->pluck('id');

            // 両方をまとめて取得
            $allProductIds = $buyerProductsIds->merge($sellerProductIds)->unique();

            // 評価が未完了の商品を抽出
            $incompleteRatedProductIds = [];

            foreach ($allProductIds as $productId) {
                // 購入者
                $partnerId = Order::where('product_id', $productId)->value('user_id');
                // 出品者
                $sellerId = Product::find($productId)->user_id;

                $userRated = DB::table('ratings')->where('rater_id', $user->id)
                    ->where('ratee_id', $user->id === $sellerId ? $partnerId : $sellerId)
                    ->where('product_id', $productId)
                    ->exists();

                $partnerRated = DB::table('ratings')->where('rater_id', $user->id === $sellerId ? $partnerId : $sellerId)
                    ->where('ratee_id', $user->id)
                    ->where('product_id', $productId)
                    ->exists();

                // どちらか評価が未実施なら「取引中」
                if (!($userRated && $partnerRated)) {
                    $incompleteRatedProductIds[] = $productId;
                }
            }

            $products = Product::whereIn('id', $incompleteRatedProductIds)
                ->select('id', 'name', 'image')
                ->get();

            $notifications = [];

            foreach ($products as $product) {
                $lastRead = DB::table('chat_reads')
                    ->where('user_id', $user->id)
                    ->where('product_id', $product->id)
                    ->value('last_read_at');

                $unreadCount = Chat::where('product_id', $product->id)
                    ->where('user_id', '!=', $user->id) // 自分以外
                    ->when($lastRead, function ($query) use ($lastRead) {
                        $query->where('created_at', '>', $lastRead);
                    })
                    ->count();

                $notifications[$product->id] = $unreadCount;
            }
        }
        $profile = Profile::where('user_id', $user->id)->first();
        if (empty($profile)) {
            return redirect()->route('profile.edit');
        }
        else {
            if ($page === 'deal') {
                return view('profile', compact('page', 'products', 'profile', 'notifications'));
            } else {
                return view('profile', compact('page', 'products', 'profile'));
            }
        }
    }

    //商品検索機能
    public function search(Request $request)
    {
        $user = User::find(Auth::id());
        $page = $request->get('page', 'sell'); // デフォルトは 'sell'
        $query = Product::query(); // クエリビルダーを作成
        if ($request->filled('input')) {
            $query->where('name', 'LIKE', "%{$request->input}%");
        }
        if ($page === 'sell') {
            $soldProductIds = $user->products()->pluck('products.id');
            $query->whereIn('id', $soldProductIds);
        } elseif($page === 'buy') {
            $boughtProductIds = $user->orders()->select(('product_id'));
            $query->whereIn('id', $boughtProductIds);
        }
        $products = $query->get();
        $profile = Profile::where('user_id', $user->id)->first();
        return view('profile', compact('page', 'products', 'profile'));
    }

    // プロフィール編集画面表示
    public function edit()
    {
        $user = Auth::user();
        $profile = $user->profile ?? new Profile();
        return view('edit', compact('profile'));
    }

    // プロフィール更新機能
    public function update(ProfileRequest $request, $profileId = null)
    {
        $user = Auth::user();
        $data = $request->all();

        // 画像をストレージから削除
        $deleteImgProfile = Profile::find($profileId);
        if($deleteImgProfile) {
            Storage::disk('public')->delete($deleteImgProfile->image);
        }

        //画像をパスで保存
        if($request->hasFile('image')){
            $path = $request->file('image')->store('profile-img', 'public');
            $data['image'] = $path;
        }

        Profile::updateOrCreate(
            ['user_id' => $user->id], //条件
            $data //更新または挿入するデータ
        );

        return redirect()->route('product.index');
    }

    // 住所変更画面表示
    public function address_edit($id)
    {
        $user = Auth::user();
        $past_addresses = Order::where('user_id', $user->id)
        ->select('post', 'address', 'building')
        ->distinct()
        ->get();
        return view('address', compact('id', 'past_addresses'));
    }

    // 住所変更機能
    public function address_update(AddressRequest $request, $id)
    {
        $address = $request->only(['post', 'address', 'building']);
        session([
            'temp_address' => $address
        ]);
        return redirect()->route('product.purchase', ['id' => $id]);
    }
}
