<?php
namespace Kd9703\Usecases\Progress;

use Crawler\Support\Random;
use Crawler\Support\Timer;
use Kd9703\Entities\Media\Account;
use Kd9703\Logger\Interfaces\OwnerLogger;
use Kd9703\Logger\Interfaces\SystemLogger;
use Kd9703\Usecases\SetGlobalAccountRegulation as BaseSetGlobalAccountRegulation;

/**
 * アカウントがサロンルールに準じているかを厳密にチェック
 * （ユーザーごとの裁量によらず公式ルールを適用）
 */
final class SetGlobalAccountRegulation extends BaseSetGlobalAccountRegulation
{
    const PATTERNS = [
        'PROGRESS',
        // 50アカウントの実例から抽出したよくあるスペルミスパターン
        'PRGRESS',
        'PROFRESS',
        'PROGESS',
        'PROGGRES',
        'PROGLESS',
        'PROGPESS',
        'PROGREES',
        'PROGRES',
        'PROGRSS',
        'PROGSESS',
    ];

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
        $pattern = implode('|', self::PATTERNS);

        $account->is_salon_account =
        // 鍵アカウントである
        $account->is_private
        // 名前が progress で始まっている
         && preg_match("/^($pattern).*/i", $account->username)
        // 本名
        // 「#中田敦彦オンラインサロン」のタグがある
        ;

        return $account;
    }

}
