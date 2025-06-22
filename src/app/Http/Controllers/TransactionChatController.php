<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ChatRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Product;
use App\Models\Chat;

class TransactionChatController extends Controller
{
    public function show($transactionId)
    {
        $product = Product::find($transactionId);
        $seller = $product->user->profile;
        $user = User::find(Auth::id());

        // 出品者とログインユーザーが一致していれば出品者
        $isSeller = $product->user_id === $user->id;

        // 出品者として関わっている商品ID（他人がチャットしてきた）
        $sellerProductIds = Product::where('user_id', $user->id)->pluck('id');
        $sellerChatProductIds = Chat::whereIn('product_id', $sellerProductIds)
            ->where('user_id', '!=', $user->id) // 自分以外がチャットした
            ->select('product_id')
            ->distinct()
            ->pluck('product_id');
        $sellerProducts = Product::whereIn('id', $sellerChatProductIds)
            ->where('id', '!=', $transactionId)
            ->select('id', 'name')
            ->get();

        // 購入者として関わっている商品ID（自分がチャットした）
        $buyerChatProductIds = Chat::where('user_id', $user->id)
            ->select('product_id')
            ->distinct()
            ->pluck('product_id');

        // 自分が購入者、かつ出品者ではない商品（＝他人の出品物）
        $buyerProducts = Product::whereIn('id', $buyerChatProductIds)
            ->where('user_id', '!=', $user->id)
            ->where('id', '!=', $transactionId)
            ->select('id', 'name')
            ->get();

        $chats = Chat::where('product_id', $product->id)
            ->with('user.profile')
            ->orderby('created_at', 'asc')
            ->get();

        // 相手の最初のチャットを取得
        $firstChatFromOther = Chat::where('product_id', $product->id)
            ->where('user_id', '!=', Auth::id())
            ->with('user.profile')
            ->orderBy('created_at', 'asc')
            ->first();
        $chatPartnerProfile = null;
        if ($firstChatFromOther && $firstChatFromOther->user) {
            $chatPartnerProfile = $firstChatFromOther->user->profile;
        }

        return view('/chat', compact('product', 'seller', 'chats', 'isSeller', 'chatPartnerProfile', 'sellerProducts', 'buyerProducts'));
    }

    public function store(ChatRequest $request, $productId)
    {
        $user = User::find(Auth::id());

        $imagePath = null;
        if ($request->hasFile('image'))
            $imagePath = $request->file('image')->store('chat-image', 'public');
        Chat::create([
            'user_id' => $user->id,
            'product_id' => $productId,
            'text' => $request->text,
            'image' => $imagePath,
        ]);

        return redirect()->route('transactions.chat',['transaction' => $productId]);
    }

    public function update(ChatRequest $request, Chat $chat)
    {
        $chat->update([
            'text' => $request->input('text'),
        ]);

        return back()->with('success', 'チャットを編集しました');
    }

    public function destroy(Chat $chat)
    {
        $chat->delete();

        return back()->with('success', 'チャットを削除しました');
    }
}
