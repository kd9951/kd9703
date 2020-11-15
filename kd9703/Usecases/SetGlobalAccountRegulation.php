<?php
namespace Kd9703\Usecases;

use Crawler\Support\Random;
use Crawler\Support\Timer;
use Kd9703\Entities\Media\Account;
use Kd9703\Logger\Interfaces\OwnerLogger;
use Kd9703\Logger\Interfaces\SystemLogger;
use Kd9703\Usecases\Usecase;

/**
 * アカウントがサロンルールに準じているかを厳密にチェック
 * （ユーザーごとの裁量によらず公式ルールを適用）
 */
final class SetGlobalAccountRegulation extends Usecase
{
    /**
     * 依存オブジェクトを受け取る
     */
    public function __construct(
        Random $random,
        Timer $timer,
        SystemLogger $systemLogger,
        OwnerLogger $ownerLogger
    ) {
        parent::__construct($random, $timer, null, null);
    }

    /**
     * 実行
     */
    public function exec(Account $account): Account
    {
        $account->is_salon_account =
        // 鍵アカウントである
        $account->is_private
        // 名前が salon で始まっている
         && preg_match('/^salon.*/i', $account->username)
        // 本名
        ;

        return $account;
    }

}
