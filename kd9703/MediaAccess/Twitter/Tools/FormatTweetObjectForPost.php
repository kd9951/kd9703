<?php

namespace Kd9703\MediaAccess\Twitter\Tools;

use Carbon\Carbon;
use Kd9703\Constants\Media;
use Kd9703\Entities\Media\Account;
use Kd9703\Entities\Media\Post;

/**
 * Twitter API のTweetオブジェクトを、Postエンティティ用にフォーマットする
 */
trait FormatTweetObjectForPost
{
    public function formatTweetObjectForPost(Account $account, array $tweet): array
    {
        $post = [
            'media'      => Media::TWITTER(),
            'is_private' => false,

            'post_id'                => $tweet['id'] ?? null,
            'account_id'             => $tweet['user']['id'] ?? null,
            'in_reply_to_account_id' => $tweet['in_reply_to_user_id'] ?? null,
            'in_reply_to_post_id'    => $tweet['in_reply_to_status_id'] ?? null,

            'title'            => '',
            'body'             => $tweet['text'] ?? '',
            'img_thumnail_url' => $tweet['entities']['media']['media_url_https'] ?? null,
            'img_main_url'     => $tweet['entities']['media']['media_url_https'] ?? null,

            'count_liked'   => $tweet['favorite_count'] ?? null,
            'count_comment' => null,
            'count_shared'  => $tweet['retweet_count'] ?? null,
        ];

        // URL
        if (isset($tweet['user']['id'])) {
            $screenname = ($account->account_id == $tweet['user']['id']) ? $account->username : $tweet['user']['screen_name'];

            $post['url'] = "https://twitter.com/{$screenname}/status/{$tweet['id']}";
        }

        // 宛先・メンション先
        // リプライ元のユーザーはメンション先にも含まれているけど、全部そうなってるかわからないので確実に含むようにする
        $recipient_account_ids = array_column($tweet['entities']['user_mentions'], 'id');
        if (($tweet['in_reply_to_user_id'] ?? null) && !in_array($tweet['in_reply_to_user_id'], $recipient_account_ids)) {
            array_unshift($recipient_account_ids, $tweet['in_reply_to_user_id']);
        }
        if (!empty($recipient_account_ids)) {
            $post['recipient_account_ids'] = $recipient_account_ids;
        }

        // 投稿日時
        if (isset($tweet['created_at'])) {
            $post['posted_at'] = Carbon::parse($tweet['created_at'])
                ->setTimezone(date_default_timezone_get())
                ->format('Y-m-d H:i:s');
        }

        // スコア計算 ロジックは(他でも使うなら)切り離したほうが良い
        $post['score'] = 0 +
            $post['count_liked']
             + $post['count_comment'] * 5
             + $post['count_shared'] * 10
        ;

        $post = array_filter($post, function ($v) {return !is_null($v);});

        return $post;
    }
}
