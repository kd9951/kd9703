<?php
namespace Kd9703\Resources\Interfaces\Tag;

use Kd9703\Entities\Media\Account;
use Kd9703\Entities\Media\Tags;

/**
 * アカウントがウォッチしているタグ
 */
interface WatchingTag
{
    /**
     * ウォッチタグを取得
     *
     * @param  Account $account
     * @return Tags
     */
    public function getList(Account $account): Tags;

    /**
     * ウォッチタグを追加
     *
     * @param  Account $account
     * @param  Tags    $tags
     * @return void
     */
    public function addList(Account $account, Tags $tags): void;

    /**
     * ウォッチタグを除去
     *
     * @param  Account $account
     * @param  Tags    $tags
     * @return void
     */
    public function removeList(Account $account, Tags $tags): void;
}
