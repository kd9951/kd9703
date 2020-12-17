<?php
namespace Kd9703\Resources\Interfaces\Post;

use Kd9703\Entities\Media\Account;
use Kd9703\Entities\Media\Post as PostEntity;
use Kd9703\Entities\Media\Posts;
use Kd9703\Entities\Paginate\Input as PaginateInput;
use Kd9703\Entities\Sort\Inputs as SortInputs;

/**
 * 投稿
 */
interface Post
{
    /**
     * 最新の投稿
     *
     * @param  Account           $account
     * @return PostEntity|null
     */
    public function getLatest(Account $account): ?PostEntity;

    /**
     * 他のアカウントとコミュニケーションしている投稿
     *
     * @param  Account            $account
     * @param  string|null        $target_account_id
     * @param  string|null        $username_partial
     * @param  string|null        $keyword
     * @param  PaginateInput|null $paginateInput
     * @param  SortInputs|null    $sortInputs
     * @return Posts
     */
    public function getCommunications(Account $account, ?string $target_account_id = null, ?string $target_username = null,  ?string $username_partial = null, ?string $keyword = null, ?PaginateInput $paginateInput = null, ?SortInputs $sortInputs = null): Posts;

    /**
     * @param Account           $account
     * @param string            $keyword
     * @param nullPaginateInput $paginateInput
     * @param nullSortInputs    $sortInputs
     */
    public function search(Account $account, ?string $keyword = null, ?PaginateInput $paginateInput = null, ?SortInputs $sortInputs = null): Posts;

    /**
     * シンプルに単一の投稿を永続化する
     *
     * @param PostEntity $post
     */
    public function store(PostEntity $post): void;

    /**
     * 複数の投稿を一括で永続化する
     *
     * @param Posts $posts
     */
    public function storeMany(Posts $posts): void;

}
