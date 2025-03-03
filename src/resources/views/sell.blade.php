@extends('layout.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('content')
<div class="product-sell">
    @if($product->id)
    <div class="product-sell-header">
        <h1 class="product-sell__title-edit">商品の編集</h1>
        <form class="header-form" action="{{ route('product.delete', ['id' => $product->id]) }}" method="post">
            @csrf
            <button class="header-form__button" type="submit">
                <i class="fa-solid fa-trash" style="color: #ff0000;"></i>
            </button>
        </form>
    </div>
    @else
    <h1 class="product-sell__title-create">商品の出品</h1>
    @endif
    <form class="product-sell__form" action="{{ $product->id ? route('product.save', ['id' => $product->id]) : route('product.save') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="sell__form--image">
            @if($product->id)
            <label class="form--image-label" for="image">商品画像 (画像は選び直してください)</label>
            @else
            <label class="form--image-label" for="image">商品画像</label>
            @endif
            <div class="form--image-content">
                <div class="content-image">
                    <img class="content-image__image" id="imagePreview" src="{{ $product->image ? asset('storage/' . $product->image) : '' }}">
                </div>
                <input id="imageInput" name="image" type="file" style="display:none">
                <button id="fileSelect" class="content-file" type="button">画像を選択する</button>
            </div>
        </div>
        @if ($errors->has('image'))
        <div class="form__error">
            <ul>
                @foreach ($errors->get('image') as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <div class="sell__form--detail">
            <div class="form-title">
                <h2 class="form--detail-title">商品の詳細</h2>
            </div>
            <h3 class="form--detail-category">ブランド</h3>
            <input class="description-form" type="text" name="brand" value="{{ old('brand', $product->brand)}}">
            <h3 class="form--detail-category">カテゴリー</h3>
            <div class="category-content">
                @foreach($categories as $category)
                <div class="category-content__block">
                    <input id="category_{{ $category->id }}" class="content__block-button" type="checkbox" name="categories[]" value="{{ $category->id }}"
                    {{ in_array($category->id, old('categories', $product->categories->pluck('id')->toArray())) ? 'checked' : '' }}>
                    <label class="content__block-name" for="category_{{ $category->id }}">
                        {{ $category->content }}
                    </label>
                </div>
                @endforeach
            </div>
            @if ($errors->has('categories'))
            <div class="form__error">
                <ul>
                    @foreach ($errors->get('categories') as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <h3 class="form--detail-condition">商品の状態</h3>
            <div class="condition-content">
                <select class="condition-content__select" name="condition_id">
                    <option value="">選択してください</option>
                    @foreach( $conditions as $condition)
                    <option value="{{ $condition->id }}"
                    {{ old('condition_id', $product->condition_id) == $condition->id ? 'selected' : '' }}>
                        {{ $condition->content }}
                    </option>
                    @endforeach
                </select>
            </div>
            @if ($errors->has('condition_id'))
            <div class="form__error">
                <ul>
                    @foreach ($errors->get('condition_id') as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
        <div class="sell__form--description">
            <div class="form-title">
                <h2 class="form--description-title">商品名と説明</h2>
            </div>
            <h3 class="form--description-subtitle">商品名</h3>
            <input class="description-form" type="text" name="name" value="{{ old('name', $product->name)}}">
            @if ($errors->has('name'))
            <div class="form__error">
                <ul>
                    @foreach ($errors->get('name') as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <h3 class="form--description-subtitle">商品の説明</h3>
            <textarea class="description-form__description" name="description">{{ old('description', $product->description)}}</textarea>
            @if ($errors->has('description'))
            <div class="form__error">
                <ul>
                    @foreach ($errors->get('description') as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <h3 class="form--description-subtitle">販売価格</h3>
            <input class="description-form" type="text" name="price" value="{{ old('price', $product->price)}}">
            @if ($errors->has('price'))
            <div class="form__error">
                <ul>
                    @foreach ($errors->get('price') as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
        @if($product->id)
        <button class="form-button" type="submit">編集する</button>
        @else
        <button class="form-button" type="submit">出品する</button>
        @endif
    </form>
</div>
@endsection

@section('script')
<script>
    const fileSelect = document.getElementById("fileSelect");
    const imageInput = document.getElementById("imageInput");

    // ボタンをクリックしたら file input を開く
    fileSelect.addEventListener("click", (e) => {
        e.preventDefault(); // ボタンのデフォルト動作を防ぐ
        imageInput.click();
    });

    // ファイルが選択されたらプレビューを更新
    imageInput.addEventListener("change", function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('imagePreview');
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection