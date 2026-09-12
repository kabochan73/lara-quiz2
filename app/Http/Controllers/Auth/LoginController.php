<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * ログインフォームを表示する。
     * 要件定義により会員登録機能は持たないため、登録画面への導線は一切置かない。
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * ログイン処理。Seederで投入した管理者アカウント以外は存在しないので、
     * 実質「唯一のユーザーとしてログインできるか」のチェックになる。
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials)) {
            return back()
                ->withErrors(['email' => 'メールアドレスまたはパスワードが正しくありません。'])
                ->onlyInput('email');
        }

        // セッション固定攻撃を防ぐため、ログイン成功時にセッションIDを再発行する
        $request->session()->regenerate();

        // ログイン後の着地点はカテゴリ画面にする
        return redirect()->intended(route('categories.index'));
    }

    /**
     * ログアウトしてセッションを完全に破棄する。
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
