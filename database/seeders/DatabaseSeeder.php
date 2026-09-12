<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * このアプリは会員登録機能を持たず、ログインできるのは管理者1名のみ(要件定義2章)。
     * そのため、そのただ1人のアカウントをSeederで直接投入する。
     * プロトタイプの個人利用なので、.envに分離せずここに固定値で書く。
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => 'password',
            ]
        );
    }
}
