<x-layout title="ホーム">
    <div class="card">
        <h1>ログインしました</h1>
        <p>
            {{ auth()->user()->name }} さん、こんにちは。<br>
            問題管理・回答・履歴などの画面はこれから実装していきます。
        </p>
    </div>
</x-layout>
