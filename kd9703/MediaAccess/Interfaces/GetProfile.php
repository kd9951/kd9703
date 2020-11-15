<?php
namespace Kd9703\MediaAccess\Interfaces;

use Kd9703\Entities\Media\Account;
use Kd9703\Framework\StrictInvokator\StrictInvokatorInterface;

/**
 * 初回認証を実行
 * Accountの詳細を取得
 * 入力の $target_account には account_id しかない想定で、それ以外の情報をできるだけ埋める
 */
interface GetProfile extends StrictInvokatorInterface
{
    public function exec(Account $account, Account $target_account): Account;
}
