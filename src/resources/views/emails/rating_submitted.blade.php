<p>{{ $ratee->name }}さん、こんにちは。</p>
<p>{{ $rater->name }}さんから取引の評価が届きました。</p>
<p>評価：{{ $rating }} / 5</p>

<p>マイページで詳細を確認できます。</p>
<a href="{{ route('profile.index')}}">マイページへ</a>