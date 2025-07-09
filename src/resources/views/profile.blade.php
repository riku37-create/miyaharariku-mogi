@extends('layout.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('search')
<form class="header-inner__search" action="{{ route('profile.search') }}" method="get">
    @csrf
    <input type="hidden" name="page" value="{{ request('page', 'sell') }}">
    <input class="search-input" name="input" type="text" value="{{ old('input', request('input')) }}" placeholder="何をお探しですか?">
    <button class="search-button" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
</form>
@endsection

@section('content')
<div class="profile-data">
    <div class="data-image">
        <img class="data-image__image" src="{{ asset('storage/' . $profile->image) }}" alt="{{ $profile->name }}">
    </div>
    <div class="data-user">
        <h2 class="data-name">{{ $profile->name }}</h2>
        @php
            $averageRating = round($profile->user->averageRating());
        @endphp
        @if ($averageRating > 0)
            <div class="rating-stars">
                @for ($i = 1; $i <= 5; $i++)
                    <span class="star {{ $i <= $averageRating ? 'filled' : '' }}">★</span>
                @endfor
            </div>
        @endif
    </div>
    <form class="data-form" action="{{ route('profile.edit') }}">
        <button class="data-form__button" type="submit">プロフィールを編集</button>
    </form>
</div>
<div class="product-header">
    <nav class="product-header__nav">
        <ul class="product-header__list">
            <li class="product-header__item">
                <a class="product-header__a {{ $page === 'sell' ? 'active' : '' }}"
                href="{{ route('profile.index', ['page' => 'sell'] ) }}">出品した商品</a>
            </li>
            <li class="product-header__item">
                <a class="product-header__a {{ $page === 'buy' ? 'active' : '' }}"
                href="{{ route('profile.index', ['page' => 'buy'])}}">購入した商品</a>
            </li>
            <li class="product-header__item">
                <a class="product-header__a {{ $page === 'deal' ? 'active' : '' }}"
                href="{{ route('profile.index', ['page' => 'deal'] ) }}">取引中の商品</a>
            </li>
        </ul>
    </nav>
</div>
<div class="product-main">
    @if (request('page', 'sell') === 'sell')
        @if($products->isEmpty()){{-- 商品がない時 --}}
            <h1 class="product__item--none">商品はありません</h1>
        @endif
        @foreach ($products as $product)
            <div class="product-item">
                <a class="product-it__a" href="{{ route('product.sell', ['id' => $product->id]) }}">
                <div class="product-item__image-box">
                    <img class="image-box__image" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                    @if($product->order()->where('product_id', $product->id)->exists())
                    <span class="image-box__sold">SOLD</span>
                    @endif
                </div>
                </a>
                <div class="product-item__name">
                    <span class="item__name-text">{{ $product->name }}</span>
                    <form class="item__name-form" action="{{ route('product.delete', ['id' => $product->id]) }}" method="post">
                        @csrf
                        <button class="form-button" type="submit">
                            <i class="fa-solid fa-trash" style="color: #ff0000;"></i>
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    @elseif (request('page') === 'buy')
        @if($products->isEmpty()){{-- 商品がない時 --}}
            <h1 class="product__item--none">商品はありません</h1>
        @endif
        @foreach ($products as $product)
            <div class="product__item">
                <div class="product-item__image-box">
                    <img class="image-box__image" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                </div>
                <span class="product-item__name">{{ $product->name }}</span>
            </div>
        @endforeach
    @elseif (request('page') === 'deal')
        @foreach ($products as $product)
            <div class="product-item">
                <a class="product-item__a" href="{{ route('transactions.chat', ['transaction' => $product->id]) }}">
                    <div class="product-item__image-box">
                        <img class="image-box__image" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                        {{-- 未読通知 --}}
                        @if(!empty($notifications[$product->id]))
                            <span class="notification-badge">{{ $notifications[$product->id] }}</span>
                        @endif
                    </div>
                </a>
                <span class="product-item__name">{{ $product->name }}</span>
            </div>
        @endforeach
    @endif
</div>
@endsection