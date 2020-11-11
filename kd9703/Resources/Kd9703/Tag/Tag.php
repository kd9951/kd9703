<?php
namespace Kd9703\Resources\Kd9703\Tag;

use Kd9703\Constants\Media;
use Kd9703\Entities\Media\Account;
use Kd9703\Entities\Media\Tag as TagEntity;
use Kd9703\Entities\Media\Tags;
use Kd9703\Resources\Kd9703\Tools\EloquentAdapter;
use Kd9703\Resources\Interfaces\Tag\Tag as TagInterface;

/**
 * タグ
 */
class Tag implements TagInterface
{
    use EloquentAdapter;

    const TAG_COLUMNS = [
        // 'media',
        'tag_id',
        'tag_body',
        'total_post',
        'total_follower',
    ];

    /**
     * シンプルに単一のタグ情報を永続化する
     *
     * @param  Account $account
     * @return void
     */
    public function getList(Media $media, array $tag_ids): Tags
    {
        $eloquent = $this->getEloquent($media, 'Tag');
        $tags     = $eloquent->whereIn('tag_id', $tag_ids)
            ->select(self::TAG_COLUMNS)->get()->toArray();

        foreach ($tags as $idx => $tag) {
            $tags[$idx]['media'] = $media;
        }

        return new Tags($tags);
    }

    /**
     * シンプルに単一のタグ情報を永続化する
     *
     * @param  Account $account
     * @return void
     */
    public function storeOne(TagEntity $tag): void
    {
        // TODO バリデーションルール タグの保存可能数とかも
        if (!isset($tag->tag_id)) {
            return;
        }

        $eloquent = $this->getEloquent($tag->media, 'Tag');

        $model = $eloquent->find($tag->tag_id);
        $model = $model ?: $eloquent;

        foreach (array_filter([
            'tag_id'         => $tag->tag_id,
            'tag_body'       => $tag->tag_body,
            'total_post'     => $tag->total_post,
            'total_follower' => $tag->total_follower,
        ], function ($v) {return !is_null($v);}) as $key => $value) {
            $model->$key = $value;
        }

        $model->save();
    }

    /**
     * 複数一括のタグ情報を永続化する
     *
     * @param  Account $account
     * @return void
     */
    public function storeList(Tags $tags): void
    {
        // FIXME ループしてたら一括する意味ないよね
        foreach ($tags as $tag) {
            $this->storeOne($tag);
        }
    }
}
