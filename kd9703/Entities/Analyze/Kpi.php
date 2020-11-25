<?php

namespace Kd9703\Entities\Analyze;

use Kd9703\Entities\Entity;

class Kpi extends Entity
{
    /**
     * @var array
     */
    protected $attritubes = [
        'date' => ['date:Y-m-d', null],

        // その日時点での（1日の最後に記録して以後更新しない）
        'accounts_total'             => ['int', null], // アカウント数
        'salon_accounts_total'       => ['int', null], // 確認サロンアカウント数
        'salon_accounts_active'      => ['int', null], // アクティブアカウント数
        'registered_accounts_total'  => ['int', null], // アプリ利用者数
        'registered_accounts_active' => ['int', null], // アプリアクティブ利用者数
        'rejected_accounts_total'    => ['int', null], // アプリ利用拒否者数
        'reviewed_accounts'          => ['int', null], // プロフィール更新数
        'created_accounts'           => ['int', null], // 新規登録数
        'started_accounts_2w'        => ['int', null], // 過去2週間に利用開始したアカウント
        'api_called_total'           => ['int', null], // TwitterAPI コール数
        'oldest_review_datetime'     => ['date:Y-m-d H:i:s', null], // 最後にレビューしたアカウントの日時

        // その日の（後日変わるものもあるし変わらないものもある）
    ];
}
