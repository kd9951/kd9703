<?php

use Crawler\HttpClients\TwitterApi;
use Illuminate\Support\Facades\Artisan;
use Kd9703\Constants\Media;
use Kd9703\Entities\Media\Account;
use Kd9703\Entities\Worker\Job;

/**
 * すべてのアカウントの一般公開情報を更新
 */
Artisan::command('app:update-users {limit_sec=50}', function (
    \Kd9703\Logger\Interfaces\SystemLogger $systemLogger,
    \Kd9703\Logger\Interfaces\OwnerLogger $ownerLogger,
    \Kd9703\Resources\Interfaces\Account\Account $Account,
    \Kd9703\MediaBinder $MediaBinder
) {
    try {
        $account = $Account->getOne(Media::TWITTER(), config('services.twitter.owner_twitter_id'));

        $MediaBinder->bind($account);

        $job = new Job([
            'media'      => $account->media,
            'account_id' => $account->account_id,
            'job_id'     => time(),
            'job_class'  => 'app:update-users',
        ]);
        $systemLogger->startJob($job, time());
        $ownerLogger->startJob($job, time());

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

    if (isset($job)) {
        $systemLogger->endJob($job, time());
        $ownerLogger->endJob($job, time());
    }
});

/**
 * 利用中アカウントの非公開情報を含んだ詳細情報を取得・更新
 */
Artisan::command('app:update-using-user {limit_sec=50}', function (
    \Kd9703\Logger\Interfaces\SystemLogger $systemLogger,
    \Kd9703\Logger\Interfaces\OwnerLogger $ownerLogger,
    \Kd9703\Resources\Interfaces\Account\Account $Account,
    Kd9703\Resources\Interfaces\Owner\Configuration $Configuration,
    \Kd9703\MediaBinder $MediaBinder
) {
    try {
        $account = $Account->getUsingAccountToBeUpdatedNext(Media::TWITTER(), 1)[0] ?? null;
        // $account = $Account->getOne(Media::TWITTER(), config('services.twitter.owner_twitter_id'));

        $MediaBinder->bind($account);

        $reviewed_as_using_user_at = $account->reviewed_as_using_user_at ?? 'NULL';
        $systemLogger->info("UpdateUsingUser selected {$account->username}({$account->account_id}) last updated at {$reviewed_as_using_user_at}.");

        $job = new Job([
            'media'      => $account->media,
            'account_id' => $account->account_id,
            'job_id'     => time(),
            'job_class'  => 'app:update-using-user',
        ]);
        $systemLogger->startJob($job, time());
        $ownerLogger->startJob($job, time());

        ///////////////////////////////////////////////////////////////////////
        // コミュニケーション更新
        $UpdateUsers = app(\Kd9703\Usecases\UpdateUsingUser::class);

        $result = ($UpdateUsers)([
            'account'   => $account,
            'limit_sec' => $this->argument('limit_sec'),
        ]);

        ///////////////////////////////////////////////////////////////////////
        // 自動フォロー承認
        $config = $Configuration->get($account);

        $AutoFollowAccept = app(\Kd9703\Usecases\AutoFollowAccept::class);

        $result = ($AutoFollowAccept)([
            'account'                           => $account,
            'limit_sec'                         => 30,
            'auto_follow_back'                  => $config->auto_follow_back,
            'auto_reject'                       => $config->auto_reject,
            'follow_back_only_tweets_more_than' => $config->follow_back_only_tweets_more_than,
            'follow_back_only_profile_contains' => $config->follow_back_only_profile_contains,
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

    if (isset($job)) {
        $systemLogger->endJob($job, time());
        $ownerLogger->endJob($job, time());
    }
});

Artisan::command('app:get-new-users {limit_sec=15}', function (
    \Kd9703\Logger\Interfaces\SystemLogger $systemLogger,
    \Kd9703\Logger\Interfaces\OwnerLogger $ownerLogger,
    \Kd9703\Resources\Interfaces\Account\Account $Account,
    \Kd9703\MediaBinder $MediaBinder
) {
    try {
        $account = $Account->getOne(Media::TWITTER(), config('services.twitter.owner_twitter_id'));

        $MediaBinder->bind($account);

        $job = new Job([
            'media'      => $account->media,
            'account_id' => $account->account_id,
            'job_id'     => time(),
            'job_class'  => 'app:get-new-users',
        ]);
        $systemLogger->startJob($job, time());
        $ownerLogger->startJob($job, time());

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

    if (isset($job)) {
        $systemLogger->endJob($job, time());
        $ownerLogger->endJob($job, time());
    }
});

Artisan::command('app:update-kpi', function (
    \Kd9703\Logger\Interfaces\SystemLogger $systemLogger,
    \Kd9703\Logger\Interfaces\OwnerLogger $ownerLogger
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

Artisan::command('app:daily', function (
    \Kd9703\Logger\Interfaces\SystemLogger $systemLogger
) {
    try {
        $systemLogger->notice("Daily Batch.");

        $systemLogger->info("Deleting logs.");
        Kd9703\Eloquents\Support\OwnerLog::where('created_at', '<', date('Y-m-d 00:00:00', strtotime('-7 days')))->delete();
        Kd9703\Eloquents\Support\SystemLog::where('created_at', '<', date('Y-m-d 00:00:00', strtotime('-7 days')))->delete();
        $systemLogger->info("Deleted logs.");

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

// // --------------------------------- 以下テストコマンド

// /**
//  * 初回 オーナーとアカウントを登録
//  */
// Artisan::command('app:init', function (
//     \Kd9703\Resources\Interfaces\Account\Account $Account,
//     \Kd9703\Resources\Interfaces\Owner\Owner $Owner
// ) {

//     $account = $Account->regist($owner, new Account([
//         'media'        => Media::GREEN_SNAP,
//         'account_id'   => '22737',
//         'login_method' => LoginMethod::ID_PW(),
//         'login_id'     => 'kd8339@gmail.com',
//         'password'     => 'Gaiwhe7d',
//     ]));

//     return $account->toArray();
// });

// /**
//  * アカウントプロフィール更新
//  */
// Artisan::command('app:update-profile', function (
//     \Kd9703\MediaBinder $MediaBinder,
//     \Kd9703\Resources\Interfaces\Account\Account $Account,
//     \Kd9703\Resources\Interfaces\Owner\Owner $Owner
// ) {

//     $owner = $Owner->retrieveByCredentials([
//         'login_id' => 'kd9951@gmail.com',
//     ]);

//     $account = $Account->getList($owner)[0];

//     $MediaBinder->bind($account->media);

//     $UpdateProfile = app(\Kd9703\Usecases\Interfaces\Crawl\UpdateProfile::class);

//     $UpdateProfile([
//         'account'   => $account,
//         'limit_sec' => 300,
//     ]);
// });

// /**
//  * ジョブを登録
//  */
// Artisan::command('app:regist-job', function (
//     \Kd9703\Resources\Interfaces\Account\Account $Account,
//     \Kd9703\Resources\Interfaces\Owner\Owner $Owner,
//     \Kd9703\Resources\Interfaces\Job\Job $Job
// ) {

//     $owner = $Owner->retrieveByCredentials([
//         'login_id' => 'kd9951@gmail.com',
//     ]);

//     $account = $Account->getList($owner)[0];

//     // フォローしているユーザーを全チェック
//     $Job->store($owner, $account, 'Kd9703\Usecases\Kd9703\Worker\Job\StoreAllFollowings', Priority::HIGH(1), 1);

//     // フォロワーを全チェック
//     // $Job->store($owner, $account, 'Kd9703\Usecases\Kd9703\Worker\Job\StoreAllFollwers', Priority::HIGH(2), 1);
// });

// /**
//  * フォロータグを追加
//  */
// Artisan::command('app:regist-tags', function (
//     \Kd9703\Resources\Interfaces\Account\Account $Account,
//     \Kd9703\Resources\Interfaces\Owner\Owner $Owner,
//     \Kd9703\Resources\Interfaces\Tag\WatchingTag $Job
// ) {

//     $owner = $Owner->retrieveByCredentials([
//         'login_id' => 'kd9951@gmail.com',
//     ]);

//     $account = $Account->getList($owner)[0];

//     $subject = app(\Kd9703\Resources\Interfaces\Tag\WatchingTag::class);

//     ///////////////////////
//     // リストに追加
//     $subject->addList($account, new Tags([new Tag([
//         'media'    => $account->media,
//         'tag_id'   => '1823',
//         'tag_body' => 'セダム属',
//     ]), new Tag([
//         'media'    => $account->media,
//         'tag_id'   => '3643',
//         'tag_body' => 'アエオニウム属',
//     ]), new Tag([
//         'media'    => $account->media,
//         'tag_id'   => '6311',
//         'tag_body' => 'フェロカクタス属',
//     ]), new Tag([
//         'media'    => $account->media,
//         'tag_id'   => '79523',
//         'tag_body' => 'テフロカクタス属',
//     ])]));
// });

// /**
//  * DailyJob
//  */
// Artisan::command('app:daily-job', function (
//     \Kd9703\Resources\Interfaces\Account\Account $Account,
//     \Kd9703\Resources\Interfaces\Owner\Owner $Owner,
//     \Kd9703\Resources\Interfaces\Job\Job $Job,
//     \Kd9703\Usecases\Interfaces\Worker\QueWorker $QueWorker
// ) {
//     $owner = $Owner->retrieveByCredentials([
//         'login_id' => 'kd9951@gmail.com',
//     ]);

//     $account = $Account->getList($owner)[0];

//     // ジョブを登録
//     $Job->store($owner, $account, 'Kd9703\Usecases\Kd9703\Worker\Job\DailyJob', Priority::MIDDLE(0), 0);

//     $QueWorker([
//         'serial'    => time(),
//         'limit_sec' => 180,
//     ]);

// });

// /**
//  * フォロー
//  */
// Artisan::command('app:auto-follow', function (
// ) {
//     app()->bind(LoggerInterface::class, ConsoleLogger::class);
//     app()->bind(SystemLogger::class, ConsoleLogger::class);
//     app()->bind(OwnerLogger::class, ConsoleLogger::class);

//     $Account     = app('Kd9703\Resources\Interfaces\Account\Account');
//     $Owner       = app('Kd9703\Resources\Interfaces\Owner\Owner');
//     $MediaBinder = app('Kd9703\MediaBinder');

//     $owner = $Owner->retrieveByCredentials([
//         'login_id' => 'kd9951@gmail.com',
//     ]);

//     $account = $Account->getList($owner)[0];

//     $MediaBinder->bind($account->media);
//     app()->bind(LoggerInterface::class, ConsoleLogger::class);
//     app()->bind(SystemLogger::class, ConsoleLogger::class);
//     app()->bind(OwnerLogger::class, ConsoleLogger::class);

//     $AutoFollow = app(\Kd9703\Usecases\Interfaces\Crawl\AutoFollow::class);

//     $result = ($AutoFollow)([
//         'account'   => $account,
//         'limit_sec' => 60,
//     ]);

//     $DailyTotal = app(\Kd9703\Usecases\Interfaces\Analyze\DailyTotal::class);

//     $result = ($DailyTotal)([
//         'account' => $account,
//     ]);
// });

// /**
//  * フォロー解除
//  */
// Artisan::command('app:auto-unfollow', function (
// ) {
//     app()->bind(LoggerInterface::class, ConsoleLogger::class);
//     app()->bind(SystemLogger::class, ConsoleLogger::class);
//     app()->bind(OwnerLogger::class, ConsoleLogger::class);

//     $Account     = app('Kd9703\Resources\Interfaces\Account\Account');
//     $Owner       = app('Kd9703\Resources\Interfaces\Owner\Owner');
//     $MediaBinder = app('Kd9703\MediaBinder');

//     $owner = $Owner->retrieveByCredentials([
//         'login_id' => 'kd9951@gmail.com',
//     ]);

//     $account = $Account->getList($owner)[0];

//     $MediaBinder->bind($account->media);
//     app()->bind(LoggerInterface::class, ConsoleLogger::class);
//     app()->bind(SystemLogger::class, ConsoleLogger::class);
//     app()->bind(OwnerLogger::class, ConsoleLogger::class);

//     $AutoUnfollow = app(\Kd9703\Usecases\Interfaces\Crawl\AutoUnfollow::class);

//     $result = ($AutoUnfollow)([
//         'account'   => $account,
//         'limit_sec' => 60,
//     ]);

//     $DailyTotal = app(\Kd9703\Usecases\Interfaces\Analyze\DailyTotal::class);

//     $result = ($DailyTotal)([
//         'account' => $account,
//     ]);
// });

// /**
//  * 通知を取得
//  */
// Artisan::command('app:crawl-notices', function (
//     \Kd9703\Resources\Interfaces\Account\Account $Account,
//     \Kd9703\Resources\Interfaces\Owner\Owner $Owner,
//     \Kd9703\MediaBinder $MediaBinder
// ) {
//     $owner = $Owner->retrieveByCredentials([
//         'login_id' => 'kd9951@gmail.com',
//     ]);

//     $account = $Account->getList($owner)[0];

//     $MediaBinder->bind($account->media);

//     $UpdateStatusByNotices = app(\Kd9703\Usecases\Interfaces\Crawl\UpdateStatusByNotices::class);

//     $result = ($UpdateStatusByNotices)([
//         'account'   => $account,
//         'limit_sec' => 60,
//     ]);
// });

/**
 * テスト
 */
Artisan::command('app:test', function (
    \Kd9703\Resources\Interfaces\Account\Account $Account,
    Kd9703\Resources\Interfaces\Owner\Configuration $Configuration,
    \Kd9703\MediaBinder $MediaBinder,
    \Kd9703\Logger\Interfaces\SystemLogger $systemLogger,
    \Kd9703\Logger\Interfaces\OwnerLogger $ownerLogger
) {
    $account = $Account->getOne(Media::TWITTER(), config('services.twitter.owner_twitter_id'));
    // $account = $Account->getOne(Media::TWITTER(), '1259724960664174592'); // 立花さん

    $config = $Configuration->get($account);

    $MediaBinder->bind($account);

    $job = new Job([
        'media'      => $account->media,
        'account_id' => $account->account_id,
        'job_id'     => time(),
        'job_class'  => 'app:test',
    ]);
    $systemLogger->startJob($job, time());
    $ownerLogger->startJob($job, time());

    ///////////////////////////////////////////////////////////////////////
    $AutoFollowAccept = app(\Kd9703\Usecases\AutoFollowAccept::class);

    $result = ($AutoFollowAccept)([
        'account'                           => $account,
        'limit_sec'                         => 30,
        'auto_follow_back'                  => $config->auto_follow_back,
        'auto_reject'                       => $config->auto_reject,
        'follow_back_only_tweets_more_than' => $config->follow_back_only_tweets_more_than,
        'follow_back_only_profile_contains' => $config->follow_back_only_profile_contains,
    ]);
    dd($result);

    ///////////////////////////////////////////////////////////////////////
    $UpdateKpi = app(\Kd9703\Usecases\UpdateUsingUser::class);

    $result = ($UpdateKpi)([
        'account'   => $account,
        'limit_sec' => 30,
    ]);

    // ///////////////////////////////////////////////////////////////////////
    // $UpdateKpi = app(\Kd9703\Usecases\UpdateKpi::class);

    // $result = ($UpdateKpi)([
    //     'account'   => $account,
    //     'limit_sec' => 30,
    // ]);

    ///////////////////////////////////////////////////////////////////////
    // $subject = app(\Kd9703\Resources\Interfaces\Analyze\Kpi::class);

    // $result = ($subject)->generateNow();
    // dd($result);

    // ///////////////////////////////////////////////////////////////////////
    // $GetNewUsers = app(\Kd9703\Usecases\GetNewUsers::class);

    // $result = ($GetNewUsers)([
    //     'account'   => $account,
    //     'limit_sec' => 30,
    // ]);

    /////////////////////////////////////////////////////////////////////////
    // $UpdateUsers = app(\Kd9703\Usecases\UpdateUsers::class);

    // $result = ($UpdateUsers)([
    //     'limit_sec' => 5,
    // ]);

    /////////////////////////////////////////////////////////////////////////
    // $GetUsers = app(\Kd9703\MediaAccess\Interfaces\GetUsers::class);

    // $result = ($GetUsers)([
    //     'account'   => $account,
    //     'target_accounts'   => new Accounts([$account]),
    //     'limit_sec' => 60,
    // ]);

    if (isset($job)) {
        $systemLogger->endJob($job, time());
        $ownerLogger->endJob($job, time());
    }
});

/**
 * テスト
 */
Artisan::command('app:test-media-access', function (
    \Kd9703\Resources\Interfaces\Account\Account $AccountResource,
    \Kd9703\MediaBinder $MediaBinder
) {

    $account = $AccountResource->getOne(Media::TWITTER(), config('services.twitter.owner_twitter_id'));
    // $account = $AccountResource->getOne(Media::TWITTER(), '1259724960664174592'); // 立花さん

    $MediaBinder->bind($account);

    //////////////////////////////////////////////////////////
    $GetFollowersIncomingInterface = app(\Kd9703\MediaAccess\Twitter\DenyFollowerIncoming::class);
    $result                        = ($GetFollowersIncomingInterface)([
        'account'           => $account,
        'target_account_id' => 763553497337868288,
    ]);
    dd($result->toArray());

    //////////////////////////////////////////////////////////
    $GetFollowersIncomingInterface = app(\Kd9703\MediaAccess\Twitter\AcceptFollowerIncoming::class);
    $result                        = ($GetFollowersIncomingInterface)([
        'account'           => $account,
        'target_account_id' => 1349289029754179584,
    ]);
    dd($result->toArray());
    //     // 2 => 1356411981444435968
    //     // 3 => 1349289029754179584
    //     // 4 => 1359284005745528832
    //     // 5 => 1343469411873669120
    //     // 6 => 1353510012052676608
    //     // 7 => 1350835910665883650
    //     // 8 => 1348988592563970049
    //     // 9 => 1336935071974998016

    //////////////////////////////////////////////////////////
    $GetFollowersIncomingInterface = app(\Kd9703\MediaAccess\Twitter\GetFollowersIncoming::class);
    $result                        = ($GetFollowersIncomingInterface)([
        'account' => $account,
    ]);
    dd($result->pluck('account_id'));

    //////////////////////////////////////////////////////////
    $GetPosts = app(\Kd9703\MediaAccess\Interfaces\GetPosts::class);
    $result   = ($GetPosts)([
        'account'        => $account,
        'limit'          => 10000,
        'since_datetime' => '2020-12-06 23:00:00',
    ]);

    // //////////////////////////////////////////////////////////
    // $GetProfile = app(\Kd9703\MediaAccess\Interfaces\GetProfile::class);
    // $result = ($GetProfile)([
    //     'account' => $account,
    //     'limit'   => 40,
    // ]);

    // //////////////////////////////////////////////////////////
    // $exclude_account_ids = $AccountResource->getAllIds(Media::TWITTER());
    // $GetFollowers        = app(\Kd9703\MediaAccess\Interfaces\GetFollowers::class);
    // $target_account      = new Account(['account_id' => '1261228709580693505']);
    // $result              = ($GetFollowers)([
    //     'account'             => $target_account,
    //     'exclude_account_ids' => $exclude_account_ids,
    //     'limit'               => 20,
    // ]);

    // foreach ($result as $r) {
    //     echo "{$r->from_account->started_at} : {$r->from_account->last_posted_at} : {$r->from_account->fullname} \n";
    // }
    dd($result[0]->toArray(), $result->count());
});

Artisan::command('app:test-api', function (
    \Kd9703\Logger\Interfaces\SystemLogger $systemLogger,
    \Kd9703\Resources\Interfaces\Account\Account $Account,
    \Kd9703\MediaBinder $MediaBinder
) {
    try {
        $account = $Account->getOne(Media::TWITTER(), config('services.twitter.owner_twitter_id'));
        // $account = $Account->getOne(Media::TWITTER(), '1259724960664174592'); // 立花さん

        $MediaBinder->bind($account);

        $client = app(\Crawler\HttpClientInterface::class);

        // // フォローリクエスト一覧
        // $client->get('https://api.twitter.com/1.1/friendships/incoming.json', [
        //     // 'count' => 3,
        //     // 'cursor ' => null,
        // ]);
        // $result = $client->getContentAs('json.array');
        // dd($account->toArray(), $client->getResponseStatusCode(), $result);

        // 0 => 1347935963507429381
        // 1 => 1351412772454633475
        // 2 => 1356411981444435968
        // 3 => 1349289029754179584
        // 4 => 1359284005745528832
        // 5 => 1343469411873669120
        // 6 => 1353510012052676608
        // 7 => 1350835910665883650
        // 8 => 1348988592563970049
        // 9 => 1336935071974998016
        // 10 => 1352832080971894784
        // 11 => 1352597942964613120
        // 12 => 1352951487245078534
        // 13 => 1351490476663140352
        // 14 => 1348988745366663168
        // 15 => 1287051474975875072
        // 16 => 1351509516647501826
        // 17 => 1351014324446375945
        // 18 => 1359875156529745921
        // 19 => 1355684624979124224
        // 20 => 1126058177340960768
        // 21 => 1351104632148340736
        // 22 => 1359029409122951172

        // フォローリクエスト承認
        $client->post('https://api.twitter.com/1/friendships/accept.json', [
            'user_id' => '1351412772454633475',
            // 'cursor ' => null,
        ]);
        $result = $client->getContentAs('json.array');
        dd($client->getResponseStatusCode(), $result);
        // 410
        // null
        // 200
        // array:55 [
        //   "id" => 1350802661759598599
        //   "id_str" => "1350802661759598599"
        //   "name" => "柳川佑平@愛知県"
        //   "screen_name" => "PROGRESSYUHEI"
        //   "location" => "愛知県"
        //   "url" => null
        //   "description" => "システム開発&運送事業の会社を経営しています！｜経済の血液と言われる
        // 物流業界の原動力になりたいと思い21歳の時、起業しました。｜wolx｜色々な方々と繋がりをもっ
        // て楽しくコミュニケーションできたら嬉しいです！｜#中田敦彦オンラインサロン"
        //   "protected" => true
        //   "followers_count" => 162
        //   "fast_followers_count" => 0
        //   "normal_followers_count" => 162
        //   "friends_count" => 157
        //   "listed_count" => 1
        //   "created_at" => "Sun Jan 17 13:50:34 +0000 2021"
        //   "favourites_count" => 25
        //   "utc_offset" => null
        //   "time_zone" => null
        //   "geo_enabled" => false
        //   "verified" => false
        //   "statuses_count" => 0
        //   "media_count" => 0
        //   "lang" => null
        //   "contributors_enabled" => false
        //   "is_translator" => false
        //   "is_translation_enabled" => false
        //   "profile_background_color" => "F5F8FA"
        //   "profile_background_image_url" => null
        //   "profile_background_image_url_https" => null
        //   "profile_background_tile" => false
        //   "profile_image_url" => "http://pbs.twimg.com/profile_images/1350802758392135681/bQfcZBtP_normal.jpg"
        //   "profile_image_url_https" => "https://pbs.twimg.com/profile_images/1350802758392135681/bQfcZBtP_normal.jpg"
        //   "profile_banner_url" => "https://pbs.twimg.com/profile_banners/1350802661759598599/1612707176"
        //   "profile_link_color" => "1DA1F2"
        //   "profile_sidebar_border_color" => "C0DEED"
        //   "profile_sidebar_fill_color" => "DDEEF6"
        //   "profile_text_color" => "333333"
        //   "profile_use_background_image" => true
        //   "has_extended_profile" => true
        //   "default_profile" => true
        //   "default_profile_image" => false
        //   "pinned_tweet_ids" => []
        //   "pinned_tweet_ids_str" => []
        //   "has_custom_timelines" => false
        //   "can_media_tag" => false
        //   "followed_by" => false
        //   "following" => true
        //   "follow_request_sent" => false
        //   "notifications" => false
        //   "muting" => false
        //   "advertiser_account_type" => "none"
        //   "advertiser_account_service_levels" => []
        //   "analytics_type" => "disabled"
        //   "business_profile_state" => "none"
        //   "translator_type" => "none"
        //   "require_some_consent" => false
        // ]

        // ダイレクトメッセージ
        $client->get('/direct_messages/events/list.json', [
            'count' => 3,
            // 'cursor ' => null,
        ]);
        $result = $client->getContentAs('json.array');
        dd($result);

        // 自分の発言と相手へのメンション
        $client->get('/statuses/user_timeline', [
            'user_id'   => 1042566187920515072, //"109830468", //,1260117637847150592,1259579647332716544",
            // 'screen_name' => null,
            // 'since_id' => null,
            // 'count' => null,
            // 'max_id' => null,
            'trim_user' => true,
            // 'exclude_replies' => null,
            // 'include_rts' => null,
        ]);
        $result = $client->getContentAs('json.array');
        dd($result);

        // ダイレクトメッセージ
        $client->get('/direct_messages/events/list.json', [
            'count' => 3,
            // 'cursor ' => null,
        ]);
        $result = $client->getContentAs('json.array');
        dd($result);
        // [
        //     "type" => "message_create"
        //     "id" => "1334656393152929796"
        //     "created_timestamp" => "1607041855501"
        //     "message_create" => array:3 [
        //       "target" => array:1 [
        //         "recipient_id" => "1315175613351641088"
        //       ]
        //       "sender_id" => "1259691897624248320"
        //       "message_data" => array:2 [
        //         "text" => """
        //           ありがとうございます！！\n
        //           \n
        //           5日の夜10時から可能であればお願いしたいです�🙇‍♂️
        //           """
        //         "entities" => array:4 [
        //           "hashtags" => []
        //           "symbols" => []
        //           "user_mentions" => []
        //           "urls" => []
        //         ]
        //       ]
        //     ]
        //   ]

        // 自分へのメンション
        $client->get('/statuses/mentions_timeline', [
            // 'since_id' => null,
            // 'count' => null,
            // 'max_id' => null,
            // 'trim_user' => null,
            // 'include_entities' => null,
        ]);
        $result = $client->getContentAs('json.array');
        dd($result);
        // [
        //     "created_at" => "Mon Nov 16 22:46:12 +0000 2020"
        //     "id" => 1328469445010812928
        //     "id_str" => "1328469445010812928"
        //     "text" => """
        //       @salonkentarohar 人気って難しいですよね。\n
        //       \n
        //       サロンに関する卒論をしているので、人気なども扱おうか考えましたが、適切に算出する良い方法が出なかったので諦めまし
        // た笑\n
        //       \n
        //       なので、楽しみにしてます�🤤\n
        //       #zoomなどでゆっくり話してみたい
        //       """
        //     "truncated" => false
        //     "entities" => array:4 [
        //       "hashtags" => array:1 [
        //         0 => array:2 [
        //           "text" => "zoomなどでゆっくり話してみたい"
        //           "indices" => array:2 [
        //             0 => 105
        //             1 => 123
        //           ]
        //         ]
        //       ]
        //       "symbols" => []
        //       "user_mentions" => array:1 [
        //         0 => array:5 [
        //           "screen_name" => "salonkentarohar"
        //           "name" => "原田健太郎@多肉植物プログラマ（大阪府）"
        //           "id" => 1315175613351641088
        //           "id_str" => "1315175613351641088"
        //           "indices" => array:2 [
        //             0 => 0
        //             1 => 16
        //           ]
        //         ]
        //       ]
        //       "urls" => array:1 [
        //           0 => array:4 [
        //             "url" => "https://t.co/p6UujzrhU6"
        //             "expanded_url" => "http://pukubook.jp/"
        //             "display_url" => "pukubook.jp"
        //             "indices" => array:2 [
        //               0 => 10
        //               1 => 33
        //             ]
        //           ]
        //       "media" => array:1 [
        //           0 => array:10 [
        //             "id" => 1334435495745503235
        //             "id_str" => "1334435495745503235"
        //             "indices" => array:2 [
        //               0 => 77
        //               1 => 100
        //             ]
        //             "media_url" => "http://pbs.twimg.com/media/EoTeA2OVQAMDGLX.jpg"
        //             "media_url_https" => "https://pbs.twimg.com/media/EoTeA2OVQAMDGLX.jpg"
        //             "url" => "https://t.co/F2Aj8cZggv"
        //             "display_url" => "pic.twitter.com/F2Aj8cZggv"
        //             "expanded_url" => "https://twitter.com/salonkentarohar/status/1334435781163671557/photo/1"
        //             "type" => "photo"
        //             "sizes" => array:4 [
        //               "large" => array:3 [
        //                 "w" => 2000
        //                 "h" => 1500
        //                 "resize" => "fit"
        //               ]
        //               "thumb" => array:3 [
        //                 "w" => 150
        //                 "h" => 150
        //                 "resize" => "crop"
        //               ]
        //               "medium" => array:3 [
        //                 "w" => 1200
        //                 "h" => 900
        //                 "resize" => "fit"
        //               ]
        //               "small" => array:3 [
        //                 "w" => 680
        //                 "h" => 510
        //                 "resize" => "fit"
        //               ]
        //             ]
        //           ]
        //         ]
        //           ]
        //     "source" => "<a href="http://twitter.com/download/iphone" rel="nofollow">Twitter for iPhone</a>"
        //     "in_reply_to_status_id" => 1328140565553180672
        //     "in_reply_to_status_id_str" => "1328140565553180672"
        //     "in_reply_to_user_id" => 1315175613351641088
        //     "in_reply_to_user_id_str" => "1315175613351641088"
        //     "in_reply_to_screen_name" => "salonkentarohar"
        //     "user" => array:42 [
        //       "id" => 1259691897624248320
        //       "id_str" => "1259691897624248320"
        //       "name" => "平野翔斗(しゅうと)@埼玉"
        //       "screen_name" => "salonS_H"
        //       "location" => "日本 埼玉"
        //       "description" => "#西野亮廣エンタメ研究所 #埼玉 野球好きな大学4年 Voicyデータアナリスト 阪神 ボードゲーム 漫画好
        // き かき氷 都道府県別リスト作成(47+海外) https://t.co/WjKSvk19Ha←追加/削除はDMまで"
        //       "url" => "https://t.co/tak17AXdLs"
        //       "entities" => array:2 [
        //         "url" => array:1 [
        //           "urls" => array:1 [
        //             0 => array:4 [
        //               "url" => "https://t.co/tak17AXdLs"
        //               "expanded_url" => "https://twicall.net"
        //               "display_url" => "twicall.net"
        //               "indices" => array:2 [
        //                 0 => 0
        //                 1 => 23
        //               ]
        //             ]
        //           ]
        //         ]

        // 自分の発言と相手へのメンション
        $client->get('/statuses/user_timeline', [
            'user_id'   => $account->account_id, //"109830468", //,1260117637847150592,1259579647332716544",
            // 'screen_name' => null,
            // 'since_id' => null,
            // 'count' => null,
            // 'max_id' => null,
            'trim_user' => true,
            // 'exclude_replies' => null,
            // 'include_rts' => null,
        ]);
        $result = $client->getContentAs('json.array');
        dd($result);
        // [
        //     "created_at" => "Tue Dec 01 15:00:18 +0000 2020"
        //     "id" => 1333788014946836480
        //     "id_str" => "1333788014946836480"
        //     "text" => "@salonyukacinq @salontsuda 寒さもあるかもですが、急に陽に当てたから、というのもあるかもしれませんね。植
        // 物は急な環境変化は苦手です。室内で元気にしているなら日光浴は必要ありませんし、日光浴させるなら毎日欠かさずやるくらいの
        // ほうが良いかと思います���"
        //     "truncated" => false
        //     "entities" => array:4 [
        //       "hashtags" => []
        //       "symbols" => []
        //       "user_mentions" => array:2 [
        //         0 => array:5 [
        //           "screen_name" => "salonyukacinq"
        //           "name" => "宮下 ゆか@京都市伏見区"
        //           "id" => 1259734667386744833
        //           "id_str" => "1259734667386744833"
        //           "indices" => array:2 [
        //             0 => 0
        //             1 => 14
        //           ]
        //         ]
        //         1 => array:5 [
        //           "screen_name" => "salontsuda"
        //           "name" => "津田 好明"
        //           "id" => 3599180234
        //           "id_str" => "3599180234"
        //           "indices" => array:2 [
        //             0 => 15
        //             1 => 26
        //           ]
        //         ]
        //       ]
        //       "urls" => []
        //     ]
        //     "source" => "<a href="https://mobile.twitter.com" rel="nofollow">Twitter Web App</a>"
        //     "in_reply_to_status_id" => 1333782047735701505
        //     "in_reply_to_status_id_str" => "1333782047735701505"
        //     "in_reply_to_user_id" => 1259734667386744833
        //     "in_reply_to_user_id_str" => "1259734667386744833"
        //     "in_reply_to_screen_name" => "salonyukacinq"
        //     "retweet_count" => 0
        //     "favorite_count" => 2

        // $client->get('/followers/list', [
        //     'user_id' => "109830468", //,1260117637847150592,1259579647332716544",
        //     // screen_name,
        //     // 'cursor    '   => $account,
        //     // 'stringify_ids' => $this->argument('limit_sec'),
        //     // count
        // ]);
        // $result = $client->getContentAs('json.array');
        // dd($result);

        // $client->get('/users/search', [
        //     'q' => 'salon',
        //     // 'page' => 1,
        //     'count' => 20,
        //     'include_entities' => false,
        // ]);

        // $client->get('/friendships/outgoing', [
        // //     // 'cursor    '   => $account,
        // //     // 'stringify_ids' => $this->argument('limit_sec'),
        // ]);
        // $result = $client->getContentAs('json.array');

        // // dd($result);

        // $outgoing = $result['ids'];

        // $client->get('/friends/ids', [
        //     // user_id
        //     // screen_name
        //     // count
        //     // 'cursor    '   => $account,
        //     // 'stringify_ids' => $this->argument('limit_sec'),
        // ]);
        // $result = $client->getContentAs('json.array');

        // // dd($result);

        // $friends = $result['ids'];
        // dd(count($outgoing), count($friends), array_diff($outgoing, $friends));

        // $client->get('/users/lookup', [
        //     'user_id' => "{$outgoing[0]},{$outgoing[1]},{$outgoing[2]}",
        //     // screen_name
        //     // count
        //     // 'cursor    '   => $account,
        //     // 'stringify_ids' => $this->argument('limit_sec'),
        // ]);
        // $result = $client->getContentAs('json.array');
        // dd($result);

    } catch (\Throwable $e) {

        echo (string) $e;
    }
});

/**
 * テスト
 */
Artisan::command('app:test-twitter-api', function (
    \Kd9703\Resources\Interfaces\Account\Account $Account
) {

    $account = $Account->getOne(Media::TWITTER(), config('services.twitter.owner_twitter_id'));

    $twitter = new TwitterApi(
        $account->account_id,
        // 'RwYLhxGZpMqsWZENFVw',
        // 'Jk80YVGqc7Iz1IDEjCI6x3ExMSBnGjzBAH6qHcWJlo',
        // 'IQKbtAYlXLripLGPWd0HUA',
        // 'GgDYlkSvaPxGxC4X8liwpUoqKwwr3lCADbz8A7ADU',
        config('services.twitter.client_id'),
        config('services.twitter.client_secret'),
        $account->oauth_access_token,
        $account->oauth_access_secret->getPlainPassword()
    );

    $twitter->get('friendships/incoming', [
        // 'cursor' => 'アプリ',
        'stringify_ids' => false,
    ]);

    var_dump($twitter->getResponseStatusCode(), $twitter->getContentAs('json'));

    $incomming = $twitter->getContentAs('json');

    // $twitter->get('users/lookup', [
    //     // 'user_id' => implode(',', $incomming->ids),
    //     'user_id' => $incomming->ids[0],
    // ]);

    // var_dump($twitter->getResponseStatusCode(), $twitter->getContentAs('json'));

    $twitter->post('/friendships/accept.json', [
        'user_id'                           => $incomming->ids[0],
        'include_profile_interstitial_type' => 1,
        'include_blocking'                  => 1,
        'include_blocked_by'                => 1,
        'include_followed_by'               => 1,
        'include_want_retweets'             => 1,
        'include_mute_edge'                 => 1,
        'include_can_dm'                    => 1,
        'include_can_media_tag'             => 1,
        'skip_status'                       => 1,
        'cursor'                            => -1,
        // 'post_authenticity_token' => 'cbb345597daaa0d7b3cce63e4d3014818be75627',
    ]);
    // Lv.1 "Invalid or expired token."                              キーが無茶苦茶
    // Lv.2 "Not authorized to use this endpoint."                   認証されてない
    // Lv.3 "Your credentials do not allow access to this resource." 認証はされているがココでは使えない
    var_dump($twitter->getResponseStatusCode(), $twitter->getContentAs('json'));

    // $result = app(\Kd9703\Resources\Interfaces\Account\FollowingAccount::class)->getToBeUnfollow($account);
    // $result = app(\Kd9703\Resources\Interfaces\Follow\Following::class)->getToBeUnfollow($account);
    // $result = app(\Kd9703\Usecases\Interfaces\Crawl\UpdateStatusByNotices::class)(['account' => $account, 'limit_sec' => 10]);
    // $result = app(\Kd9703\Usecases\Interfaces\Analyze\DailyTotal::class)(['account' => $account]);

    // var_dump($result);
    // var_dump($result->count());
    // var_dump($result[0]->toArray());
});
