<?php
namespace App\Providers\UserProviders;

use App\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider as BaseUserProvider;
use Kd9703\Constants\Media;
use Kd9703\Resources\Interfaces\Account\Account as AccountResource;

class UserProvider implements BaseUserProvider
{
    /**
     * @param AccountResource $AccountResource
     */
    public function __construct(AccountResource $AccountResource)
    {
        $this->AccountResource = $AccountResource;
    }

    /**
     * 与えられた credentials からユーザーのインスタンスを探す
     * Socialログインしか無いので使用しない
     *
     * @param  array                                             $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        throw new \LogicException('not implemented');

        // $account = $this->AccountResource->retrieveByCredentials($credentials);

        // if (!$account) {
        //     return null;
        // }

        // return new User($account);
    }

    /**
     * セッションからユーザー情報を復旧する際にコールされる
     *
     * @param $identifier
     */
    public function retrieveById($identifier)
    {
        $account = $this->AccountResource->getOne(Media::TWITTER(), $identifier);

        if (!$account) {
            return null;
        }

        return new User($account);
    }

    /**
     * @param $identifier
     * @param $token
     */
    public function retrieveByToken($identifier, $token)
    {
        throw new \LogicException('not implemented');

        // $account = $this->AccountResource->getByRememberToken($token);

        // if (!$account) {
        //     return null;
        // }

        // return new User($account);
    }

    /**
     * @param  Authenticatable $user
     * @param  $token
     * @return null
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        throw new \LogicException('not implemented');

        // $account = $this->AccountResource->updateRemenberToken($user->owner, $token);

        // if (!$account) {
        //     return null;
        // }

        // return new User($account);
    }

    /**
     * 与えられたクレデンシャルが有効化をチェックする
     *
     * @param Authenticatable $user
     * @param array           $credentials
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        $plain = $credentials['password'];

        return $user->owner->password->check($plain);
    }

}
