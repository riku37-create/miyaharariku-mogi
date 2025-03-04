@extends('layout.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/payment.css') }}">
@endsection

@section('content')
<div class="payment">
    <h1>決済キャンセル</h1>
    <p>決済がキャンセルされました。</p>
    <a href="{{ route('product.index') }}">トップに戻る</a>
</div>
@endsection