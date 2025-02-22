@extends('layout.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/edit.css') }}">
@endsection

@section('content')
<div class="user-edit">
    <h1 class="edit-title">プロフィール設定</h1>
    <form class="edit-form" action="{{ $profile->id ? route('profile.update', ['profileId' => $profile->id]) : route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        <div class="form-image">
            <img id="imagePreview" class="form-image__image"
                src="{{ $profile->image ? asset( 'storage/' . $profile->image) : '' }}"
                alt="{{ $profile->name ?? '' }}">
            <input id="imageInput" name="image" type="file" style="visibility: hidden;">
            <button id="fileSelect" class="form-image__button">画像を選択する</button>
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
        <div class="form-group">
            <label class="form-group__label" for="name">ユーザー名</label>
            <input class="form-group__input" name="name" type="text" value="{{ old('name', $profile->name ?? '') }}">
        </div>
        @if ($errors->has('name'))
        <div class="form__error">
            <ul>
                @foreach ($errors->get('name') as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <div class="form-group">
            <label class="form-group__label" for="post">郵便番号</label>
            <input class="form-group__input" name="post" type="text" value="{{ old('post', $profile->post ?? '') }}">
        </div>
        @if ($errors->has('post'))
        <div class="form__error">
            <ul>
                @foreach ($errors->get('post') as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <div class="form-group">
            <label class="form-group__label" for="address">住所</label>
            <input class="form-group__input" name="address" type="text" value="{{ old('address', $profile->address ?? '') }}">
        </div>
        @if ($errors->has('address'))
        <div class="form__error">
            <ul>
                @foreach ($errors->get('address') as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <div class="form-group">
            <label class="form-group__label" for="building">建物名</label>
            <input class="form-group__input" name="building" type="text" value="{{ old('building', $profile->building ?? '') }}">
        </div>
        @if ($errors->has('building'))
        <div class="form__error">
            <ul>
                @foreach ($errors->get('building') as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <input class="form-button" type="submit" value="更新する">
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