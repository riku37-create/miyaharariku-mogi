@extends('layout.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/products.css') }}">
@endsection

@section('search')
<form class="header-inner__search" action="{{ route('product.search') }}" method="post">
    @csrf
    <input type="hidden" name="page" value="{{ request('page', 'recommend') }}">
    <input class="search-input" name="input" type="text" value="{{ old('input', request('input')) }}" placeholder="何をお探しですか?">
    <button class="search-button" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
</form>
@endsection

@section('content')
<div class="product-header">
    <nav class="product-header__nav">
        <ul class="product-header__list">
            <li class="product-header__item">
                <a class="product-header__a {{ $page === 'recommend' ? 'active' : '' }}"
                href="{{ route('product.index', ['page' => 'recommend'] ) }}">おすすめ</a>
            </li>
            <li class="product-header__item">
                <a class="product-header__a {{ $page === 'mylist' ? 'active' : '' }}"
                href="{{ route('product.index', ['page' => 'mylist'])}}">マイリスト</a>
            </li>
        </ul>
    </nav>
</div>
<div class="product-main">
    @if($products->isEmpty())
    <h1 class="product-item__none">商品はありません</h1>
    @else
    @if (request('page', 'recommend') === 'recommend')
    @foreach ($products as $product)
    <div class="product-item">
        <a class="product-item__a" href="{{ route('product.detail', ['id' => $product->id]) }}">
            <div class="product-item__image-box">
                <img class="image-box__image" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                @if($product->order()->where('product_id', $product->id)->exists())
                <span class="image-box__sold">SOLD</span>
                @endif
            </div>
            <span class="product-item__name">{{ $product->name }}</span>
        </a>
    </div>
    @endforeach
    @elseif (request('page') === 'mylist')
    @foreach ($products as $product)
        <div class="product__item">
            <a class="product-item__a" href="{{ route('product.detail', ['id' => $product->id]) }}">
                <div class="product-item__image-box">
                    <img class="image-box__image" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                    @if($product->order()->where('product_id', $product->id)->exists())
                    <span class="image-box__sold">SOLD</span>
                    @endif
                </div>
                <span class="product-item__name">{{ $product->name }}</span>
            </a>
        </div>
    @endforeach
    @endif
    @endif
</div>
@endsection