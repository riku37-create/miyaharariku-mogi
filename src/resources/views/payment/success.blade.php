@extends('layout.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/payment.css') }}">
@endsection

@section('content')
<div class="payment">
    <h1 class="payment-ttl">決済成功！</h1>
    <p class="payment-txt">ご購入ありがとうございます。</p>
    <a class="payment-home" href="{{ route('product.index') }}">トップに戻る</a>
</div>
@endsection