<?php

namespace Kd9703\MediaAccess\Twitter\Tools;

use Carbon\Carbon;
use Kd9703\Entities\Media\Account;

/**
 * Twitter API のユーザーオブジェクトを、Accountエンティティ用にフォーマットする
 */
trait FormatUserObjectForAccount
{
    public function formatUserObjectForAccount(array $user): array
    {
        $account = [
            'account_id'       => $user['id'] ?? null,
            'username'         => $user['screen_name'] ?? null,
            'fullname'         => $user['name'] ?? null,
            'location'         => $user['location'] ?? null,
            'description'      => $user['description'] ?? null,
            'web_url1'         => $user['url'] ?? null,
            'img_thumnail_url' => $user['profile_image_url_https'] ?? null,
            'img_cover_url'    => $user['profile_banner_url'] ?? null,
            'total_post'       => $user['statuses_count'] ?? null,
            'total_follow'     => $user['friends_count'] ?? null,
            'total_follower'   => $user['followers_count'] ?? null,
            'total_likes'      => $user['favourites_count'] ?? null,
            'total_listed'     => $user['listed_count'] ?? null,
            'is_private'       => $user['protected'] ?? null,
        ];

        // 都道府県（推定）
        $account['prefecture'] = $this->guessPrefecture(
            $user['name'] ?? '',
            $user['description'] ?? '',
            $user['location'] ?? ''
        );

        // 最後の投稿日時
        if (isset($user['status']['created_at'])) {
            $account['last_posted_at'] = Carbon::parse($user['status']['created_at'])->format('Y-m-d H:i:s');
        }
        if (isset($user['created_at'])) {
            $account['started_at'] = Carbon::parse($user['created_at'])->format('Y-m-d H:i:s');
        }

        // スコア計算 ロジックは(他でも使うなら)切り離したほうが良い
        $account['score'] = 0 +
            $account['total_follower']
             - $account['total_follow'] / 2
             + $account['total_listed'] * 100
        ;

        $account = array_filter($account, function ($v) {return !is_null($v);});

        return $account;
    }

}
