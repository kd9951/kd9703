<?php
namespace Kd9703\Resources\Kd9703\Account;

use Kd9703\Constants\Media;
use Kd9703\Entities\Media\Account as AccountEntity;
use Kd9703\Entities\Owner\Owner;
use Kd9703\Resources\Interfaces\Account\Account as AccountInterface;
use Kd9703\Resources\Kd9703\Tools\EloquentAdapter;

/**
 * アカウント
 */
class Account implements AccountInterface
{
    use EloquentAdapter;

    /**
     * @param Owner $owner
     */
    public function getOne(?Media $media, string $account_id): ?AccountEntity
    {
        $eloquent = $this->getEloquent($media, 'Account');
        $account  = $eloquent->select([
            'account_id',
            'username',
            'fullname',
            'login_method',
            'login_id',
            'password',
            'prefecture',
            'web_url1',
            'web_url2',
            'web_url3',
            'img_thumnail_url',
            'img_cover_url',
            'score',
            'total_post',
            'total_follow',
            'total_follower',
            'total_likes',
            'last_posted_at',
            'reviewed_at',
            'is_private',
            'is_salon_account',
            'hidden_from_auto_follow',
            'hidden_from_search',
        ])->where('account_id', $account_id)->first();

        if (!$account) {
            return null;
        }
        $account          = $account->toArray();
        $account['media'] = $media;

        return new AccountEntity($account);
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

        $data = array_merge([
            'account_id'   => $account->account_id,
            'login_method' => $account->login_method,
        ], array_filter([
            'login_id'            => $account->login_id,
            'password'            => $account->password,
            'oauth_access_token'  => $account->login_id,
            'oauth_access_secret' => $account->password,
            'username'            => $account->username,
            'fullname'            => $account->fullname,
            'prefecture'          => $account->prefecture,
            'web_url1'            => $account->web_url1,
            'web_url2'            => $account->web_url2,
            'web_url3'            => $account->web_url3,
            'img_thumnail_url'    => $account->img_thumnail_url,
            'img_cover_url'       => $account->img_cover_url,
            'score'               => $account->score,
            'total_post'          => $account->total_post,
            'total_follow'        => $account->total_follow,
            'total_follower'      => $account->total_follower,
            'total_likes'         => $account->total_likes,
            'last_posted_at'      => $account->last_posted_at,
            'reviewed_at'         => $account->reviewed_at,
            'is_private'          => $account->is_private,
        ]));
        foreach ($data as $key => $value) {
            $model->$key = $value;
        }
        $model->save();

        return new AccountEntity([
            'media'                   => Media::TWITTER,
            'account_id'              => $model->account_id,
            'username'                => $model->username,
            'fullname'                => $model->fullname,
            'login_method'            => $model->login_method,
            'login_id'                => $model->login_id,
            'password'                => $model->password,
            'prefecture'              => $model->prefecture,
            'web_url1'                => $model->web_url1,
            'web_url2'                => $model->web_url2,
            'web_url3'                => $model->web_url3,
            'img_thumnail_url'        => $model->img_thumnail_url,
            'img_cover_url'           => $model->img_cover_url,
            'score'                   => $model->score,
            'total_post'              => $model->total_post,
            'total_follow'            => $model->total_follow,
            'total_follower'          => $model->total_follower,
            'total_likes'             => $model->total_likes,
            'last_posted_at'          => $model->last_posted_at,
            'reviewed_at'             => $model->reviewed_at,
            'is_salon_account'        => $model->is_salon_account,
            'hidden_from_auto_follow' => $model->hidden_from_auto_follow,
            'hidden_from_search'      => $model->hidden_from_search,
            'is_private'              => $model->is_private,
        ]);
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

        $eloquent = $this->getEloquent(Media::TWITTER(), 'Account');

        $model = $eloquent->find($account->account_id);
        $model = $model ?: $eloquent;

        foreach (array_filter([
            'account_id'              => $account->account_id,
            'username'                => $account->username,
            'fullname'                => $account->fullname,
            'prefecture'              => $account->prefecture,
            'web_url1'                => $account->web_url1,
            'web_url2'                => $account->web_url2,
            'web_url3'                => $account->web_url3,
            'img_thumnail_url'        => $account->img_thumnail_url,
            'img_cover_url'           => $account->img_cover_url,
            'score'                   => $account->score,
            'total_post'              => $account->total_post,
            'total_follow'            => $account->total_follow,
            'total_follower'          => $account->total_follower,
            'total_likes'             => $account->total_likes,
            'last_posted_at'          => $account->last_posted_at,
            'reviewed_at'             => $account->reviewed_at,
            'is_private'              => $account->is_private,
            'is_salon_account'        => $account->is_salon_account,
            'hidden_from_auto_follow' => $account->hidden_from_auto_follow,
            'hidden_from_search'      => $account->hidden_from_search,
        ], function ($v) {return !is_null($v);}) as $key => $value) {
            $model->$key = $value;
        }

        $model->save();
    }
}
