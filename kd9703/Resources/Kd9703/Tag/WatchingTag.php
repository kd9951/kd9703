<?php
namespace Kd9703\Resources\Kd9703\Tag;

use Kd9703\Entities\Media\Account;
use Kd9703\Entities\Media\Tags;
use Kd9703\Resources\Kd9703\Tools\EloquentAdapter;
use Kd9703\Resources\Interfaces\Tag\Tag as TagResource;
use Kd9703\Resources\Interfaces\Tag\WatchingTag as WatchingTagInterface;

/**
 * アカウントがウォッチしているタグ
 */
class WatchingTag implements WatchingTagInterface
{
    use EloquentAdapter;

    /**
     * @param Follow $follow
     */
    public function __construct(TagResource $tag)
    {
        $this->Resources['Tag'] = $tag;
    }

    /**
     * ウォッチタグを取得
     *
     * @param  Account $account
     * @return Tags
     */
    public function getList(Account $account): Tags
    {
        $eloquent = $this->getEloquent($account->media, 'AccountTag');
        $tag_ids  = $eloquent->where('account_id', $account->account_id)->pluck('tag_id')->toArray();

        return $this->Resources['Tag']->getList($account->media, $tag_ids);
    }

    /**
     * ウォッチタグを追加
     *
     * @param  Account $account
     * @param  Tags    $tags
     * @return void
     */
    public function addList(Account $account, Tags $tags): void
    {
        $eloquent = $this->getEloquent($account->media, 'AccountTag');

        $existing_tag_ids = $eloquent->where('account_id', $account->account_id)->pluck('tag_id')->toArray();

        $this->Resources['Tag']->storeList($tags);

        foreach ($tags as $tag) {
            if (in_array($tag->tag_id, $existing_tag_ids)) {
                continue;
            }

            $eloquent->create([
                'account_id' => $account->account_id,
                'tag_id'     => $tag->tag_id,
            ]);
        }
    }

    /**
     * ウォッチタグを除去
     *
     * @param  Account $account
     * @param  Tags    $tags
     * @return void
     */
    public function removeList(Account $account, Tags $tags): void
    {
        $tag_ids = array_column($tags->toArray(), 'tag_id');

        $eloquent = $this->getEloquent($account->media, 'AccountTag');
        $eloquent->where('account_id', $account->account_id)->whereIn('tag_id', $tag_ids)->delete();
    }
}
