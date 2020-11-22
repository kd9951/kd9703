<?php
namespace Kd9703\Resources\Interfaces\Account;

use Kd9703\Constants\Media;
use Kd9703\Entities\Media\Account as AccountEntity;
use Kd9703\Entities\Media\Accounts;
use Kd9703\Entities\Paginate\Input as PaginateInput;

/**
 * アカウント
 */
interface Account
{
    /**
     * @param int $account_id
     */
    public function getOne(?Media $media, string $account_id): ?AccountEntity;

    /**
     * @param int $account_id
     */
    public function getAllIds(?Media $media): array;

    /**
     * 最近更新されていないアカウント
     */
    public function getOlds(Media $media, int $limit): Accounts;

    /**
     * 注目のアカウント
     */
    public function getPops(Media $media, ?PaginateInput $paginateInput = null): Accounts;

    /**
     * 検索
     */
    public function search(Media $media, ?string $keyword = null, ?PaginateInput $paginateInput = null): Accounts;

    /**
     * ownerの自分のアカウントとしてを登録する
     * 事前に自身のアカウントであることを認証済であり
     * ログイン情報が含まれている必要がある
     *
     * @param  Account   $account
     * @return Account
     */
    public function regist(AccountEntity $account): AccountEntity;

    /**
     * シンプルに単一のアカウント情報を永続化する
     *
     * @param  Account $account
     * @return void
     */
    public function upsert(AccountEntity $account): void;
}
