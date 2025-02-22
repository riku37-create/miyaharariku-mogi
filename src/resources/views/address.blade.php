@extends('layout.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/address.css') }}">
@endsection

@section('content')
<div class="user-address">
    <h1 class="user-address__title">住所の変更</h1>
    <form class="user-address-main" action="{{ route('address.update', ['id' => $id]) }}" method="post">
        @csrf
        <div class="address-content">
            <label class="address-content__label" for="past_address">過去の住所を選択</label>
            <select class="address-content__form-select" id="past_address" class="address-select">
                <option value="">新しい住所を入力する</option>
                @foreach ($past_addresses as $past)
                    <option value="{{ $past->post }},{{ $past->address }},{{ $past->building }}">
                        〒{{ $past->post }} {{ $past->address }} {{ $past->building }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="address-content">
            <label class="address-content__label" for="post">郵便番号</label>
            <input class="address-content__form" id="post" type="text" name="post" value="{{ old('post', $profile->post ?? '') }}">
            @if ($errors->has('post'))
            <div class="form__error">
                <ul>
                    @foreach ($errors->get('post') as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
        <div class="address-content">
            <label class="address-content__label" for="address">住所</label>
            <input class="address-content__form" id="address" type="text" name="address" value="{{ old('address', $profile->address ?? '') }}">
            @if ($errors->has('address'))
            <div class="form__error">
                <ul>
                    @foreach ($errors->get('address') as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
        <div class="address-content">
            <label class="address-content__label" for="building" name="building">建物名</label>
            <input class="address-content__form" id="building" type="text" name="building" value="{{ old('building', $profile->building ?? '') }}">
            @if ($errors->has('building'))
            <div class="form__error">
                <ul>
                    @foreach ($errors->get('building') as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
        <div class="address-content">
            <button class="address-content__button" type="submit">更新する</button>
        </div>
    </form>
</div>

<script>
    document.getElementById('past_address').addEventListener('change', function() {
    let selected = this.value ? this.value.split(',') : null;

    if (selected && selected.length === 3) {
        document.getElementById('post').value = selected[0];
        document.getElementById('address').value = selected[1];
        document.getElementById('building').value = selected[2];
    } else {
        document.getElementById('post').value = '';
        document.getElementById('address').value = '';
        document.getElementById('building').value = '';
    }
});
</script>
@endsection