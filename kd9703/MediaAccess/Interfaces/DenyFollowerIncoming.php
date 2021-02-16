<?php
namespace Kd9703\MediaAccess\Interfaces;

use Kd9703\Entities\Media\Account;
use Kd9703\Framework\StrictInvokator\StrictInvokatorInterface;

/**
 * フォローリクエストを削除
 */
interface DenyFollowerIncoming extends StrictInvokatorInterface
{
    /**
     * フォローリクエストを承認
     * @param  Account   $account
     * @param  string    $target_account_id
     * @return Account
     */
    public function exec(Account $account, string $target_account_id): ?Account;
}
