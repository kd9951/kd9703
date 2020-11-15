<?php

use Illuminate\Support\Facades\Artisan;

/**
 * ユーザー情報を更新
 */
Artisan::command('app:update-users {limit_sec=50}', function (
    \Kd9703\Usecases\UpdateUsers $UpdateUsers
) {
    $result = ($UpdateUsers)([
        'limit_sec' => $this->argument('limit_sec'),
    ]);
});
