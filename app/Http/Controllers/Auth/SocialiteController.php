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
    protected $redirectToAfterRegist = '/dashboard';
    // protected $redirectToAfterRegist = '/configuration'; 

    /**
     * @param Request $request
     */
    function login(string $provider)
    {
        if (config("services.$provider.pin_based")) {
            $url   = $this->getProvider($provider)->redirect()->getTargetUrl();
            parse_str(parse_url($url, PHP_URL_QUERY), $queries);

            $token = $queries['oauth_token'] ?? '';

            return view('auth.social.pin')->with([
                'provider'     => $provider,
                'callback_url' => $url,
                'oauth_token'  => $token,
            ]);
        }

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

        // ユーザー情報を現在値に（RegistAccountと処理がかぶっているし、ちょっと足りない）
        foreach ([
            'username'         => $user->getNickname(),
            'fullname'         => $user->getName(),
            // 'prefecture'          => $user[''],
            'web_url1'         => $user['entities']['url']['urls'][0]['expanded_url'] ?? null,
            'web_url2'         => $user['entities']['url']['urls'][1]['expanded_url'] ?? null,
            'web_url3'         => $user['entities']['url']['urls'][2]['expanded_url'] ?? null,
            'img_thumnail_url' => $user['profile_image_url_https'] ?? null,
            'img_cover_url'    => $user['profile_background_image_url_https'] ?? null,
            'total_post'       => $user['statuses_count'] ?? null,
            'total_follow'     => $user['friends_count'] ?? null,
            'total_follower'   => $user['followers_count'] ?? null,
            'total_likes'      => $user['favourites_count'] ?? null,
            'last_posted_at'   => ($user['status']['created_at'] ?? null) ? date('Y-m-d H:i:s', strtotime($user['status']['created_at'])) : null,
            'is_private'       => $user['protected'] ?? null,
        ] as $key => $value) {
            if (!is_null($value)) {
                $account->$key = $value;
            }
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
