<?php
namespace Kd9703\MediaAccess\Twitter;

use Carbon\Carbon;
use Kd9703\Constants\Prefecture;
use Kd9703\Entities\Media\Account;
use Kd9703\Entities\Media\Accounts;
use Kd9703\MediaAccess\Interfaces\GetUsers as GetUsersInterface;

/**
 * 通知を取得
 */
class GetUsers extends MediaAccess implements GetUsersInterface
{
    const ENDPOINT_USER = '/users/lookup'; // :account_id

    /**
     * @param  Account $account
     * @param  Account $target_account
     * @return mixed
     */
    public function exec(Account $account, Accounts $target_accounts): Accounts
    {
        $this->wait->waitNormal('twitter.GetUsers', 500, 1500);

        $url = self::ENDPOINT_USER;
        $this->system_logger->mediaCall('GET', $url, [], [], $account);
        $this->client->get($url, ['user_id' => implode(',', $target_accounts->pluck('account_id'))]);
        $this->system_logger->mediaResponse('GET', $url, [], [], $this->client, $account);

        $response_json_array = $this->client->getContentAs('json.array');

        $formatted = $this->format($response_json_array);

        $now = Carbon::now()->format('Y-m-d H:i:s');
        foreach ($target_accounts as $target_account) {
            if (isset($formatted[$target_account->account_id])) {
                foreach ($formatted[$target_account->account_id] as $key => $value) {
                    if (!is_null($value)) {
                        $target_account->$key = $value;
                    }
                }
                $target_account->reviewed_at = $now;
            }
        }

        return $target_accounts;
    }

    /**
     * パターン抽出
     *
     * @return mixed
     */
    protected function format($response_json_array)
    {
        $formatted = [];

        foreach ($response_json_array as $user) {

            $account_id = $user['id'];

            $account = [
                'username'         => $user['screen_name'] ?? null,
                'fullname'         => $user['name'] ?? null,
                'description'      => $user['description'] ?? null,
                'web_url1'         => $user['url'] ?? null,
                'img_thumnail_url' => $user['profile_image_url_https'] ?? null,
                'img_cover_url'    => $user['profile_banner_url'] ?? null,
                'total_post'       => $user['statuses_count'] ?? null,
                'total_follow'     => $user['friends_count'] ?? null,
                'total_follower'   => $user['followers_count'] ?? null,
                'total_likes'      => $user['favourites_count'] ?? null,
                'last_posted_at'   => $user['last_posted_at'] ?? null,
                'is_private'       => $user['protected'] ?? null,
                'last_posted_at'   => $user['status']['created_at'] ?? null,
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

            // スコア計算 ロジックは(他でも使うなら)切り離したほうが良い
            $account['score'] = 0 + $account['total_follower'] + $account['total_likes'];

            $formatted[$account_id] = $account;
        }

        return $formatted;
    }

    /**
     * @param string $str
     */
    protected function guessPrefecture(string $name, string $description, string $location): ?int
    {
        // 推定の優先度 場所→名前(人名っぽいものは省いて)→紹介文
        return $this->seekPrefecture($location) ?:
            // 自己紹介文には「サッカーの香川選手が好きです」と書くかもしれないので
            ($this->seekPrefectureStrict($name) ?: $this->seekPrefectureStrict($description));
    }

    /**
     * @param string $str
     */
    protected function seekPrefecture(string $str): ?int
    {
        foreach (Prefecture::TEXT_JPN as $id => $name) {
            if (strpos($str, $name) !== false) {
                return $id;
            }
        }

        foreach (Prefecture::TEXT_EN as $id => $name) {
            if (strpos($str, $name) !== false) {
                return $id;
            }
        }

        foreach (Prefecture::TEXT_JPN_SHORT as $id => $name) {
            if (strpos($str, $name) !== false) {
                return $id;
            }
        }

        return null;
    }

    /**
     * 人名かもしれない都道府県名はマッチさせない
     * @param string $str
     */
    protected function seekPrefectureStrict(string $str): ?int
    {
        // 名字由来net調べ
        $strict_id = [
            3, //岩手さん    390人
            8, //茨城さん    340人
            // 9,  //栃木さん    4000人
            10, //群馬さん    40人
            11, //埼玉さん    30人
            13, //東京さん    10人
            14, //神奈川さん  190人
            15, //新潟さん    340人
            // 19, //山梨さん    5800人
            21, //岐阜さん    20人
            22, //静岡さん    210人
            23, //愛知さん    870人
            24, //三重さん    560人
            25, //滋賀さん    180人
            26, //京都さん     90人
            27, //大阪さん    1200人
            28, //兵庫さん    1800人
            30, //和歌山さん  240人
            31, //鳥取さん    1700人
            // 32, //島根さん    4900人
            // 34, //広島さん    6300人
            38, //愛媛さん    0人
            39, //高知さん    280人
            // 41, //佐賀さん    6500人
            44, //大分さん    30人
            46, //鹿児島さん  1000人
            47, //沖縄さん    0人
        ];
        foreach (Prefecture::TEXT_JPN as $id => $name) {
            if (strpos($str, $name) !== false) {
                return $id;
            }
        }

        foreach (Prefecture::TEXT_EN as $id => $name) {
            // osaka 大坂さんや小坂さんは多いので
            if ($id !== 27 && in_array($id, $strict_id) && strpos($str, $name) !== false) {
                return $id;
            }
        }

        foreach (Prefecture::TEXT_JPN_SHORT as $id => $name) {
            if (in_array($id, $strict_id) && strpos($str, $name) !== false) {
                return $id;
            }
        }

        return null;
    }
}
