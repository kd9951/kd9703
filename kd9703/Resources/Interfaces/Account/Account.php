<?php
namespace Kd9703\Resources\Interfaces\Account;

use Kd9703\Constants\Media;
use Kd9703\Entities\Media\Account as AccountEntity;
use Kd9703\Entities\Media\Accounts;
use Kd9703\Entities\Paginate\Input as PaginateInput;
use Kd9703\Entities\Sort\Inputs as SortInputs;

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
     * @param  Media|null           $media
     * @param  string               $username
     * @return AccountEntity|null
     */
    public function getByUsername(?Media $media, string $username): ?AccountEntity;

    /**
     * @param int $account_id
     */
    public function getAllIds(?Media $media): array;

    /**
     * 指定されたIDのうち存在しないものをアカウントエンティティとして取得
     */
    public function getNotExists(Media $mediam, array $account_ids): Accounts;

    /**
     * 利用中アカウントのうち、最後に詳細情報を取得したアカウント
     */
    public function getUsingAccountToBeUpdatedNext(Media $media, int $limit): Accounts;

    /**
     * 最近更新されていないアカウント
     */
    public function getOlds(Media $media, int $limit): Accounts;

    /**
     * 注目のアカウント
     */
    public function getPops(Media $media, ?PaginateInput $paginateInput = null): Accounts;

    /**
     * 最近コミュニケーションとったアカウント
     */
    public function getCommunicatingAccounts(AccountEntity $account, ?string $username_partial = null, ?PaginateInput $paginateInput = null, ?SortInputs $sortInputs = null): Accounts;

    /**
     * 検索
     */
    public function search(Media $media, ?string $keyword = null, ?PaginateInput $paginateInput = null, ?SortInputs $sortInput = null): Accounts;

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
