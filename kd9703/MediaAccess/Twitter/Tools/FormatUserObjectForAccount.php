<?php

namespace Kd9703\MediaAccess\Twitter\Tools;

use Carbon\Carbon;
use Kd9703\Constants\Media;
use Kd9703\Constants\Prefecture;
use Kd9703\Entities\Media\Account;

/**
 * Twitter API のユーザーオブジェクトを、Accountエンティティ用にフォーマットする
 */
trait FormatUserObjectForAccount
{
    public function formatUserObjectForAccount(array $user): array
    {
        $account = [
            'media'            => Media::TWITTER(),
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
            $account['last_posted_at'] = Carbon::parse($user['status']['created_at'])
                ->setTimezone(date_default_timezone_get())
                ->format('Y-m-d H:i:s');
        }
        if (isset($user['created_at'])) {
            $account['started_at'] = Carbon::parse($user['created_at'])
                ->setTimezone(date_default_timezone_get())
                ->format('Y-m-d H:i:s');
        }

        // スコア計算 ロジックは(他でも使うなら)切り離したほうが良い
        $account['score'] = 0
         + $account['total_follower']
         - $account['total_follow'] * 0.6
         + $account['total_post'] * 0.2
         + $account['total_listed'] * 180
         + ($account['total_listed'] / max($account['total_follower'], 1000) * 500000) // リスト率
        ;
        // 開始からの日数で割る
        $started_at_nishino = '2020-05-10 20:23:07';
        $diff               = time() - max(strtotime($account['started_at'] ?? 'now'), strtotime($started_at_nishino));
        $days               = max(14, $diff / 60 / 60 / 24 / 10);
        $account['score']   = floor($account['score'] / $days);

        $account = array_filter($account, function ($v) {return !is_null($v);});

        return $account;
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
