<?php
namespace Kd9703\Usecases;

use Kd9703\Entities\Media\Account;
use Kd9703\Usecases\Usecase;

/**
 * アカウントがサロンルールに準じているかを厳密にチェック
 */
abstract class SetGlobalAccountRegulation extends Usecase
{
    /**
     * 実行
     */
    abstract public function exec(Account $account): Account;
}
