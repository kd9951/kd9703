<?php
namespace Kd9703\Usecases;

use Carbon\Carbon;
use Crawler\Support\Random;
use Crawler\Support\Timer;
use Kd9703\Constants\LoginMethod;
use Kd9703\Constants\Media;
use Kd9703\Entities\Media\Account As AccountEntity;
use Kd9703\Logger\Interfaces\OwnerLogger;
use Kd9703\Logger\Interfaces\SystemLogger;
use Kd9703\Resources\Interfaces\Account\Account as AccountAccount;
use Kd9703\Resources\Interfaces\Owner\Configuration;
use Kd9703\Resources\Interfaces\Transaction;
use Kd9703\Usecases\Usecase;

/**
 * 指定されたアカウントを「アプリユーザー」として登録する
 */
final class RegistAccount extends Usecase
{
    /**
     * 依存オブジェクトを受け取る
     */
    public function __construct(
        SetGlobalAccountRegulation $SetGlobalAccountRegulation,
        AccountAccount $Account,
        Configuration $Configuration,
        Transaction $Transaction,
        Random $random,
        Timer $timer,
        SystemLogger $systemLogger,
        OwnerLogger $ownerLogger
    ) {
        $this->usecases['SetGlobalAccountRegulation'] = $SetGlobalAccountRegulation;
        // $this->mediaAccesses['ExecFollow']           = $execFollow;
        $this->resources['Account']       = $Account;
        $this->resources['Configuration'] = $Configuration;
        $this->resources['Transaction']   = $Transaction;

        parent::__construct($random, $timer, $systemLogger, $ownerLogger);
    }

    /**
     * 実行
     */
    public function exec(
        $twitter_account_id, // "763553497337868288"
        $token, // "763553497337868288-VXs473q0KYOqt1LuqWTk5SL612lLb8t"
        $token_secret, // "64wn7h1vPxPygPPI4RzmR0pfsfKpOXfnphngY6qOCwevD"
        $username, // "pukubook"
        $nickname, // "pukubook"
        $user // その他付帯情報
    ): AccountEntity {
        $account = new AccountEntity([
            'media'               => Media::TWITTER,
            'account_id'          => $twitter_account_id,
            'username'            => $username,
            'fullname'            => $nickname,
            'login_method'        => LoginMethod::TWITTER(), // GLOVERがTwitterをクロールするための認証情報
            'login_id'            => null,
            'password'            => null,
            'oauth_access_token'  => $token,
            'oauth_access_secret' => $token_secret,
            'reviewed_at'         => Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        // その他付帯情報（情報があるときだけセット）
        foreach ([
            // 'prefecture'          => $user[''],
            'web_url1'         => $user['entities']['url']['urls'][0]['expanded_url'] ?? null,
            'web_url2'         => $user['entities']['url']['urls'][1]['expanded_url'] ?? null,
            'web_url3'         => $user['entities']['url']['urls'][2]['expanded_url'] ?? null,
            'img_thumnail_url' => $user['profile_image_url_https'] ?? null,
            'img_cover_url'    => $user['profile_background_image_url_https'] ?? null,
            'total_post'       => $user['statuses_count'] ?? null,
            'total_follow'     => $user['friends_count'] ?? null,
            'total_follower'   => $user['followers_count'] ?? null,
            'total_likes'      => $user['favourites_count'] ?? null,
            'last_posted_at'   => ($user['status']['created_at'] ?? null) ? date('Y-m-d H:i:s', strtotime($user['status']['created_at'])) : null,
            'is_private'       => $user['protected'] ?? null,
        ] as $key => $value) {
            if (!is_null($value)) {
                $account->$key = $value;
            }
        }

        $this->resources['Transaction']->beginTransaction();

        $account = ($this->usecases['SetGlobalAccountRegulation'])(['account' => $account]);

        $account = $this->resources['Account']->regist($account);

        // 設定を初期化
        $this->resources['Configuration']->create($account);

        $this->resources['Transaction']->commit();

        return $account;
    }

}
