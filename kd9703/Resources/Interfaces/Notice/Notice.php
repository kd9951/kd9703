<?php
namespace Kd9703\Resources\Interfaces\Notice;

use Kd9703\Constants\Media;
use Kd9703\Entities\Media\Account;
use Kd9703\Entities\Media\Notice as NoticeEntity;
use Kd9703\Entities\Media\Notices;

/**
 * 通知
 */
interface Notice
{
    /**
     * 最後の通知を取得
     *
     * @param  Media               $media
     * @param  array               $notice_ids
     * @return NoticeEntity|null
     */
    public function getLatest(Account $account): ?NoticeEntity;

    /**
     * シンプルに単一の通知情報を永続化する
     *
     * @param  Account $account
     * @return void
     */
    public function storeOne(NoticeEntity $notice): void;

    /**
     * 複数一括の通知情報を永続化する
     *
     * @param  Account $account
     * @return void
     */
    public function storeList(Notices $notices): void;
}
