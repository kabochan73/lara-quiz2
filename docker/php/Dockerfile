# PHP-FPM本体のイメージ。composerでインストール済みの依存パッケージがPHP 8.4.1以上を
# 要求するため(vendor/composer/platform_check.php参照)、8.4系を使う。
FROM php:8.4-fpm

# LaravelがPostgreSQLに接続する際に必要な pdo_pgsql と、
# アプリの依存パッケージが使う mbstring / bcmath / zip を追加でインストールする。
# libpq-dev / libzip-dev はそれぞれ pdo_pgsql / zip 拡張のビルドに必要なライブラリ。
RUN apt-get update && apt-get install -y \
        libpq-dev \
        libzip-dev \
        libonig-dev \
        unzip \
        git \
    && docker-php-ext-install pdo pdo_pgsql mbstring bcmath zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# composerコマンド自体は公式composerイメージから拝借する(自前ビルド不要)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# コンテナ内でのアプリ配置場所。docker-compose側でホストのプロジェクトを
# ここにバインドマウントするので、このDockerfileではソースコードはコピーしない。
WORKDIR /var/www/html
