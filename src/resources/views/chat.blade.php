@extends('layout.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/chat.css') }}">
@endsection

@section('content')
<div class="content">
    <div class="others">
        <span class="others-title">その他の取引</span>
        <div class="deal-section">
            <span class="deal-section-title">出品者としての取引</span>
            @foreach ($sellerProducts as $other)
                <a href="{{ route('transactions.chat', ['transaction' => $other->id]) }}">
                    <div class="deal-item-name">{{ $other->name }}</div>
                </a>
            @endforeach
        </div>
        <div class="deal-section">
            <span class="deal-section-title">購入者としての取引</span>
            @foreach ($buyerProducts as $other)
                <a href="{{ route('transactions.chat', ['transaction' => $other->id]) }}">
                    <div class="deal-item-name">{{ $other->name }}</div>
                </a>
            @endforeach
        </div>
    </div>
    <div class="main">
        <div class="chat-header">
            <div class="seller-info">
                @if ($isSeller && $chatPartnerProfile)
                    <img class="seller-avatar" src="{{ asset('storage/' . $chatPartnerProfile->image ) }}">
                    <span class="seller-name">「{{ $chatPartnerProfile->name }}」さんとの取引画面</span>
                @elseif (!$isSeller)
                    <img class="seller-avatar" src="{{ asset('storage/' .  $seller->image ) }}">
                    <span class="seller-name">「{{  $seller->name }}」さんとの取引画面</span>
                @endif
            </div>
        </div>
        <div class="product-summary">
            <div class="product-image">
                <img src="{{ asset('storage/' . $product->image) }}" alt="">
            </div>
            <div class="product-info">
                <span class="product-name">{{ $product->name }}</span>
                <span class="product-price">¥{{ number_format($product->price) }}</span>
            </div>
        </div>
        <div class="chat-box">
            @foreach ($chats as $chat)
                @php
                    $isOwnMessage = $chat->user_id === Auth::id();
                    $chatUserProfile = $chat->user->profile;
                @endphp
                @if ($isOwnMessage)
                    <div class="chat-wrapper right-wrapper">
                        {{-- 自分の情報 --}}
                        <div class="chat-user-info right-info">
                            <img class="chat-avatar" src="{{ asset('storage/' . $chatUserProfile->image) }}">
                            <span class="chat-username">{{ $chatUserProfile->name }}</span>
                        </div>
                        {{-- 自分のメッセージ --}}
                        <div class="chat-entry buyer-entry">
                            @if ($chat->image)
                                <img class="chat-image" src="{{ asset('storage/' . $chat->image) }}">
                            @endif
                            <div class="chat-message">
                                <div class="chat-bubble buyer-bubble">
                                    <span id="chat-text-{{ $chat->id }}" class="chat-text">
                                        {{ $chat->text }}
                                    </span>
                                    {{-- 編集フォーム(非表示) --}}
                                    <form id="chat-form-{{ $chat->id }}" action="{{ route('chats.update', ['chat' => $chat->id]) }}" method="post" style="display: none;">
                                        @csrf
                                        @method('PUT')
                                        <input class="edit-input" name="text" value="{{ $chat->text }}"
                                        onkeydown="if(event.key === 'Enter'){ this.form.submit(); }">
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="chat-controls">
                            {{-- 編集 --}}
                            <button onclick="toggleEdit( {{ $chat->id }})">編集</button>
                            {{-- 削除 --}}
                            <form action="{{ route('chats.destroy', ['chat' => $chat->id]) }}" method="post">
                                @csrf
                                @method('DELETE')
                                <button type="submit">削除</button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="chat-wrapper left-wrapper">
                        {{-- 相手の情報 --}}
                        <div class="chat-user-info left-info">
                            <span class="chat-username">{{ $chatUserProfile->name }}</span>
                            <img class="chat-avatar" src="{{ asset('storage/' . ($chatUserProfile->image)) }}">
                        </div>
                        {{-- 相手のメッセージ --}}
                        <div class="chat-entry seller-entry">
                            @if ($chat->image)
                                <img class="chat-image" src="{{ asset('storage/' . $chat->image) }}">
                            @endif
                            <div class="chat-message">
                                <div class="chat-bubble seller-bubble">{{ $chat->text }}</div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
        @if ($errors->any())
            <ul class="error-list">
                @foreach($errors->all() as $error)
                    <li class="error-message">{{ $error }}</li>
                @endforeach
            </ul>
        @endif
        <form class="chat-form" action="{{ route('transactions.chat.store', ['transaction' => $product->id]) }}" method="post"enctype="multipart/form-data" >
            @csrf
            <input class="chat-input" name="text" type="text" value="{{ old('text') }}" placeholder="取引メッセージを記入してください">
            <span id="filename" class="chat-filename">未選択</span>
            <input id="imageInput" class="chat-image-input" type="file" name="image" accept="image/*" style="display: none;">
            <button class="chat-image-button" type="button" onclick="document.getElementById('imageInput').click();">画像を追加</button>
            <button class="chat-submit-button" type="submit">
                <img src="{{ asset('images/send.jpg') }}">
            </button>
        </form>
    </div>
</div>
@endsection

<script>
    document.getElementById('imageInput').addEventListener('change', function () {
    const filename = this.files[0]?.name || '未選択';
    document.getElementById('filename').textContent = filename;
    });

    function toggleEdit(chatId) {
        const textElement = document.getElementById(`chat-text-${chatId}`)
        const formElement = document.getElementById(`chat-form-${chatId}`)

        //表示切り替え
        textElement.style.display = 'none';
        formElement.style.display = 'block';

        //自動フォーカス
        formElement.querySelector('input').focus();
    }
</script>