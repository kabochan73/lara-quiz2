@props(['points'])

{{--
    得点推移の折れ線グラフ。$points は ['label' => '9/12 14:00', 'score' => 82] のような
    コレクションを時系列(古い→新しい)で渡す想定。
    Node/Viteのビルド環境を使わない方針なので、外部のグラフライブラリは使わず
    素のSVGで直接描画している。
--}}
@php
    $width = 640;
    $height = 220;
    $padLeft = 32;
    $padRight = 12;
    $padTop = 12;
    $padBottom = 12;
    $plotWidth = $width - $padLeft - $padRight;
    $plotHeight = $height - $padTop - $padBottom;

    $points = collect($points)->values();
    $count = $points->count();

    // 各点をSVG上の座標(x, y)に変換する。scoreは0〜100なので、そのままplotHeightに正規化できる。
    $coords = $points->map(function ($point, $i) use ($count, $plotWidth, $plotHeight, $padLeft, $padTop) {
        $x = $padLeft + ($count > 1 ? $plotWidth * $i / ($count - 1) : $plotWidth / 2);
        $y = $padTop + $plotHeight - ($point['score'] / 100 * $plotHeight);

        return ['x' => $x, 'y' => $y, 'label' => $point['label'], 'score' => $point['score']];
    });
@endphp

@if ($count === 0)
    <p style="color:#888;">まだ採点データがありません。</p>
@else
    <svg viewBox="0 0 {{ $width }} {{ $height }}" style="width:100%; height:auto;">
        {{-- 0/50/100点の目安の横線 --}}
        @foreach ([0, 50, 100] as $mark)
            @php $y = $padTop + $plotHeight - ($mark / 100 * $plotHeight); @endphp
            <line x1="{{ $padLeft }}" y1="{{ $y }}" x2="{{ $width - $padRight }}" y2="{{ $y }}" stroke="#e2e5eb" stroke-width="1" />
            <text x="0" y="{{ $y + 4 }}" font-size="11" fill="#888">{{ $mark }}</text>
        @endforeach

        @if ($count > 1)
            <polyline
                fill="none"
                stroke="#2f5233"
                stroke-width="2"
                points="{{ $coords->map(fn ($c) => "{$c['x']},{$c['y']}")->implode(' ') }}"
            />
        @endif

        {{-- 各点にカーソルを合わせると日時と点数がツールチップで見える --}}
        @foreach ($coords as $c)
            <circle cx="{{ $c['x'] }}" cy="{{ $c['y'] }}" r="4" fill="#2f5233">
                <title>{{ $c['label'] }}: {{ $c['score'] }}点</title>
            </circle>
        @endforeach
    </svg>
@endif
