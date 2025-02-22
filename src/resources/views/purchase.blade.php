@extends('layout.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<form class="purchase-content" action="{{ route('product.order', ['id' => $product->id])}}" method="post">
    @csrf
    <div class="purchase-content__left">
        <div class="left-content">
            <div class="left-content__product">
                <div class="product-image">
                    <img class="product-image__img"  src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name}}">
                </div>
                <div class="product-main">
                    <span class="product-main__name">{{ $product->name }}</span>
                    <div class="product-main__price">
                        <span class="product__price--doll">¥</span>
                        <span class="product__price--text">{{ number_format($product->price) }}</span>
                        <span class="product__price--tax">(税込)</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="left-content">
            <h2 class="left-content__title">支払い方法</h2>
            <div class="pay-method">
                <select class="method-select" name="method" id="paymentSelect">
                    <option value="">選択してください</option>
                    <option value="コンビニ払い">コンビニ払い</option>
                    <option value="カード払い">カード払い</option>
                </select>
            </div>
            @if ($errors->has('method'))
            <div class="form__error">
                <ul>
                    @foreach ($errors->get('method') as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
        <div class="left-content">
            <div class="address-block">
                <h2 class="left-content__title">配送先</h2>
                <a class="address-block__edit" href="{{ route('address.edit', ['id' => $product->id]) }}">変更する</a>
            </div>
            <div class="user-address">
                @php
                $temp_address = session('temp_address', null);
                $display_address = $temp_address ?? $user->profile;
                @endphp
                <span class="user-address__post">〒{{ $display_address['post'] }}</span>
                <span class="user-address__building">{{ $display_address['address'] }}{{ $display_address['building'] }}</span>
            </div>
        </div>
    </div>
    <div class="purchase-content__right">
        <div class="right-box">
            <span class="right-box__label">商品代金</span>
            <div class="right-box__text">
                <span class="product__price--doll">¥</span>
                <span class="product__price--text">{{ number_format($product->price) }}</span>
                <span class="product__price--tax">(税込)</span>
            </div>
        </div>
        <div class="right-box">
            <span class="right-box__label">支払い方法</span>
            <div class="right-box__text"  id="paymentText">選択してください</div>
        </div>
        <div class="right-button">
            <button class="right-button__submit" type="submit">購入する</button>
        </div>
    </div>
</form>
@endsection

@section('script')
<script>
    document.getElementById('paymentSelect').addEventListener('change', function() {
        document.getElementById('paymentText').textContent = this.value || '選択してください';
    });
</script>
@endsection