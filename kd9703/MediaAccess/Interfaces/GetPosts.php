<?php
namespace Kd9703\MediaAccess\Interfaces;

use Kd9703\Entities\Media\Account;
use Kd9703\Entities\Media\Posts;
use Kd9703\Framework\StrictInvokator\StrictInvokatorInterface;

/**
 * 関連した投稿を取得
 */
interface GetPosts extends StrictInvokatorInterface
{
    public function exec(Account $account, int $limit): Posts;
}
