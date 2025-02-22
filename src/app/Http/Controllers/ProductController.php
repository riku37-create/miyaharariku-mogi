<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\CommentRequest;
use App\Models\Product;
use App\Models\Comment;
use App\Models\Condition;
use App\Models\Category;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->get('page', 'recommend');
        $user = User::find(Auth::id());
        if ($page === 'mylist') {
            if($user) {
                $products = $user->likedProducts()
                ->select('products.id', 'products.name', 'products.image')->latest('likes.created_at')->get();
            } else {
                return view('auth.login');
            }
        } else {
            $products = Product::select('id', 'name', 'image')->latest('products.created_at')->get();
        }
        return view('products', compact('page','products'));
    }

    //商品検索機能
    public function search(Request $request)
    {
        $user = User::find(Auth::id());
        $page = $request->get('page', 'recommend');
        $query = Product::query();
        if ($request->filled('input')) {
            $query->where('name', 'LIKE', "%{$request->input}%");
        }
        if($page === 'mylist' || $page === 'recommend') {
            if ($page === 'mylist') {
                $likedProductIds = $user->likedProducts()->pluck('products.id');
                $query->whereIn('id', $likedProductIds);
            }
        }
        $products = $query->get();
        return view('products', compact('page', 'products'));
    }

    // 商品詳細画面表示
    public function detail($id)
    {
        $product = Product::with(['categories', 'condition', 'likedUsers', 'comments'])->find($id);
        $comments = $product->comments()->with('user.profile')->latest('comments.created_at')->get();
        $likeCount = $product->likedUsers()->count(); // いいね数
        $commentCount = $product->comments()->count(); // コメント数
        return view('detail', compact('product', 'likeCount', 'commentCount', 'comments'));
    }

    // いいね
    public function like($id)
    {
        $user = User::find(Auth::id());
        $user->likedProducts()->attach($id);
        return redirect()->back();
    }

    // いいね取り消し
    public function unlike($id)
    {
        $user = User::find(Auth::id());
        $user->likedProducts()->detach($id);
        return redirect()->back();
    }

    // コメント追加
    public function storeComment(CommentRequest $request, $id)
    {
        Comment::create([
            'user_id' => Auth::id(),
            'product_id' => $id,
            'content' => $request->content
        ]);
        return redirect()->route('product.detail',['id' => $id]);
    }

    //コメント削除
    public function commentDelete($commentId)
    {
        $product = Comment::find($commentId)->product_id;
        Comment::find($commentId)->delete();
        return redirect()->route('product.detail', ['id' => $product]);
    }

    // 商品出品画面表示
    public function sell($id = null)
    {
        $product = $id ? Product::findOrFail($id) : new Product();
        $categories = Category::all();
        $conditions = Condition::all();
        return view('sell',compact('product', 'categories', 'conditions'));
    }

    // 商品出品機能
    public function save(ProductRequest $request, $id = null )
    {
        $user = Auth::user();
        $data = $request->all();

        // 画像をストレージから削除
        $deleteImgProduct = Product::find($id);
        if($deleteImgProduct) {
            Storage::disk('public')->delete($deleteImgProduct->image);
        }

        //画像をパスで保存
        $data['user_id'] = $user->id;
        if($request->hasFile('image')){
            $path = $request->file('image')->store('product-img', 'public');
            $data['image'] = $path;
        }
        $product = Product::updateOrCreate(
            ['id' => $id],
            $data
        );

        if ($request->has('categories')) {
        $product->categories()->sync($request->categories);
        }

        return redirect()->route('product.index');
    }

    // 商品購入画面表示
    public function purchase($id)
    {
        $user = Auth::user();
        $product = Product::find($id);
        return view('purchase', compact('product','user'));
    }

    //商品購入機能
    public function order(PurchaseRequest $request, $id)
    {
        $user = Auth::user();
        $address = session('temp_address', [
            'post' => $user->profile->post,
            'address' => $user->profile->address,
            'building' => $user->profile->building
        ]);
        $order = new Order();
        $order->user_id = $user->id;
        $order->product_id = $id;
        $order->method = $request->method;
        $order->post = $address['post'];
        $order->address = $address['address'];
        $order->building = $address['building'];
        $order->save();
        return redirect()->route('product.index');
    }

    //商品削除機能
    public function deleteProduct($id)
    {
        $deleteProduct = Product::find($id);
        if($deleteProduct) {
            Storage::disk('public')->delete($deleteProduct->image);
            $deleteProduct->delete();
        }
        return redirect()->route('profile.index');
    }
}
