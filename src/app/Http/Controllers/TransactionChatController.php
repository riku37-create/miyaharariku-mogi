<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests\ChatRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Chat;
use App\Models\Rating;
use App\Mail\RatingSubmitted;
use Illuminate\Support\Facades\Mail;

class TransactionChatController extends Controller
{
    public function show(Request $request, $transactionId)
    {
        $product = Product::findOrFail($transactionId);
        $seller = $product->user->profile;
        $user = Auth::user();
        $userId = $user->id;

        // 出品者とログインユーザーが一致していれば出品者
        $isSeller = $product->user_id === $userId;

        // 自分が購入した商品(購入者) チャットの商品以外
        $buyerProductIds = Order::where('user_id', $userId)->pluck('product_id')->toArray();

        // 自分が出品し、購入された商品(出品者) チャットの商品以外
        $sellerProductIds = Product::where('user_id', $userId)
            ->whereIn('id', Order::pluck('product_id'))
            ->pluck('id')->toArray();

        // 両方をまとめて取得
        $relatedProductIds = collect($buyerProductIds)
            ->merge($sellerProductIds)
            ->unique()
            ->reject(fn($id) => $id == $transactionId)
            ->values();

        $ratings = Rating::whereIn('product_id', $relatedProductIds)->get();

        $incompleteRatedProductIds = [];

        foreach ($relatedProductIds as $pid) {
            $order = Order::where('product_id', $pid)->first();
            // 購入者
            $sellerId = Product::find($pid)->user_id;
            // 出品者
            $partnerId = $userId === $sellerId ? $order->user_id : $sellerId;

            $userRated = $ratings->contains(fn($r) =>
            $r->rater_id == $userId && $r->ratee_id == $partnerId && $r->product_id == $pid
            );
            $partnerRated = $ratings->contains(fn($r) =>
                $r->rater_id == $partnerId && $r->ratee_id == $userId && $r->product_id == $pid
            );

            // どちらか評価が未実施なら「取引中」
            if (!($userRated && $partnerRated)) {
                $incompleteRatedProductIds[] = $pid;
            }
        }

        $otherProducts = Product::whereIn('id', $incompleteRatedProductIds)
            ->select('id', 'name')
            ->get();

        $chats = Chat::where('product_id', $transactionId)
            ->with('user.profile')
            ->orderby('created_at', 'asc')
            ->get();

        // 相手の最初のチャットを取得
        $firstChatFromOther = Chat::where('product_id', $transactionId)
            ->where('user_id', '!=', Auth::id())
            ->with('user.profile')
            ->orderBy('created_at', 'asc')
            ->first();

        $chatPartnerProfile = null;
        if ($firstChatFromOther && $firstChatFromOther->user) {
            $chatPartnerProfile = $firstChatFromOther->user->profile;
        }

        // 最終閲覧を更新
        DB::table('chat_reads')->updateOrInsert(
            ['user_id' => $user->id, 'product_id' => $transactionId],
            ['last_read_at' => now()]
        );

        $isRatedByPartner = false;
        $hasUserRatedPartner =false;
        $shouldShowModal = false;
        if ($chatPartnerProfile) {
            $partnerId = $chatPartnerProfile->user->id;

            $currentRatings = Rating::where('product_id', $product->id)
            ->whereIn('rater_id', [$userId, $partnerId])
            ->whereIn('ratee_id', [$userId, $partnerId])
            ->get()
            ->groupBy('rater_id');

            // 自分がパートナーを評価したか
            $hasUserRatedPartner = isset($currentRatings[$userId]);

            // パートナーが自分を評価したか
            $isRatedByPartner = isset($currentRatings[$partnerId]);

            // モーダル表示判定（相手から評価されていて、自分はまだ）
            $shouldShowModal = $isRatedByPartner && !$hasUserRatedPartner;
        }
        return view('/chat', compact('product', 'otherProducts', 'seller', 'chats', 'isSeller', 'chatPartnerProfile', 'isRatedByPartner', 'hasUserRatedPartner', 'shouldShowModal' ));
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

        return back();
    }

    public function destroy(Chat $chat)
    {
        $chat->delete();

        return back();
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
