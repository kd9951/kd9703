<?php

namespace Kd9703\MediaAccess\Twitter\Tools;

use Kd9703\Constants\Media;
use Kd9703\Entities\Media\Account;
use Kd9703\Entities\Media\Post;

/**
 * Twitter API のDMオブジェクトを、Postエンティティ用にフォーマットする
 */
trait FormatDirectMessageObjectForPost
{
    public function formatDirectMessageObjectForPost(Account $account, array $dm): array
    {
        $post = [
            'media'      => Media::TWITTER(),
            'is_private' => true,

            // MEMO DMとTweetのIDはかぶってない、気がする
            // 'post_id'                => ($dm['id'] ?? null) ? 'dm' . $dm['id'] : null,
            'post_id'                => $dm['id'] ?? null,
            'account_id'             => $dm['message_create']['sender_id'] ?? null,
            'in_reply_to_account_id' => null,
            'in_reply_to_post_id'    => null,

            'title'            => '',
            'body'             => $dm['message_create']['message_data']['text'] ?? '',
            'img_thumnail_url' => null,
            'img_main_url'     => null,

            'count_liked'   => null,
            'count_comment' => null,
            'count_shared'  => null,
        ];

        // 宛先・メンション先
        if (isset($dm['message_create']['target']['recipient_id'])) {
            $post['in_reply_to_account_id'] = $dm['message_create']['target']['recipient_id'];
            $post['recipient_account_ids']  = [$dm['message_create']['target']['recipient_id']];
        }

        // URL
        if (!empty($post['recipient_account_ids'])) {
            // FIXME たまに受信者送信者のIDが逆のパターンがあって違いがわからない
            $post['url'] = "https://twitter.com/messages/{$post['recipient_account_ids'][0]}-{$post['account_id']}";
        }

        // 投稿日時
        if (isset($dm['created_timestamp'])) {
            $post['posted_at'] = date('Y-m-d H:i:s', $dm['created_timestamp'] / 1000);
        }

        // スコア計算 ロジックは(他でも使うなら)切り離したほうが良い
        $post['score'] = 0;

        $post = array_filter($post, function ($v) {return !is_null($v);});

        return $post;
    }
}
