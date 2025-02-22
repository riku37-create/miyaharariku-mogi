<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>coachtechフリマ</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
    <script src="https://kit.fontawesome.com/0f5967e132.js" crossorigin="anonymous"></script>{{--アイコン--}}
</head>

<body>
    <header class="header">
        <div class="header-inner">
            <div class="header-inner__title">
                <a class="title-a" href="{{ route('product.index')}}">
                    <img class="title-image" src="{{ asset('images/logo.svg') }}">
                </a>
            </div>
            @yield('search')
            <nav class="header-inner__nav">
                <ul class="nav-list">
                    <li class="nav-item">
                        @if (Auth::check())
                        <form action="/logout" method="post">
                            @csrf
                            <button class="nav-item__button">ログアウト</button>
                        </form>
                        @else
                            <a class="nav-item__a" href="/login">ログイン</a>
                        @endif
                    </li>
                    <li class="nav-item"><a class="nav-item__a" href="{{ route('profile.index') }}">マイページ</a></li>
                    <li class="nav-item"><a class="nav-item__a" href="{{ route('product.sell') }}">出品</a></li>
                </ul>
            </nav>
        </div>
    </header>
    @yield('content')

    @yield('script')
</body>