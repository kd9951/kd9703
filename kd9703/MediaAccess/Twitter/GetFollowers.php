<?php

namespace Kd9703\MediaAccess\Twitter;

use Carbon\Carbon;
use Kd9703\Constants\Media;
use Kd9703\Entities\Media\Account;
use Kd9703\Entities\Media\Follows;
use Kd9703\Framework\StrictInvokator\Suspendable;
use Kd9703\MediaAccess\Interfaces\GetFollowers as GetFollowersContract;
use Kd9703\MediaAccess\Twitter\MediaAccess;

/**
 * フォロワを取得
 */
class GetFollowers extends MediaAccess implements GetFollowersContract, Suspendable
{
    use Tools\FormatUserObjectForAccount;

    const ENDPOINT_FOLLOWERS    = '/followers/list';
    const ENDPOINT_FOLLOWER_IDS = '/followers/ids';
    const ENDPOINT_USER         = '/users/lookup';
    const PER_PAGE              = 20;

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
     * Undocumented function
     *
     * @param  Account   $account
     * @param  integer   $limit
     * @param  array     $exclude_account_ids 除外アカウントを指定するとその差分だけ取得できる
     * @return Follows
     */
    public function exec(Account $account, int $limit, array $exclude_account_ids = []): Follows
    {
        $all_twitter_users = [];

        // 前回からの続きがあれば...
        $candidates = $this->fetchFromNextCursor($this->next_cursor);
        if (empty($candidates) && !empty($exclude_account_ids)) {
            // 除外IDが指定されていれば、今のフォロワーとの差分を候補にする
            $candidates = $this->getCandidates($account, $exclude_account_ids);

            if(empty($candidates)) {
                $this->system_logger->warning('Failed to get candidates.');

                return new Follows([]);
            }
        }

        if (!empty($candidates)) {
            // ユーザーID指定モード
            while (true) {
                $sub   = array_splice($candidates, 0, self::PER_PAGE);
                $users = $this->getUsers($account, $sub);

                $this->next_cursor = $this->makeNextCursor($candidates);

                $all_twitter_users = array_merge($all_twitter_users, $users);

                if (count($all_twitter_users) >= $limit) {
                    break;
                }

                // 終了条件 件数不足
                if (empty($candidates) || count($users) < self::PER_PAGE) {
                    $this->next_cursor = null;
                    break;
                }
            }

        } else {
            // 全フォロワー検索モード
            while (true) {
                $users = $this->getFollows($account);

                $all_twitter_users = array_merge($all_twitter_users, $users);

                if (count($all_twitter_users) >= $limit) {
                    break;
                }

                // 終了条件 件数不足
                if (count($users) < self::PER_PAGE) {
                    $this->next_cursor = null;
                    break;
                }
            }
        }

        $formatted = $this->format($all_twitter_users, $account);
        $followers = new Follows($formatted);

        return $followers;
    }

    /**
     * @param  int     $page
     * @return array
     */
    public function getFollows(Account $account): array
    {
        $this->wait->waitNormal('twitter.getFollows', 0, 0);

        $url   = self::ENDPOINT_FOLLOWERS;
        $param = [
            'user_id' => $account->account_id,
            // screen_name,
            'cursor'  => $this->next_cursor,
            // 'stringify_ids' => false,
            'count'   => self::PER_PAGE,
        ];
        $this->system_logger->mediaCall('GET', $url, $param, [], $account);
        $this->client->get($url, $param);
        $this->system_logger->mediaResponse('GET', $url, $param, [], $this->client, $account);

        $json = $this->client->getContentAs('json.array');

        if (! isset($json['users'])) {
            $this->system_logger->warning('getFollows failed. Skip this time.');

            return [];
        }

        $this->next_cursor = $json['next_cursor_str'] ?? null;

        return $json['users'];
    }

    /**
     * 全フォロワーから除外分を除いた未知のアカウントのIDだけを返す
     * IDだけで検索することで効率化を図ったもの
     *
     * @param  int     $page
     * @return array
     */
    public function getCandidates(Account $account, array $exclude_account_ids): array
    {
        $account_ids = [];
        $next_cursor = null;

        // 最高4回まで 20,000件
        foreach (range(1, 4) as $try) {
            $this->wait->waitNormal('twitter.getFollowIds', 0, 0);

            $url   = self::ENDPOINT_FOLLOWER_IDS;
            $param = [
                'user_id' => $account->account_id,
                // screen_name,
                'cursor'  => $next_cursor,
                // 'stringify_ids' => false,
                // 'count'   => self::PER_PAGE,
            ];
            $this->system_logger->mediaCall('GET', $url, $param, [], $account);
            $this->client->get($url, $param);
            $this->system_logger->mediaResponse('GET', $url, $param, [], $this->client, $account);

            $json = $this->client->getContentAs('json.array');

            if (!is_array($json['ids'])) {
                $this->system_logger->warning('getCandidates failed. could not get enough data.');
                break;
            }

            $account_ids = array_merge($account_ids, $json['ids']);

            $next_cursor = $json['next_cursor_str'] ?? null;

            if (!$next_cursor) {
                break;
            }
        }

        return array_values(array_diff($account_ids, $exclude_account_ids));
    }

    /**
     * @param array $candidates
     */
    private function makeNextCursor(array $candidates): ?string
    {
        return empty($candidates) ? null : "CANDIDATES:" . implode(',', $candidates);
    }

    /**
     * @param string $next_cursor
     */
    private function fetchFromNextCursor(?string $next_cursor): array
    {
        return strpos($next_cursor, 'CANDIDATES:') !== 0 ? [] : explode(',', substr($next_cursor, 11));
    }

    /**
     * @param  int     $page
     * @return array
     */
    public function getUsers(Account $account, array $candidates): array
    {
        if (empty($candidates)) {
            return [];
        }

        $this->wait->waitNormal('twitter.getUsers', 0, 0);

        $url   = self::ENDPOINT_USER;
        $param = ['user_id' => implode(',', $candidates)];
        $this->system_logger->mediaCall('GET', $url, $param, [], $account);
        $this->client->get($url, $param);
        $this->system_logger->mediaResponse('GET', $url, $param, [], $this->client, $account);

        $json = $this->client->getContentAs('json.array');

        if (!is_array($json)) {
            $this->system_logger->warning('getUsers failed. could not get enough data.');
            return [];
        }

        return $json;
    }

    /**
     * 抽出したパターンモデルに合わせる
     *
     * @return array
     */
    protected function format(array $users, Account $account): array
    {
        $afters = [];
        $now = Carbon::now()->format('Y-m-d H:i:s');

        foreach ($users as $user) {
            $from_account = $this->formatUserObjectForAccount($user);
            $from_account['reviewed_at'] = $now;

            // 変換パターン
            $after = [
                'media' => Media::TWITTER(),

                'to_account_id' => $account->account_id,
                'to_account'    => $account,

                'from_account_id' => $from_account['account_id'],
                'from_account'    => $from_account,

                // 'followed_back_at'     => Carbon::now(),
                // 'followed_back_type'   => null,
            ];

            $afters[] = $after;
        }

        return $afters;
    }

}
