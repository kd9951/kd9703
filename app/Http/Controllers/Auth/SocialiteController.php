<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Kd9703\Constants\Media;
use Kd9703\Entities\Media\Account;
use Kd9703\Resources\Interfaces\Account\Account as AccountResource;
use Kd9703\Usecases\RegistAccount;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;

/**
 * ソーシャルログインを実現するために
 * Socialiteを使用したコントローラ
 */
class SocialiteController extends BaseController
{
    use AuthenticatesUsers, RegistersUsers {
        AuthenticatesUsers::redirectPath insteadof RegistersUsers;
        AuthenticatesUsers::guard insteadof RegistersUsers;
    }

    /**
     * 認証後のリダイレクト
     *
     * @var string
     */
    protected $redirectToAfterLogin = '/dashboard';

    /**
     * 登録後のリダイレクト
     *
     * @var string
     */
    protected $redirectToAfterRegist = '/configuration';

    /**
     * @param Request $request
     */
    function login(string $provider)
    {
        return $this->getProvider($provider)->redirect();
    }

    /**
     * @param $id
     * @param Request $request
     */
    function callback(
        string $provider,
        Request $request,
        AccountResource $AccountResource
    ) {

        $socialite = $this->getProvider($provider);

        $user = $socialite->user() ?? null;

        // dd($user, $request->all(), [
        //     'oauth_provider'     => $provider,
        //     'account_id'         => $user->getId(),
        //     'oauth_access_token' => $user->token,
        // ]);

        // 該当する User を取得
        $account = $AccountResource->getOne(Media::TWITTER(), $user->getId());

        // 見つからないなら即時Owner作成
        if (!$account) {
            $account = app()->call([$this, 'regist'], [
                'user' => $user,
            ]);

            $this->redirectTo = $this->redirectToAfterRegist;
        } else {
            $this->redirectTo = $this->redirectToAfterLogin;
        }

        if ($account->oauth_access_token != $user->token) {
            $account->oauth_access_token  = $user->token;
            $account->oauth_access_secret = $user->tokenSecret;
        }

        $account->last_logged_in_at = Carbon::now()->format('Y-m-d H:i:s');

        $AccountResource->upsert($account);

        // Laravel ログイン処理
        // 認証はSNSで済んでいるのでLaravelでは認証しない
        $auth_user = new User($account);
        $this->guard()->login($auth_user);

        return $this->sendLoginResponse($request);
    }

    /**
     * @param  string        $provider
     * @param  Account       $account
     * @param  SocialiteUser $user
     * @param  RegistAccount $RegistAccount
     * @return mixed
     */
    function regist(
        SocialiteUser $user,
        RegistAccount $RegistAccount
    ): Account {
        $account = $RegistAccount([
            'twitter_account_id' => $user->getId(),
            'token'              => $user->token ?? null,
            'token_secret'       => $user->tokenSecret ?? null,
            'username'           => $user->getNickname(),
            'nickname'           => $user->getName(),
            'user'               => $user,
        ]);

        return $account;
    }

    /**
     * 対応するソーシャルログインプロバイダ
     * @param $provider
     */
    function getProvider($provider)
    {
        switch ($provider) {
            case 'twitter':
                // case 'facebook':
                // case 'google':
                return Socialite::driver($provider);

            default:
                abort(404, 'Auth Provider Not Found');
        }
    }

}
