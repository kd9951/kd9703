<?php

use Illuminate\Support\Facades\Artisan;
use Kd9703\Constants\Media;
use Kd9703\Entities\Media\Account;

/**
 * すべてのアカウントの一般公開情報を更新
 */
Artisan::command('app:update-users {limit_sec=50}', function (
    \Kd9703\Logger\Interfaces\SystemLogger $systemLogger,
    \Kd9703\Resources\Interfaces\Account\Account $Account,
    \Kd9703\MediaBinder $MediaBinder
) {
    try {
        $account = $Account->getOne(Media::TWITTER(), config('services.twitter.owner_twitter_id'));

        $MediaBinder->bind($account);

        $UpdateUsers = app(\Kd9703\Usecases\UpdateUsers::class);

        $result = ($UpdateUsers)([
            'account'   => $account,
            'limit_sec' => $this->argument('limit_sec'),
        ]);

    } catch (\Throwable $e) {

        $systemLogger->error($e->getMessage(), [
            'line' => $e->getLine(),
            'file' => $e->getFile(),
            'code' => $e->getCode(),
            'body' => (string) $e,
        ]);

        echo (string) $e;
    }
});

/**
 * 利用中アカウントの非公開情報を含んだ詳細情報を取得・更新
 */
Artisan::command('app:update-using-user {limit_sec=50}', function (
    \Kd9703\Logger\Interfaces\SystemLogger $systemLogger,
    \Kd9703\Resources\Interfaces\Account\Account $Account,
    \Kd9703\MediaBinder $MediaBinder
) {
    try {
        $account = $Account->getUsingAccountToBeUpdatedNext(Media::TWITTER(), 1)[0] ?? null;
        // $account = $Account->getOne(Media::TWITTER(), '1259724960664174592'); // 立花さん
        // $account = $Account->getOne(Media::TWITTER(), config('services.twitter.owner_twitter_id'));

        $MediaBinder->bind($account);
        $reviewed_as_using_user_at = $account->reviewed_as_using_user_at ?? 'NULL';
        $systemLogger->info("UpdateUsingUser selected {$account->username}({$account->account_id}) last updated at {$reviewed_as_using_user_at}.");

        $UpdateUsers = app(\Kd9703\Usecases\UpdateUsingUser::class);

        $result = ($UpdateUsers)([
            'account'   => $account,
            'limit_sec' => $this->argument('limit_sec'),
        ]);

    } catch (\Throwable $e) {

        $systemLogger->error($e->getMessage(), [
            'line' => $e->getLine(),
            'file' => $e->getFile(),
            'code' => $e->getCode(),
            'body' => (string) $e,
        ]);

        echo (string) $e;
    }
});

Artisan::command('app:get-new-users {limit_sec=15}', function (
    \Kd9703\Logger\Interfaces\SystemLogger $systemLogger,
    \Kd9703\Resources\Interfaces\Account\Account $Account,
    \Kd9703\MediaBinder $MediaBinder
) {
    try {
        $account = $Account->getOne(Media::TWITTER(), config('services.twitter.owner_twitter_id'));

        $MediaBinder->bind($account);

        $GetNewUsers = app(\Kd9703\Usecases\GetNewUsers::class);

        $result = ($GetNewUsers)([
            'account'   => $account,
            'limit_sec' => $this->argument('limit_sec'),
        ]);

    } catch (\Throwable $e) {

        $systemLogger->error($e->getMessage(), [
            'line' => $e->getLine(),
            'file' => $e->getFile(),
            'code' => $e->getCode(),
            'body' => (string) $e,
        ]);

        echo (string) $e;
    }
});

Artisan::command('app:update-kpi', function (
    \Kd9703\Logger\Interfaces\SystemLogger $systemLogger
) {
    try {
        $UpdateKpi = app(\Kd9703\Usecases\UpdateKpi::class);

        ($UpdateKpi)([]);

    } catch (\Throwable $e) {

        $systemLogger->error($e->getMessage(), [
            'line' => $e->getLine(),
            'file' => $e->getFile(),
            'code' => $e->getCode(),
            'body' => (string) $e,
        ]);

        echo (string) $e;
    }
});
