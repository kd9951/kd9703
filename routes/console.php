<?php

use App\Extensions\Logger\ConsoleLogger;
use Glover\Constants\Job\Priority;
use Glover\Constants\LoginMethod;
use Glover\Constants\Media;
use Glover\Entities\Media\Account;
use Glover\Entities\Media\Tag;
use Glover\Entities\Media\Tags;
use Glover\Entities\Owner\Owner;
use Glover\Logger\Interfaces\OwnerLogger;
use Glover\Logger\Interfaces\SystemLogger;
use Illuminate\Support\Facades\Artisan;
use Psr\Log\LoggerInterface;

/**
 * キューを実行
 */
Artisan::command('app:exec', function (
    \Glover\Usecases\Interfaces\Worker\QueWorker $QueWorker,
    \Glover\Logger\Interfaces\SystemLogger $systemLogger
) {
    try {
        $QueWorker([
            'serial'    => time(),
            'limit_sec' => 180,
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

// --------------------------------- 以下テストコマンド

/**
 * 初回 オーナーとアカウントを登録
 */
Artisan::command('app:init', function (
    \Glover\Resources\Interfaces\Account\Account $Account,
    \Glover\Resources\Interfaces\Owner\Owner $Owner
) {

    $owner = $Owner->regist(new Owner([
        'owner_name'   => 'KD9951',
        'login_method' => LoginMethod::ID_PW(),
        'login_id'     => 'kd9951@gmail.com',
        'password'     => 'plainpassword',
    ]));

    $account = $Account->regist($owner, new Account([
        'media'        => Media::GREEN_SNAP,
        'account_id'   => '22737',
        'login_method' => LoginMethod::ID_PW(),
        'login_id'     => 'kd8339@gmail.com',
        'password'     => 'Gaiwhe7d',
    ]));

    return $account->toArray();
});

/**
 * アカウントプロフィール更新
 */
Artisan::command('app:update-profile', function (
    \Glover\MediaBinder $MediaBinder,
    \Glover\Resources\Interfaces\Account\Account $Account,
    \Glover\Resources\Interfaces\Owner\Owner $Owner
) {

    $owner = $Owner->retrieveByCredentials([
        'login_id' => 'kd9951@gmail.com',
    ]);

    $account = $Account->getList($owner)[0];

    $MediaBinder->bind($account->media);

    $UpdateProfile = app(\Glover\Usecases\Interfaces\Crawl\UpdateProfile::class);

    $UpdateProfile([
        'account'   => $account,
        'limit_sec' => 300,
    ]);
});

/**
 * ジョブを登録
 */
Artisan::command('app:regist-job', function (
    \Glover\Resources\Interfaces\Account\Account $Account,
    \Glover\Resources\Interfaces\Owner\Owner $Owner,
    \Glover\Resources\Interfaces\Job\Job $Job
) {

    $owner = $Owner->retrieveByCredentials([
        'login_id' => 'kd9951@gmail.com',
    ]);

    $account = $Account->getList($owner)[0];

    // フォローしているユーザーを全チェック
    $Job->store($owner, $account, 'Glover\Usecases\Glover\Worker\Job\StoreAllFollowings', Priority::HIGH(1), 1);

    // フォロワーを全チェック
    // $Job->store($owner, $account, 'Glover\Usecases\Glover\Worker\Job\StoreAllFollwers', Priority::HIGH(2), 1);
});

/**
 * フォロータグを追加
 */
Artisan::command('app:regist-tags', function (
    \Glover\Resources\Interfaces\Account\Account $Account,
    \Glover\Resources\Interfaces\Owner\Owner $Owner,
    \Glover\Resources\Interfaces\Tag\WatchingTag $Job
) {

    $owner = $Owner->retrieveByCredentials([
        'login_id' => 'kd9951@gmail.com',
    ]);

    $account = $Account->getList($owner)[0];

    $subject = app(\Glover\Resources\Interfaces\Tag\WatchingTag::class);

    ///////////////////////
    // リストに追加
    $subject->addList($account, new Tags([new Tag([
        'media'    => $account->media,
        'tag_id'   => '1823',
        'tag_body' => 'セダム属',
    ]), new Tag([
        'media'    => $account->media,
        'tag_id'   => '3643',
        'tag_body' => 'アエオニウム属',
    ]), new Tag([
        'media'    => $account->media,
        'tag_id'   => '6311',
        'tag_body' => 'フェロカクタス属',
    ]), new Tag([
        'media'    => $account->media,
        'tag_id'   => '79523',
        'tag_body' => 'テフロカクタス属',
    ])]));
});

/**
 * DailyJob
 */
Artisan::command('app:daily-job', function (
    \Glover\Resources\Interfaces\Account\Account $Account,
    \Glover\Resources\Interfaces\Owner\Owner $Owner,
    \Glover\Resources\Interfaces\Job\Job $Job,
    \Glover\Usecases\Interfaces\Worker\QueWorker $QueWorker
) {
    $owner = $Owner->retrieveByCredentials([
        'login_id' => 'kd9951@gmail.com',
    ]);

    $account = $Account->getList($owner)[0];

    // ジョブを登録
    $Job->store($owner, $account, 'Glover\Usecases\Glover\Worker\Job\DailyJob', Priority::MIDDLE(0), 0);

    $QueWorker([
        'serial'    => time(),
        'limit_sec' => 180,
    ]);

});

/**
 * フォロー
 */
Artisan::command('app:auto-follow', function (
) {
    app()->bind(LoggerInterface::class, ConsoleLogger::class);
    app()->bind(SystemLogger::class, ConsoleLogger::class);
    app()->bind(OwnerLogger::class, ConsoleLogger::class);

    $Account     = app('Glover\Resources\Interfaces\Account\Account');
    $Owner       = app('Glover\Resources\Interfaces\Owner\Owner');
    $MediaBinder = app('Glover\MediaBinder');

    $owner = $Owner->retrieveByCredentials([
        'login_id' => 'kd9951@gmail.com',
    ]);

    $account = $Account->getList($owner)[0];

    $MediaBinder->bind($account->media);
    app()->bind(LoggerInterface::class, ConsoleLogger::class);
    app()->bind(SystemLogger::class, ConsoleLogger::class);
    app()->bind(OwnerLogger::class, ConsoleLogger::class);

    $AutoFollow = app(\Glover\Usecases\Interfaces\Crawl\AutoFollow::class);

    $result = ($AutoFollow)([
        'account'   => $account,
        'limit_sec' => 60,
    ]);

    $DailyTotal = app(\Glover\Usecases\Interfaces\Analyze\DailyTotal::class);

    $result = ($DailyTotal)([
        'account' => $account,
    ]);
});

/**
 * フォロー解除
 */
Artisan::command('app:auto-unfollow', function (
) {
    app()->bind(LoggerInterface::class, ConsoleLogger::class);
    app()->bind(SystemLogger::class, ConsoleLogger::class);
    app()->bind(OwnerLogger::class, ConsoleLogger::class);

    $Account     = app('Glover\Resources\Interfaces\Account\Account');
    $Owner       = app('Glover\Resources\Interfaces\Owner\Owner');
    $MediaBinder = app('Glover\MediaBinder');

    $owner = $Owner->retrieveByCredentials([
        'login_id' => 'kd9951@gmail.com',
    ]);

    $account = $Account->getList($owner)[0];

    $MediaBinder->bind($account->media);
    app()->bind(LoggerInterface::class, ConsoleLogger::class);
    app()->bind(SystemLogger::class, ConsoleLogger::class);
    app()->bind(OwnerLogger::class, ConsoleLogger::class);

    $AutoUnfollow = app(\Glover\Usecases\Interfaces\Crawl\AutoUnfollow::class);

    $result = ($AutoUnfollow)([
        'account'   => $account,
        'limit_sec' => 60,
    ]);

    $DailyTotal = app(\Glover\Usecases\Interfaces\Analyze\DailyTotal::class);

    $result = ($DailyTotal)([
        'account' => $account,
    ]);
});

/**
 * 通知を取得
 */
Artisan::command('app:crawl-notices', function (
    \Glover\Resources\Interfaces\Account\Account $Account,
    \Glover\Resources\Interfaces\Owner\Owner $Owner,
    \Glover\MediaBinder $MediaBinder
) {
    $owner = $Owner->retrieveByCredentials([
        'login_id' => 'kd9951@gmail.com',
    ]);

    $account = $Account->getList($owner)[0];

    $MediaBinder->bind($account->media);

    $UpdateStatusByNotices = app(\Glover\Usecases\Interfaces\Crawl\UpdateStatusByNotices::class);

    $result = ($UpdateStatusByNotices)([
        'account'   => $account,
        'limit_sec' => 60,
    ]);
});

/**
 * テスト
 */
Artisan::command('app:test', function (
    \Glover\Resources\Interfaces\Account\Account $Account,
    \Glover\Resources\Interfaces\Owner\Owner $Owner
) {
    $owner = $Owner->retrieveByCredentials([
        'login_id' => 'kd9951@gmail.com',
    ]);

    $account = $Account->getList($owner)[0];

    // $result = app(\Glover\Resources\Interfaces\Account\FollowingAccount::class)->getToBeUnfollow($account);
    // $result = app(\Glover\Resources\Interfaces\Follow\Following::class)->getToBeUnfollow($account);
    // $result = app(\Glover\Usecases\Interfaces\Crawl\UpdateStatusByNotices::class)(['account' => $account, 'limit_sec' => 10]);
    $result = app(\Glover\Usecases\Interfaces\Analyze\DailyTotal::class)(['account' => $account]);

    var_dump($result);
    // var_dump($result->count());
    // var_dump($result[0]->toArray());
});
