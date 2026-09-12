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
            padding: 64px 20px;
        }
        .card {
            background: #fff;
            border: 1px solid #e2e5eb;
            border-radius: 8px;
            padding: 32px;
        }
        h1 {
            font-size: 20px;
            margin: 0 0 24px;
        }
        label {
            display: block;
            font-size: 13px;
            color: #555;
            margin-bottom: 4px;
        }
        input[type=email], input[type=password], input[type=text] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 16px;
            font-size: 14px;
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
        .errors {
            background: #fdecea;
            color: #b3261e;
            border-radius: 4px;
            padding: 10px 14px;
            margin-bottom: 16px;
            font-size: 13px;
        }
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 24px;
            background: #fff;
            border-bottom: 1px solid #e2e5eb;
        }
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
    {{-- ログイン済みの画面にだけ、ログアウトボタン付きのヘッダーを出す --}}
    @auth
        <nav>
            <span>{{ config('app.name') }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">ログアウト</button>
            </form>
        </nav>
    @endauth

    <div class="page">
        {{ $slot }}
    </div>
</body>
</html>
