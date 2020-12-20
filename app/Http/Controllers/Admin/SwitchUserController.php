<?php

namespace App\Http\Controllers\Admin;

use App\User;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Kd9703\Constants\Media;
use Kd9703\Resources\Interfaces\Account\Account as AccountResource;

/**
 * ソーシャルログインを実現するために
 * Socialiteを使用したコントローラ
 */
class SwitchUserController extends BaseController
{
    /**
     * @param $account_id
     */
    public function get($username, AccountResource $AccountResource)
    {
        // 該当する User を取得
        $account = $AccountResource->getByUsername(Media::TWITTER(), $username);

        // Laravel ログイン処理
        $auth_user = new User($account);
        Auth::login($auth_user);

        return redirect()->route('dashboard');
    }
}
