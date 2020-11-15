<?php
namespace Kd9703\MediaAccess\Interfaces;

use Kd9703\Entities\Media\Account;
use Kd9703\Entities\Media\Accounts;
use Kd9703\Framework\StrictInvokator\StrictInvokatorInterface;

/**
 * ユーザー詳細を取得
 */
interface GetUsers extends StrictInvokatorInterface
{
    public function exec(Account $account, Accounts $target_accounts): Accounts;
}
