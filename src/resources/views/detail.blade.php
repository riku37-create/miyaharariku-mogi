@extends('layout.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
<div class="product-detail">
    <div class="product-detail__left">
        <img class="left-image" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
    </div>
    <div class="product-detail__right">
        <div class="right-name">
            <h1 class="right-name__text">{{ $product->name }}</h1>
        </div>
        <div class="right-brand">
            <span class="right-brand__text">{{ $product->brand }}</span>
        </div>
        <div class="right-price">
            <span class="right-price__enn">¥</span>
            <span class="right-price__text">{{ number_format($product->price) }}</span>
            <span class="right-price__tax">(税込)</span>
        </div>
        <div class="right-like_comment">
            @if(Auth::check() && Auth::user()->likedProducts()->where('product_id', $product->id)->exists())
            <form class="right-like" action="{{ route('product.unlike', ['id' => $product->id])}}" method="post">
                @csrf
                <div class="like-form">
                    <button class="like-form__button" type="submit" style="border :none; background :none;">
                        <i class="icon fa-regular fa-star" style="color: #ff0000;"></i>
                    </button>
                    <span class="like-form__count">{{ $likeCount }}</span>
                </div>
            </form>
            @else
            <form class="right-like" action="{{ route('product.like', ['id' => $product->id]) }}" method="post">
                @csrf
                <div class="like-form">
                    <button class="like-form__button" type="submit" >
                        <i class="icon fa-regular fa-star" style="color: #000000;"></i>
                    </button>
                    <span class="like-form__count">{{ $likeCount }}</span>
                </div>
            </form>
            @endif
            <div class="right-comment">
                <div class="comment-form">
                    <button id="comment-link" class="comment-form__button">
                        <i class="icon fa-regular fa-comment" style="color: #000000;"></i>
                    </button>
                    <span class="comment-form__count">{{ $commentCount }}</span>
                </div>
            </div>
        </div>
        @if($product->order()->where('product_id', $product->id)->exists())
        <div class="right-purchase">
            <div class="right-purchase__none" style="color: #FF5555;">この商品は購入されています</div>
        </div>
        @else
        <form class="right-purchase" action="{{ route('product.purchase', ['id' => $product->id]) }}" method="post">
            @csrf
            <button class="right-purchase__button" type="submit">購入手続きへ</button>
        </form>
        @endif
        <h2 class="right-subtitle">商品説明</h2>
        <div class="right-description">
            <pre class="right-description__text">{{ $product->description }}</pre>
        </div>
        <h2 class="right-subtitle">商品の情報</h2>
        <div class="right-category">
            <div class="right-category__label">カテゴリー</div>
            <div class="right-category__text">
                <ul class="category-list">
                    @foreach($product->categories as $category)
                    <li class="category-list__item">
                        {{ $category->content }}
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="right-condition">
            <div class="right-condition__label">商品の状態</div>
            <div class="right-condition__text">{{ $product->condition->content}}</div>
        </div>
        <h2 class="right-subtitle">出品者</h2>
        <div class="right-user">
            <img class="avatar-image" src="{{ asset('storage/' . $product->user->profile->image) }}" alt="">
            <span class="avatar-name">{{ $product->user->profile->name }}</span>
        </div>
        <h2 id="comment" class="right-subtitle">コメント ({{ $commentCount }})</h2>
        @if(Auth::check())
        <form action="{{ route('store_comment', ['id' => $product->id]) }}" method="post">
            @csrf
            <label class="comment-area__label" for="content">商品へのコメント</label>
            <textarea class="comment-area__text" name="content"></textarea>
            @if ($errors->has('content'))
            <div class="form__error">
                <ul>
                    @foreach ($errors->get('content') as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <button class="comment-area__button" type="submit">コメントを送信する</button>
        </form>
        @endif
        @if($comments->isEmpty())
        <div class="comment-box-none">コメントはまだありません</div>
        @else
        <div class="comment-box">
            @foreach($comments as $comment)
            <div class="comment-box__avatar">
                <img class="avatar-image" src="{{ asset('storage/' . $comment->user->profile->image) }}" alt="">
                <span class="avatar-name">{{ $comment->user->profile->name }}</span>
            </div>
            <div class="comment-box__comment">
                <pre class="comment-content">{{ $comment->content }}</pre>
                <div class="comment-sub">
                    <span class="sub-time">{{ $comment->created_at->diffForHumans() }}</span>
                    @if($comment->user_id === Auth::id())
                    <form action="{{ route('comment.delete', ['commentId' => $comment->id, 'id' => $product->id]) }}" method="post">
                        @csrf
                        <button class="form-button">
                            <i class="fa-solid fa-trash" style="color: #ff0000;"></i>
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection

@section('script')
<script>
    const $commentLink = document.getElementById('comment-link');
    const $comment = document.getElementById('comment');

    // id=comment のブラウザの上からの位置を取得
    const commentTop = $comment.getBoundingClientRect().top;

    // id="comment-link"を持つ要素がクリックされた場合
    $commentLink.addEventListener('click',()=> {

    // スクロール
    window.scrollTo({
            top: commentTop ,
            left: 0,
            behavior: 'smooth'
    });
    })
</script>
@endsection