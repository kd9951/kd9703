<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Kd9703\Entities\Paginate\Input as PaginateInput;
use Kd9703\Entities\Sort\Inputs as SortInputs;
use Kd9703\Resources\Interfaces\Account\Account;
use Kd9703\Resources\Interfaces\Post\Post;

class CommunicatingAccountController extends BaseController
{
    /**
     * @param Account $AccountResource
     */
    public function index(
        Request $request,
        // TODO UseCaseであるべき]
        Account $AccountResource,
        Post $postResource,
        $username = null
    ) {
        $account = Auth::user()->getAccount();

        // 最近会話したアカウント
        $communicatated_accounts = $AccountResource->getCommunicatingAccounts($account,
            null,
            new PaginateInput([
                'per_page' => $request->perPage ?? 30,
                'page'     => $request->page,
            ]), new SortInputs([[
                'key'   => 'count',
                'order' => 'desc',
            ]]));

        $target_account    = null;
        $target_account_id = null;
        if ($username) {
            $target_account    = $AccountResource->getByUsername($account->media, $username);
            $target_account_id = $target_account->account_id;
        }

        // タイトル
        $title    = "会話しているアカウント";
        $title_en = "Communicating Accounts";

        return view('communicating-accounts', [
            'title'                   => $title,
            'title_en'                => $title_en,
            'communicatated_accounts' => $communicatated_accounts,
            'username'                => $username,
            'username_partial'        => $username_partial ?? '',
            'keyword'                 => $keyword ?? '',
        ]);
    }
}
