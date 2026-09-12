# lara-quiz2

自由記述の解答をClaude APIが採点する、個人用の学習アプリ。要件の詳細は [doc/requirements.md](doc/requirements.md) を参照。

## 開発環境

ローカルにPHP/Composerは不要。Docker(docker-compose)で完結させる。

```sh
docker compose up -d               # app(PHP-FPM) / nginx / postgres を起動
docker compose exec app php artisan migrate
docker compose exec app composer install
```

- アプリ: http://localhost:8000
- DB: PostgreSQL(サービス名 `db`、ホストからは `localhost:5432`)

## 技術スタック

- Laravel + Blade(Livewireは使わない)
- PostgreSQL
- AI採点: Anthropic Claude API
