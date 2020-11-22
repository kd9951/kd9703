<?php
namespace Kd9703\MediaAccess\Interfaces;

use Kd9703\Entities\Media\Account;
use Kd9703\Entities\Media\Follows;
use Kd9703\Framework\StrictInvokator\StrictInvokatorInterface;

/**
 * フォロワを取得
 */
interface GetFollowers extends StrictInvokatorInterface
{
    public function exec(Account $account, int $limit, array $exclude_account_ids = []): Follows;
}
