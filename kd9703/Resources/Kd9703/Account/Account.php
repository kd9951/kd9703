<?php
namespace Kd9703\Resources\Kd9703\Account;

use Kd9703\Constants\Media;
use Kd9703\Entities\Media\Account as AccountEntity;
use Kd9703\Entities\Media\Accounts;
use Kd9703\Entities\Owner\Owner;
use Kd9703\Entities\Paginate\Input as PaginateInput;
use Kd9703\Entities\Paginate\Output as PaginateOutput;
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
        'last_posted_at',
        'total_likes',
        'reviewed_at',
        'is_private',
        'is_salon_account',
    ];

    /**
     * @param Owner $owner
     */
    public function getOne(?Media $media, string $account_id): ?AccountEntity
    {
        $eloquent = $this->getEloquent($media, 'Account');
        $account  = $eloquent
            ->select(array_merge(self::COLS_REQUIRED, self::COLS_OPTION))
            ->where('account_id', $account_id)->first();

        if (!$account) {
            return null;
        }
        $account          = $account->toArray();
        $account['media'] = $media;

        return new AccountEntity($account);
    }

    /**
     * 最近更新されていないアカウント
     */
    public function getOlds(Media $media, int $limit): Accounts
    {
        $eloquent = $this->getEloquent($media, 'Account');
        $accounts = $eloquent
            ->select(array_merge(self::COLS_REQUIRED, self::COLS_OPTION))
            ->orderBy('reviewed_at', 'asc')
            ->take($limit)
            ->get()->toArray();

        foreach ($accounts as $idx => $account) {
            $accounts[$idx]['media'] = $media;
        }

        return new Accounts($accounts);
    }

    /**
     * 人気のアカウント
     */
    public function getPops(Media $media, ?PaginateInput $paginateInput = null): Accounts
    {
        return $this->search($media, '', $paginateInput);
    }

    /**
     * 検索
     */
    public function search(Media $media, ?string $keyword = null, ?PaginateInput $paginateInput = null): Accounts
    {
        $eloquent = $this->getEloquent($media, 'Account');
        $eloquent = $eloquent
            ->select(array_merge(self::COLS_REQUIRED, self::COLS_OPTION))
            ->where(function($q) {
                $q->whereNull('hidden_from_search');
                $q->orWhere('hidden_from_search', false);
            })
            ->where('is_salon_account', true)
            ->orderBy('score', 'desc');

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

        foreach ($accounts as $idx => $account) {
            $accounts[$idx]['media'] = $media;
        }

        return (new Accounts($accounts))->withPaginate($paginateOutput);
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
