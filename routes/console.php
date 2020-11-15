<?php

use Illuminate\Support\Facades\Artisan;
use Kd9703\Constants\Media;
use Kd9703\Entities\Media\Account;

/**
 * ユーザー情報を更新
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
