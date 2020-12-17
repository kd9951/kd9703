<?php
namespace Kd9703\Resources\Kd9703\Post;

use Illuminate\Support\Facades\DB;
use Kd9703\Constants\Media;
use Kd9703\Entities\Media\Account;
use Kd9703\Entities\Media\Post as PostEntity;
use Kd9703\Entities\Media\Posts;
use Kd9703\Entities\Paginate\Input as PaginateInput;
use Kd9703\Entities\Paginate\Output as PaginateOutput;
use Kd9703\Entities\Sort\Inputs as SortInputs;
use Kd9703\Resources\Interfaces\Account\Account as AccountResource;
use Kd9703\Resources\Interfaces\Post\Post as PostInterface;
use Kd9703\Resources\Kd9703\Tools\EloquentAdapter;
use Kd9703\Resources\Kd9703\Tools\SearchKeyword;

/**
 * 投稿
 */
class Post implements PostInterface
{
    use EloquentAdapter;
    use SearchKeyword;

    const COLS_REQUIRED = [
        'post_id',
        'is_private',
        'account_id',
    ];

    const COLS_OPTION = [
        'in_reply_to_account_id',
        'in_reply_to_post_id',
        'url',
        'title',
        'body',
        'img_thumnail_url',
        'img_main_url',
        'score',
        'count_liked',
        'count_comment',
        'count_shared',
        'posted_at',
        'reviewed_at',
    ];

    /**
     * 依存オブジェクトを受け取る
     */
    public function __construct(
        AccountResource $Account
    ) {
        $this->resources['Account'] = $Account;
    }

    /**
     * 単純にPostのEloquentから取ったデータに、Entityに必要なデータを追加して詰める
     *
     * @param Account $account
     * @param array   $posts
     */
    private function packPost(Account $account, array $posts): Posts
    {
        $eloquent = $this->getEloquent($account->media, 'PostRecipient');

        // メンション先のアカウントID
        $recipient_account_ids = $eloquent->query()
            ->select('post_id', 'to_account_id')
            ->whereIn('post_id', array_column($posts, 'post_id'))
            ->get();

        $recipient_account_ids_by_post_id = [];
        foreach ($recipient_account_ids as $item) {
            $recipient_account_ids_by_post_id[$item->post_id][] = $item->to_account_id;
        }

        // リプライ元、先のアカウント（ここで追加するのはリッチすぎるかもしれない）
        $account_ids    = array_unique(array_merge(array_column($posts, 'account_id'), array_column($posts, 'in_reply_to_account_id')));
        $accounts_by_id = [];
        foreach ($account_ids as $id) {
            if ($id) {
                $accounts_by_id[$id] = $this->resources['Account']->getOne($account->media, $id);
            }
        }

        foreach ($posts as $idx => $post) {
            $posts[$idx]['media']                 = $account->media;
            $posts[$idx]['account']               = $accounts_by_id[$post['account_id']];
            $posts[$idx]['in_reply_to_account']   = $post['in_reply_to_account_id'] ? $accounts_by_id[$post['in_reply_to_account_id']] : null;
            $posts[$idx]['recipient_account_ids'] = $recipient_account_ids_by_post_id[$post['post_id']] ?? [];
        }

        return new Posts($posts);
    }

    ////////////////////////////////////////////////////////////////////////////////

    /**
     *
     */
    public function getLatest(Account $account): ?PostEntity
    {
        $eloquent = $this->getEloquent($account->media, 'Post');

        $post = $eloquent
            ->select(array_merge(self::COLS_REQUIRED, self::COLS_OPTION))
            ->where('account_id', $account->account_id)
            ->orderBy('posted_at', 'desc')
            ->first();

        if (!$post) {
            return null;
        }

        $post = $post->toArray();
        return $this->packPost($account, [$post])[0] ?? null;
    }

    /**
     * 他のアカウントとコミュニケーションしている投稿
     */
    public function getCommunications(
        Account $account,
        ?string $target_account_id = null,
        ?string $target_username = null,
        ?string $username_partial = null,
        ?string $keyword = null,
        ?PaginateInput $paginateInput = null,
        ?SortInputs $sortInputs = null
    ): Posts {
        // 最近のコミュニケーションポスト
        $eloquent = $this->getEloquent($account->media, 'PostRecipient');
        $tableMain = $eloquent->getTable();
        $query = $eloquent->select("$tableMain.post_id", DB::raw("MAX($tableMain.posted_at) as posted_at"));

        // ターゲットアカウント検索
        if ($target_account_id || $target_username || $username_partial) {
            $accountEloquent = $this->getEloquent($account->media, 'Account');
            $queryAccount           = $accountEloquent->select('account_id');
            if ($target_account_id) {
                $target_account_ids = [$target_account_id];
            } elseif ($target_username) {
                $queryAccount->where('username', $target_username);
                $target_account_id = $queryAccount->first()->account_id ?? null; 
                $target_account_ids = $target_account_id ? [$target_account_id] : [];
            } elseif ($username_partial) {
                $this->searchKeyword($queryAccount, $username_partial ?? '', ['username', 'fullname']);
                $target_account_ids = $queryAccount->pluck('account_id')->toArray();
            }
            // dd($target_account_ids, $target_account_id);
            unset($queryAccount, $target_account_id);

            $target_account_ids = array_diff($target_account_ids ?? [], [$account->account_id]);
            if (empty($target_account_ids)) {
                return (new Posts([]));
            }
            // ターゲットあり
            $query = $query->where(function ($q) use ($account, $target_account_ids) {
                $q->whereIn('to_account_id', $target_account_ids);
                $q->where('from_account_id', $account->account_id);
                $q->orWhere('to_account_id', $account->account_id);
                $q->whereIn('from_account_id', $target_account_ids);
            });

        } else {
            // ターゲットなし（自分以外すべて）
            $query = $query->where(function ($q) use ($account) {
                $q->where('to_account_id', '<>', $account->account_id);
                $q->where('from_account_id', $account->account_id);
                $q->orWhere('to_account_id', $account->account_id);
                $q->where('from_account_id', '<>', $account->account_id);
            });
        }

        // ポスト本文キーワード検索
        if ($keyword) {
            $eloquentPost = $this->getEloquent($account->media, 'Post');
            $tablePost = $eloquentPost->getTable();

            $query = $query->join("$tablePost as sub_p", 'sub_p.post_id', '=', "$tableMain.post_id");

            $query = $this->searchKeyword($query, $keyword ?? '', ["sub_p.body"]);
        }

        $query = $query->groupBy("$tableMain.post_id");
        $query = $query->orderBy('posted_at', 'desc');
        // dd($query->toSql(), $query->getBindings());

        // ソート
        if ($sortInputs && $sortInputs->count()) {
            $applied = [];
            foreach ($sortInputs as $sortInput) {
                $key = strtolower($sortInput->key);
                if (!isset($applied[$key])) {
                    $query->orderBy($key, $sortInput->order);
                    $applied[$key] = true;
                }
            }
            unset($applied);
        } else {
            // デフォルトソート
            $query->orderBy('posted_at', 'desc');
        }

        // ページネーション
        if ($paginateInput && $paginateInput->per_page) {
            $collection     = $query->paginate($paginateInput->per_page, ['*'], 'page', $paginateInput->page);
            $paginateOutput = new PaginateOutput($collection);
            $post_ids       = array_column($collection->toArray()['data'] ?? [], 'post_id');

        } else {
            $accounts       = $query->get();
            $paginateOutput = new PaginateOutput($accounts);
            $post_ids       = array_column($accounts->toArray(), 'post_id');
        }

        // 結果がない 早期リターン
        if (empty($post_ids)) {
            return (new Posts([]))->withPaginate($paginateOutput);
        }

        $eloquent = $this->getEloquent($account->media, 'Post');
        $eloquent = $eloquent
            ->select(array_merge(self::COLS_REQUIRED, self::COLS_OPTION))
            ->whereIn('post_id', $post_ids);
        $posts_by_id = array_column($eloquent->get()->toArray(), null, 'post_id');

        // 最初の post_ids の順序に直す（SQLでやるよりカンタンなので）
        $posts = [];
        foreach ($post_ids as $post_id) {
            $posts[] = $posts_by_id[$post_id];
        }

        return $this->packPost($account, $posts)->withPaginate($paginateOutput);
    }

    /**
     * 検索
     */
    public function search(Account $account, ?string $keyword = null, ?PaginateInput $paginateInput = null, ?SortInputs $sortInputs = null): Posts
    {
    }

    /**
     * シンプルに単一の投稿を永続化する
     *
     * @param  Post   $post
     * @return void
     */
    public function store(PostEntity $post): void
    {
        // TODO バリデーションルール
        if (!isset($post->account_id)) {
            return;
        }

        $eloquent = $this->getEloquent($post->media, 'Post');

        $model = $eloquent->find($post->post_id);
        $model = $model ?: $eloquent;

        foreach (array_merge(self::COLS_REQUIRED, self::COLS_OPTION) as $key) {
            if (!is_null($post->$key)) {
                $model->$key = $post->$key;
            }
        }

        $model->save();

        throw new \Exception('not implemented.');
        // $eloquent->whereIn('post_id', $post_ids)->delete();

        // $inserts = [];
        // foreach($posts as $post) {
        //     $post_id = $post->post_id;
        //     foreach($post->recipient_account_ids as $account_id) {
        //         $inserts = [
        //             'post_id' => $post_id,
        //             'account_id' => $account_id,
        //         ];
        //     }
        // }

        // $eloquent->insert($inserts);

    }

    /**
     * シンプルに単一の投稿を永続化する
     *
     * @param  Post   $post
     * @return void
     */
    public function storeMany(Posts $posts): void
    {
        // TODO バリデーションルール
        if ($posts->count() == 0) {
            return;
        }

        $eloquent = $this->getEloquent($posts[0]->media, 'Post');

        foreach ($posts as $post) {
            $model = $eloquent->find($post->post_id);
            $model = $model ?: new $eloquent();

            foreach (array_merge(self::COLS_REQUIRED, self::COLS_OPTION) as $key) {
                if (!is_null($post->$key)) {
                    $model->$key = $post->$key;
                }
            }

            $model->save();
        }

        $post_ids = $posts->pluck('post_id');

        $eloquent = $this->getEloquent($post->media, 'PostRecipient');
        $eloquent->whereIn('post_id', $post_ids)->delete();

        $inserts = [];
        foreach ($posts as $post) {
            if (empty($post->recipient_account_ids)) {
                continue;
            }
            foreach ($post->recipient_account_ids as $account_id) {
                $inserts[] = [
                    'post_id'         => $post->post_id,
                    'from_account_id' => $post->account_id,
                    'to_account_id'   => $account_id,
                    'posted_at'       => $post->posted_at,
                ];
            }
        }

        $eloquent->insert($inserts);
    }

}
