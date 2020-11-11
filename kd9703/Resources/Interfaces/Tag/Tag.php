<?php
namespace Kd9703\Resources\Interfaces\Tag;

use Kd9703\Constants\Media;
use Kd9703\Entities\Media\Account;
use Kd9703\Entities\Media\Tag as TagEntity;
use Kd9703\Entities\Media\Tags;

/**
 * タグ
 */
interface Tag
{
    /**
     * タグを取得する
     *
     * @param  Media  $media
     * @param  array  $tag_ids
     * @return Tags
     */
    public function getList(Media $media, array $tag_ids): Tags;

    /**
     * シンプルに単一のタグ情報を永続化する
     *
     * @param  Account $account
     * @return void
     */
    public function storeOne(TagEntity $tag): void;

    /**
     * 複数一括のタグ情報を永続化する
     *
     * @param  Account $account
     * @return void
     */
    public function storeList(Tags $tags): void;
}
