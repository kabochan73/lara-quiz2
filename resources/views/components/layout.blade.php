@props(['title' => null, 'wide' => false])
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>

    {{-- Node/Viteのビルド環境を用意していないので、Tailwindは使わず素のCSSで最低限の見た目だけ整える --}}
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: -apple-system, "Hiragino Sans", "Yu Gothic", sans-serif;
            background: #f4f5f7;
            color: #1f2430;
        }
        .page {
            max-width: 480px;
            margin: 0 auto;
            padding: 48px 20px;
        }
        /* カテゴリ・問題一覧などフォーム+テーブルを並べる画面はもう少し幅を持たせる */
        .page.wide {
            max-width: 760px;
        }
        .card {
            background: #fff;
            border: 1px solid #e2e5eb;
            border-radius: 8px;
            padding: 32px;
        }
        .card + .card {
            margin-top: 16px;
        }
        h1 {
            font-size: 20px;
            margin: 0 0 24px;
        }
        h2 {
            font-size: 16px;
            margin: 0 0 16px;
        }
        label {
            display: block;
            font-size: 13px;
            color: #555;
            margin-bottom: 4px;
        }
        input[type=email], input[type=password], input[type=text], select, textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 16px;
            font-size: 14px;
            font-family: inherit;
        }
        button {
            width: 100%;
            padding: 10px 12px;
            background: #2f5233;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
        }
        button:hover { background: #244027; }
        /* 一覧の行内にある「編集」「削除」のような小さいボタン・リンク用 */
        .btn-small {
            width: auto;
            display: inline-block;
            padding: 4px 10px;
            font-size: 12px;
        }
        .btn-danger { background: #b3261e; }
        .btn-danger:hover { background: #8f1f19; }
        .inline-form { display: inline; margin-left: 6px; }
        .errors {
            background: #fdecea;
            color: #b3261e;
            border-radius: 4px;
            padding: 10px 14px;
            margin-bottom: 16px;
            font-size: 13px;
        }
        .status {
            background: #eaf5ea;
            color: #2f5233;
            border-radius: 4px;
            padding: 10px 14px;
            margin-bottom: 16px;
            font-size: 13px;
        }
        .list-plain {
            list-style: none;
            margin: 8px 0 0;
            padding-left: 20px;
        }
        .row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .row:last-child { border-bottom: none; }
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 24px;
            background: #fff;
            border-bottom: 1px solid #e2e5eb;
        }
        nav .links a {
            margin-right: 16px;
            color: #1f2430;
            text-decoration: none;
            font-size: 14px;
        }
        nav .links a:hover { text-decoration: underline; }
        nav form { display: inline; }
        nav form button {
            width: auto;
            background: transparent;
            color: #555;
            border: 1px solid #ccc;
            padding: 6px 14px;
        }
    </style>
</head>
<body>
    {{-- ログイン済みの画面にだけ、主要画面へのナビゲーションとログアウトボタンを出す --}}
    @auth
        <nav>
            <div class="links">
                <a href="{{ route('home') }}">ホーム</a>
                <a href="{{ route('questions.index') }}">問題</a>
                <a href="{{ route('categories.index') }}">カテゴリ</a>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">ログアウト</button>
            </form>
        </nav>
    @endauth

    <div class="page {{ $wide ? 'wide' : '' }}">
        {{ $slot }}
    </div>
</body>
</html>
