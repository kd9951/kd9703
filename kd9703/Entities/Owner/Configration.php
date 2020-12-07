<?php

namespace Kd9703\Entities\Owner;

use Kd9703\Constants\ShowNew;
use Kd9703\Entities\Entity;

class Configration extends Entity
{
    /**
     * @var array
     */
    protected $attritubes = [
        // アプリからの利用を許可しない
        'hidden_from_auto_follow'              => ['bool', null], // 自動フォローする対象から除外する
        'hidden_from_search'                   => ['bool', null], // 検索対象から除外する（このアプリからはサロンアカウントとして存在しない扱い）

        // 表示設定
        'show_new_by'                          => ['integer', true], // 新着表示基準
        'show_new_days'                        => ['integer', ShowNew::BY_CREATED_AT], // 新着表示の日数

        // フォローするか
        'auto_follow'                          => ['bool', false], // 条件に従い自動フォローする
        // フォロー申請するかどうか
        'target_follow_per_day'                => ['integer', 100], // 1日のフォロー目標数　最大500
        'sleep_hour_start'                     => ['integer', 23], // 夜間はおやすみする　23時～6時
        'sleep_hour_end'                       => ['integer', 6], // 夜間はおやすみする　23時～6時
        'auto_follow_back_before_accept'       => ['integer', 0], // ┗まだフォロー承認していないアカウントもフォロバする
        'auto_follow_in_my_list'               => ['bool', false], // 保存したリストのメンバーを自動的にフォローする
        'auto_follow_target_list'              => ['bool', false], // ┗リストを保存した他のアカウントも自動的にフォローする
        'auto_follow_members'                  => ['bool', false], // サロン垢村名簿のアカウントをフォロー

        // フォロー申請前の確認事項
        'follow_only_official_regulation'      => ['bool', true], // 公式ルールに準拠したアカウントのみフォロー
        'follow_only_set_icon'                 => ['bool', false], // アイコンを設定しているアカウントのみフォロー
        'follow_only_tweets_more_than'         => ['integer', 0], // 最低ツイート数
        'follow_only_profile_contains'         => ['string', '#西野亮廣エンタメ研究所'], // プロフにこれらの言葉のどれかを含むアカウントのみフォロー（サロン垢ルール）

        'follow_only_keyword_contains_1'       => ['string', ''], // このキーワードセットに該当するアカウントのみフォローする
        'follow_only_keyword_contains_2'       => ['string', ''], // このキーワードセットに該当するアカウントのみフォローする
        'follow_only_keyword_contains_3'       => ['string', ''], // このキーワードセットに該当するアカウントのみフォローする

        'follow_again_in_days'                 => ['integer', 30], // フォローしなかったアカウントを再確認するまでの日数（ゼロは二度としない）

        // フォローされたらどうするか
        // フォロー申請されたらどうするか？フォローバックするか？
        'auto_follow_back'                     => ['bool', false], // 自分をフォローしたアカウントを自動的にフォロバする
        'auto_reject'                          => ['bool', false], // 自分をフォローしたアカウントでもルール外なら削除する
        // 'auto_accept_follow_request' => ['integer', 0], // 自分をフォローしたアカウントを自動的にフォロバする

        // すでにフォローした／フォローバックされた人が変化あったらどうするか？
        'check_follower_regulation'            => ['bool', false], // フォロワーがサロン垢ルールに従っているかチェックする
        'check_following_regulation'           => ['bool', false], // フォローしているアカウントがサロン垢ルールに従っているかチェックする

        // フォロワーのサロン垢ルールチェック
        'follow_back_only_official_regulation' => ['bool', true], // 公式ルールに準拠したアカウントのみフォローを承認
        'follow_back_only_set_icon'            => ['bool', false], // アイコンを設定しているアカウントのみフォロー
        'follow_back_only_tweets_more_than'    => ['integer', 0], // 最低ツイート数
        'follow_back_only_profile_contains'    => ['string', ''], // プロフにこれらの言葉のどれかを含むアカウントのみフォロー（サロン垢ルール）
    ];
}
