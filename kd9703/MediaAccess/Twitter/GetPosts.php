<?php

namespace Kd9703\MediaAccess\Twitter;

use Carbon\Carbon;
use Kd9703\Entities\Media\Account;
use Kd9703\Entities\Media\Posts;
use Kd9703\Framework\StrictInvokator\Suspendable;
use Kd9703\MediaAccess\Interfaces\GetPosts as GetPostsContract;
use Kd9703\MediaAccess\Twitter\MediaAccess;

/**
 * 投稿を取得
 */
class GetPosts extends MediaAccess implements GetPostsContract, Suspendable
{
    use Tools\FormatTweetObjectForPost;
    use Tools\FormatDirectMessageObjectForPost;

    const ENDPOINT_DIRECT_MESSAGE = '/direct_messages/events/list';
    const ENDPOINT_MENTIONS       = '/statuses/mentions_timeline';
    const ENDPOINT_USER_TIMELINE  = '/statuses/user_timeline';
    const PER_PAGE                = 200;

    /**
     * @var mixed
     */
    protected $next_cursor = null;

    /**
     * execして結果が帰ってきた後に
     * 途中で止まったか？を確認する
     *
     * @return boolean
     */
    public function isSuspended(): bool
    {
        return $this->next_cursor ? true : false;

    }

    /**
     * 途中で止まっていた場合、再開用の文字列を返す
     *
     * @return string
     */
    public function getNextCursor(): string
    {
        return $this->next_cursor ?? '';
    }

    /**
     * 次回その文字列をセットしてからexecすると再開できる
     * （もちろんexecの引数は前回と同じ）
     *
     * @param  string $next_cursor
     * @return void
     */
    public function setNextCursor(string $next_cursor): void
    {
        $this->next_cursor = $next_cursor;
    }

    /**
     * アカウントに関連した投稿を取得する（自分の投稿、自分への投稿）
     *
     * @param  Account     $account
     * @param  integer     $limit          取得個数制限
     * @param  string|null $since_post_id  前回のポスト 指定するとこの続きを取得
     * @param  string|null $since_datetime 前回の最新ポスト日時 指定するとこの続きを取得（多少超えることがある）
     * @return Posts
     */
    public function exec(Account $account, int $limit, ?string $since_post_id = null, ?string $since_datetime = null): Posts
    {
        if ($since_post_id) {
            throw new \LogicException('could not use since_post_id. (could not determine witch timeline or directmessage)');
        }
        $posts_array = [];
        $now         = Carbon::now()->format('Y-m-d H:i:s');

        // タイムライン
        $max_id = null;
        while (true) {
            $json = $this->getTimeline($account, self::PER_PAGE, $since_post_id, $max_id);
            // max_id を指定すると1つ目はそれ自身を含むので削除
            if ($max_id && ($json[0]['id'] ?? null) == $max_id) {
                $json = array_slice($json, 1);
                $this->system_logger->debug('eliminated by max_id. got ' . count($json) . 'posts.');
            }
            // それ以上なければ終了
            if (empty($json)) {
                break;
            }
            foreach ($json as $src) {
                $last_post                = $this->formatTweetObjectForPost($account, $src);
                $last_post['reviewed_at'] = $now;
                $posts_array[]            = $last_post;
            }
            // 指定日付に達したら終了
            if ($since_datetime && $last_post['posted_at'] <= $since_datetime) {
                break;
            }
            $max_id = $last_post['post_id'];
        }

        // メンション
        $max_id = null;
        while (true) {
            $json = $this->getMentions($account, self::PER_PAGE, $since_post_id, $max_id);
            // max_id を指定すると1つ目はそれ自身を含むので削除
            if ($max_id && ($json[0]['id'] ?? null) == $max_id) {
                $json = array_slice($json, 1);
                $this->system_logger->debug('eliminated by max_id. got ' . count($json) . ' posts.');
            }
            // それ以上なければ終了
            if (empty($json)) {
                break;
            }
            foreach ($json as $src) {
                $last_post                = $this->formatTweetObjectForPost($account, $src);
                $last_post['reviewed_at'] = $now;
                $posts_array[]            = $last_post;
            }
            // 指定日付に達したら終了
            if ($since_datetime && $last_post['posted_at'] <= $since_datetime) {
                break;
            }
            $max_id = $last_post['post_id'];
        }

        // ダイレクトメッセージ
        $next_cursor = null;
        while (true) {
            $json = $this->getDirectMessages($account, $next_cursor);

            // それ以上なければ終了
            if (empty($json['events'])) {
                break;
            }
            foreach ($json['events'] as $src) {
                $last_post                = $this->formatDirectMessageObjectForPost($account, $src);
                $last_post['reviewed_at'] = $now;
                $posts_array[]            = $last_post;
            }
            // 指定日付に達したら終了
            if ($since_datetime && $last_post['posted_at'] <= $since_datetime) {
                break;
            }
            $next_cursor = $json['next_cursor'];
        }

        $this->system_logger->debug('got total ' . count($posts_array) . ' as posts.');

        return new Posts($posts_array);
    }

    /**
     * @param  int     $page
     * @return array
     */
    private function getTimeline(Account $account, int $count, ?string $since_id = null, ?string $max_id = null): array
    {
        // ユーザーごとに 15分で       900回(1秒1回)
        // アプリ全体で   15分で     1,500回
        //                24時間で 100,000回
        $this->wait->waitNormal('twitter.getTimeline', 900, 1100);

        $url   = self::ENDPOINT_USER_TIMELINE;
        $param = array_filter([
            'user_id'   => $account->account_id,
            // 'screen_name' => null,
            'since_id'  => $since_id,
            'count'     => $count,
            'max_id'    => $max_id,
            'trim_user' => true, /* 自分なので要らない */
            // 'exclude_replies' => null,
            // 'include_rts' => null,
        ]);

        $this->system_logger->mediaCall('GET', $url, $param, [], $account);
        $this->client->get($url, $param);
        $this->system_logger->mediaResponse('GET', $url, $param, [], $this->client, $account);

        $json = $this->client->getContentAs('json.array');
        $this->system_logger->debug('got ' . count($json) . 'posts.');

        if (!is_array($json)) {
            $this->system_logger->warning('getTimeline failed. could not get valid data.');
            return [];
        }

        return $json;
    }

    // リプライ先がない
    // account_id がtokenのあるアカウントだったら in_reply_to_account_id のタイムラインは取れるはず
    // SELECT
    // child.account_id,
    // child.in_reply_to_account_id
    // FROM `posts` child
    // LEFT JOIN posts parent ON child.in_reply_to_post_id = parent.post_id
    // WHERE child.in_reply_to_post_id IS NOT NULL AND parent.post_id IS NULL
    // group by child.account_id,
    // child.in_reply_to_account_id

    // 自分とメンションやりあった人
    // SELECT
    //     account_id,
    //     MAX(posted_at) last_posted_at
    // FROM
    //     (SELECT
    //         to_account_id account_id,
    //         posted_at
    //     FROM post_recipients
    //     WHERE from_account_id = 1259724960664174592
    // UNION ALL
    //     SELECT
    //         from_account_id account_id,
    //         posted_at
    //     FROM post_recipients
    //     WHERE to_account_id = 1259724960664174592
    //     ) SUB
    // GROUP BY account_id
    // ORDER BY last_posted_at DESC

    // ORDER by last_posted_at desc

    /**
     * @param  int     $page
     * @return array
     */
    private function getMentions(Account $account, int $count, ?string $since_id = null, ?string $max_id = null): array
    {
        // ユーザーごとに 15分で       900回(1秒1回)
        // アプリ全体で   15分で     1,500回
        //                24時間で 100,000回
        $this->wait->waitNormal('twitter.getMentions', 900, 1100);

        $url   = self::ENDPOINT_MENTIONS;
        $param = array_filter([
            'user_id'          => $account->account_id,
            'since_id'         => $since_id,
            'count'            => $count,
            'max_id'           => $max_id,
            'trim_user'        => false,
            'include_entities' => true,
        ]);

        $this->system_logger->mediaCall('GET', $url, $param, [], $account);
        $this->client->get($url, $param);
        $this->system_logger->mediaResponse('GET', $url, $param, [], $this->client, $account);

        $json = $this->client->getContentAs('json.array');
        $this->system_logger->debug('got ' . count($json) . 'posts.');

        if (!is_array($json)) {
            $this->system_logger->warning('getMentions failed. could not get valid data.');
            return [];
        }

        return $json;
    }

    /**
     * @param  int   $page
     * @return array [events, next_cursor]
     */
    private function getDirectMessages(Account $account, ?string $next_cursor = null): array
    {
        // ユーザーごとに 15分で 15回？
        $this->wait->waitNormal('twitter.getDirectMessages', 900, 1100);

        $url   = self::ENDPOINT_DIRECT_MESSAGE;
        $param = array_filter([
            'count'  => 50, // Max number of events to be returned. 20 default. 50 max.
            'cursor' => $next_cursor,
        ]);

        $this->system_logger->mediaCall('GET', $url, $param, [], $account);
        $this->client->get($url, $param);
        $this->system_logger->mediaResponse('GET', $url, $param, [], $this->client, $account);

        $json = $this->client->getContentAs('json.array');

        if ($this->client->getResponseStatusCode() == 429) {
            $this->system_logger->notice('Rate limit exceeded. Counld not get enuogh events.');
            // $this->owner_logger->notice('DMの取得制限に達しました。過去のDMはTwitter公式アプリでご確認ください。');
            return [
                'events'      => [],
                'next_cursor' => null,
            ];
        }

        if (!is_array($json)) {
            $this->system_logger->warning('getDirectMessages failed. could not get valid data.');
            return [
                'events'      => [],
                'next_cursor' => null,
            ];
        }

        $json['events']      = $json['events'] ?? [];
        $json['next_cursor'] = $json['next_cursor'] ?? null;
        $this->system_logger->debug('got ' . count($json['events']) . 'events.');

        return $json;
        // {
        //   "next_cursor": "AB345dkfC",
        //   "events": [
        //     { "id": "110", "created_timestamp": "5300", ... },
        //     { "id": "109", "created_timestamp": "5200", ... },
        //     ...
        //   ]
        // }
    }

}
