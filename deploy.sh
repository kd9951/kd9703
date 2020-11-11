#!/bin/sh
# デプロイ時にサーバー上で実行されるスクリプト

cd /var/www/html

chmod 755 crawl-ls.sh

# コンパイル済キャッシュをクリア
artisan clear-compiled

# vendor以下をインストールして autoloadファイルを最適化
composer install --no-dev

# 環境設定をコピー
cp .env.production .env

# DBマイグレーション
php artisan migrate --force

# /configの設定情報を1ファイルにまとめておく
php artisan config:cache

# route情報をまとめておく CLOSUREがあると使用できない
php artisan route:cache

# viewキャッシュをクリア
php artisan view:clear

# キャッシュをクリア
php artisan cache:clear
sudo chown -R www-data:www-data -R storage/
sudo chmod 777 -R storage/

# ログをクリア
rm -f storage/logs/*.log
touch storage/logs/laravel.log
chmod 666 storage/logs/laravel.log
