<?php
namespace Kd9703\MediaAccess\Interfaces;

use Kd9703\Entities\Media\Account;
use Kd9703\Framework\StrictInvokator\StrictInvokatorInterface;

/**
 * フォローリクエストを承認
 */
interface AcceptFollowerIncoming extends StrictInvokatorInterface
{
    /**
     * フォローリクエストを承認
     *
     * MEMO たまたまTwitterは結果としてユーザーオブジェクトを返すが、
     * ユーザー内容は予め調査済だし、他のメディアがユーザーを返すかもわからないので、
     * 可否だけで良いとは思う
     *
     * @param  Account   $account
     * @param  string    $target_account_id
     * @return Account
     */
    public function exec(Account $account, string $target_account_id): ?Account;
}
