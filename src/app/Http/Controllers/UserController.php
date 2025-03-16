<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\AddressRequest;
use Illuminate\Support\Facades\Storage;
use App\Models\Profile;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

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
        } else {
            $orders = $user->orders()->select('product_id')->get();
            $products = Product::whereIn('id', $orders)
            ->select('id','name','image')->get();
        }
        $profile = Profile::where('user_id', $user->id)->first();
        if (empty($profile)) {
            return redirect()->route('profile.edit');
        }
        else {
            return view('profile', compact('page', 'products', 'profile'));
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
