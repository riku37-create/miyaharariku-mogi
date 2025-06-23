<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests\ChatRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Product;
use App\Models\Chat;
use App\Models\Rating;
use App\Mail\RatingSubmitted;
use Illuminate\Support\Facades\Mail;

class TransactionChatController extends Controller
{
    public function show(Request $request, $transactionId)
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

        DB::table('chat_reads')->updateOrInsert(
            ['user_id' => $user->id, 'product_id' => $product->id],
            ['last_read_at' => now()]
        );

        $isRatedByPartner = false;
        $hasUserRatedPartner =false;
        $shouldShowModal = false;
        if ($chatPartnerProfile && $chatPartnerProfile->user) {
            $partnerUserId = $chatPartnerProfile->user->id;
            // 相手が自分を評価済みか?
            $isRatedByPartner = Rating::where('rater_id', $partnerUserId)
                ->where('ratee_id', $user->id)
                ->where('product_id', $product->id)
                ->exists();

            // 自分がパートナーを未評価ならモーダル表示
            $hasUserRatedPartner = Rating::where('rater_id', $user->id)
                ->where('ratee_id', $partnerUserId)
                ->where('product_id', $product->id) // ← 追加
                ->exists();
            
            $shouldShowModal = $isRatedByPartner && !$hasUserRatedPartner;
        }

        return view('/chat', compact('product', 'seller', 'chats', 'isSeller', 'chatPartnerProfile', 'sellerProducts', 'buyerProducts', 'isRatedByPartner', 'hasUserRatedPartner', 'shouldShowModal' ));
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

    public function rate(Request $request, User $user)
    {
        Rating::updateOrCreate(
        [
            'rater_id' => Auth::id(),
            'ratee_id' => $user->id,
            'product_id' => $request->input('product_id')
        ],
        ['rating' => $request->input('rating')]
        );

        $rater = Auth::user();
        $ratee = $user;
        $rating = (int) $request->input('rating');

        Mail::to($ratee->email)->send(new RatingSubmitted($rater, $ratee, $rating));

        return redirect()->route('product.index');
    }
}
