<?php
namespace Kd9703\Resources\Kd9703\Account;

use Illuminate\Support\Facades\DB;
use Kd9703\Constants\Media;
use Kd9703\Entities\Media\Account as AccountEntity;
use Kd9703\Entities\Media\Accounts;
use Kd9703\Entities\Owner\Owner;
use Kd9703\Entities\Paginate\Input as PaginateInput;
use Kd9703\Entities\Paginate\Output as PaginateOutput;
use Kd9703\Entities\Sort\Inputs as SortInputs;
use Kd9703\Resources\Interfaces\Account\Account as AccountInterface;
use Kd9703\Resources\Kd9703\Tools\EloquentAdapter;
use Kd9703\Resources\Kd9703\Tools\SearchKeyword;

/**
 * アカウント
 */
class Account implements AccountInterface
{
    use EloquentAdapter;
    use SearchKeyword;

    const COLS_REQUIRED = [
        'account_id',
        'login_method',
    ];

    const COLS_OPTION = [
        'username',
        'fullname',
        'login_id',
        'password',
        'oauth_access_token',
        'oauth_access_secret',
        'last_logged_in_at',
        'location',
        'prefecture',
        'description',
        'web_url1',
        'web_url2',
        'web_url3',
        'img_thumnail_url',
        'img_cover_url',
        'score',
        'total_post',
        'total_follow',
        'total_follower',
        'total_listed',
        'last_posted_at',
        'started_at',
        'total_likes',
        'reviewed_at',
        'reviewed_as_using_user_at',
        'status_updated_at',
        'is_private',
        'is_salon_account',
    ];

    const COLS_READONLY = [
        'created_at',
    ];

    /**
     * 単純にPostのEloquentから取ったデータに、Entityに必要なデータを追加して詰める
     *
     * @param Account $account
     * @param array   $posts
     */
    private function packAccount(Media $media, array $accounts): Accounts
    {
        foreach ($accounts as $idx => $account) {
            $accounts[$idx]['media']      = $media;
            $accounts[$idx]['created_at'] = date('Y-m-d H:i:s', strtotime($accounts[$idx]['created_at']));
        }

        return new Accounts($accounts);
    }

    ////////////////////////////////////////////////////////////////////////////////
    /**
     * @param Owner $owner
     */
    public function getOne(?Media $media, string $account_id): ?AccountEntity
    {
        $eloquent = $this->getEloquent($media, 'Account');
        $account  = $eloquent
            ->select(array_merge(self::COLS_REQUIRED, self::COLS_OPTION, self::COLS_READONLY))
            ->where('account_id', $account_id)->first();

        if (!$account) {
            return null;
        }
        $account = $account->toArray();

        return $this->packAccount($media, [$account])[0];
    }

    /**
     * @param Owner $owner
     */
    public function getByUsername(?Media $media, string $username): ?AccountEntity
    {
        $eloquent = $this->getEloquent($media, 'Account');
        $account  = $eloquent
            ->select(array_merge(self::COLS_REQUIRED, self::COLS_OPTION, self::COLS_READONLY))
            ->where('username', $username)->first();

        if (!$account) {
            return null;
        }
        $account = $account->toArray();

        return $this->packAccount($media, [$account])[0];
    }


    /**
     * @param int $account_id
     */
    public function getAllIds(?Media $media): array
    {
        $eloquent    = $this->getEloquent($media, 'Account');
        $account_ids = $eloquent->pluck('account_id')->toArray();

        return $account_ids;
    }

    /**
     * 指定されたIDのうち存在しないものをアカウントエンティティとして取得
     */
    public function getNotExists(Media $media, array $account_ids): Accounts
    {
        $eloquent             = $this->getEloquent($media, 'Account');
        $existing_account_ids = $eloquent
            ->whereIn('account_id', $account_ids)
            ->pluck('account_id')->toArray();

        $not_exists = array_diff($account_ids, $existing_account_ids);

        $accounts = [];
        foreach ($not_exists as $account_id) {
            $accounts[] = [
                'media'      => $media,
                'account_id' => $account_id,
            ];
        }

        return new Accounts($accounts);
    }

    /**
     * 利用中アカウントのうち、一番最後に詳細情報を取得したアカウント
     */
    public function getUsingAccountToBeUpdatedNext(Media $media, int $limit): Accounts
    {
        $eloquent = $this->getEloquent($media, 'Account');
        $accounts = $eloquent
            ->select(array_merge(self::COLS_REQUIRED, self::COLS_OPTION, self::COLS_READONLY))
            ->orderBy('reviewed_as_using_user_at', 'asc')
            ->whereNotNull('oauth_access_token')
            ->where('is_salon_account', true)
            ->take($limit)
            ->get()->toArray();

        return $this->packAccount($media, $accounts);
    }

    /**
     * 最近更新されていないアカウント
     */
    public function getOlds(Media $media, int $limit): Accounts
    {
        $eloquent = $this->getEloquent($media, 'Account');
        $accounts = $eloquent
            ->select(array_merge(self::COLS_REQUIRED, self::COLS_OPTION, self::COLS_READONLY))
            ->orderBy('reviewed_at', 'asc')
            ->take($limit)
            ->get()->toArray();

        return $this->packAccount($media, $accounts);
    }

    /**
     * 注目のアカウント
     */
    public function getPops(Media $media, ?PaginateInput $paginateInput = null): Accounts
    {
        return $this->search($media, '', $paginateInput);
    }

    /**
     * コミュニケーションとったアカウント
     */
    public function getCommunicatingAccounts(AccountEntity $account, ?string $username_partial = null, ?PaginateInput $paginateInput = null, ?SortInputs $sortInputs = null): Accounts
    {
        // 最近コミュニケーションしたアカウント
        $eloquent = $this->getEloquent($account->media, 'PostRecipient');
        $query    = $eloquent->select([
            DB::raw('IF(from_account_id = ' . $account->account_id . ', to_account_id, from_account_id) account_id'),
            DB::raw('MAX(posted_at) posted_at'),
            DB::raw('COUNT(posted_at) count'),
        ]);
        $query = $query->where(function ($q) use ($account) {
            $q->where('to_account_id', '<>', $account->account_id);
            $q->where('from_account_id', $account->account_id);
            $q->orWhere('to_account_id', $account->account_id);
            $q->where('from_account_id', '<>', $account->account_id);
        });
        $query = $query->groupBy('account_id');

        // SELECT IF(from_account_id = 1315175613351641088, to_account_id, from_account_id) account_id, max(posted_at) posted_at
        // from_account_id = 1315175613351641088 AND to_account_id <> 1315175613351641088
        // OR
        // to_account_id = 1315175613351641088 AND from_account_id <> 1315175613351641088
        // group by account_id
        // order by posted_at DESC

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
            $account_ids       = array_column($collection->toArray()['data'] ?? [], 'account_id');

        } else {
            $accounts       = $query->get();
            $paginateOutput = new PaginateOutput($accounts);
            $account_ids    = array_column($accounts->toArray(), 'account_id');
        }

        // 結果がない 早期リターン
        if (empty($account_ids)) {
            return (new Accounts([]))->withPaginate($paginateOutput);
        }

        $eloquent = $this->getEloquent($account->media, 'Account');
        $eloquent = $eloquent
            ->select(array_merge(self::COLS_REQUIRED, self::COLS_OPTION, self::COLS_READONLY))
            ->whereIn('account_id', $account_ids)
            ->whereNotNull('username');
        /*->where('is_salon_account', true)*/;

        $accounts_by_id = array_column($eloquent->get()->toArray(), null, 'account_id');

        // 最初の post_ids の順序に直す（SQLでやるよりカンタンなので）
        $accounts = [];
        foreach ($account_ids as $account_id) {
            if ($accounts_by_id[$account_id] ?? null) {
                $accounts[] = $accounts_by_id[$account_id];
            }
        }

        return $this->packAccount($account->media, $accounts);
    }

    /**
     * 検索
     */
    public function search(Media $media, ?string $keyword = null, ?PaginateInput $paginateInput = null, ?SortInputs $sortInputs = null): Accounts
    {
        $eloquent = $this->getEloquent($media, 'Account');
        $eloquent = $eloquent
            ->select(array_merge(self::COLS_REQUIRED, self::COLS_OPTION, self::COLS_READONLY))
            ->where(function ($q) {
                $q->whereNull('hidden_from_search');
                $q->orWhere('hidden_from_search', false);
            })
            ->where('is_salon_account', true);

        // ソート
        if ($sortInputs && $sortInputs->count()) {
            $applied = [];
            foreach ($sortInputs as $sortInput) {
                $key = strtolower($sortInput->key);
                if (!isset($applied[$key])) {
                    $eloquent->orderBy($key, $sortInput->order);
                    $applied[$key] = true;
                }
            }
            unset($applied);
        } else {
            // デフォルトソート
            $eloquent->orderBy('score', 'desc');
        }

        // キーワード検索
        $eloquent = $this->searchKeyword($eloquent, $keyword ?? '', [
            'username',
            'fullname',
            'description',
        ]);

        if ($paginateInput && $paginateInput->per_page) {
            $collection     = $eloquent->paginate($paginateInput->per_page, ['*'], 'page', $paginateInput->page);
            $paginateOutput = new PaginateOutput($collection);
            $accounts       = $collection->toArray()['data'];

        } else {
            $accounts       = $eloquent->get();
            $paginateOutput = new PaginateOutput($accounts);
            $accounts       = $accounts->toArray();
        }

        return $this->packAccount($media, $accounts)->withPaginate($paginateOutput);
    }

    /**
     * ownerの自分のアカウントとしてを登録する
     * 事前に自身のアカウントであることを認証済であり
     * ログイン情報が含まれている必要がある
     *
     * @param  Account   $account
     * @return Account
     */
    public function regist(AccountEntity $account): AccountEntity
    {
        // TODO バリデーションルール
        if (!isset($account->account_id)) {
            throw new \LogicException('バリデーションエラー');
        }

        $eloquent = $this->getEloquent($account->media, 'Account');

        $model = $eloquent->find($account->account_id);
        $model = $model ?: $eloquent;

        foreach (self::COLS_REQUIRED as $key) {
            $model->$key = $account->$key;
        }
        foreach (self::COLS_OPTION as $key) {
            if (!is_null($account->$key)) {
                $model->$key = $account->$key;
            }
        }
        $model->save();

        return $account;
    }

    /**
     * シンプルに単一のアカウント情報を永続化する
     * クローリングして見つかった他人のアカウントを保存するためのもの
     * 誰でも更新できるが所有権やログイン情報はプロテクトされている
     *
     * @param  Account $account
     * @return void
     */
    public function upsert(AccountEntity $account): void
    {
        // TODO バリデーションルール
        if (!isset($account->account_id)) {
            return;
        }

        $eloquent = $this->getEloquent($account->media, 'Account');

        $model = $eloquent->find($account->account_id);
        $model = $model ?: $eloquent;

        foreach (array_merge(self::COLS_REQUIRED, self::COLS_OPTION) as $key) {
            if (!is_null($account->$key)) {
                $model->$key = $account->$key;
            }
        }

        $model->save();
    }
}
