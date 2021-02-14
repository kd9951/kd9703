<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Kd9703\Entities\Paginate\Input as PaginateInput;
use Kd9703\Entities\Sort\Inputs as SortInputs;
use Kd9703\Resources\Interfaces\Account\Account;
use Kd9703\Resources\Interfaces\Post\Post;

class CommunicationController extends BaseController
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

        // // 最近会話したアカウント
        // $recent_communicatated_accounts = $AccountResource->getCommunicatingAccounts($account,
        //     null,
        //     new PaginateInput([
        //         'per_page' => 6,
        //         'page'     => 1,
        //     ]), new SortInputs([[
        //         'key'   => 'count',
        //         'order' => 'desc',
        //     ]]));

        $target_account    = null;
        $target_account_id = null;
        if ($username) {
            $target_account    = $AccountResource->getByUsername($account->media, $username);
            $target_account_id = $target_account->account_id;
        }

        // 最近のコミュニケーション
        $communicatated_posts = $postResource->getCommunications(
            $account,
            $target_account_id,
            null,
            $request->username_partial ?? null,
            $request->keyword ?? null,
            new PaginateInput([
                'per_page' => $request->perPage ?? 30,
                'page'     => $request->page,
            ]), new SortInputs([[
                'key'   => 'posted_at',
                'order' => 'desc',
            ]]));

        // タイトル
        $username_partial = $request->username_partial;
        $username_partial = preg_replace('/[、，]/u', ',', $username_partial);
        $username_partial = preg_replace('/[\s;:,]+/', ' ', $username_partial);
        $username_partial = trim($username_partial);
        $keyword          = $request->keyword;
        $keyword          = preg_replace('/[、，]/u', ',', $keyword);
        $keyword          = preg_replace('/[\s;:,]+/', ' ', $keyword);
        $keyword          = trim($keyword);
        if ($keyword) {
            $title    = "「{$keyword}」の検索結果";
            $title_en = "Search results for '$keyword'";
        } elseif ($target_account) {
            $title    = $target_account->fullname . "さんとのコミュニケーション";
            $title_en = "Communications with @" . $target_account->username;
        } elseif ($username_partial) {
            $title    = "「{$username_partial}」の検索結果";
            $title_en = "Search results for '$username_partial'";
        } else {
            $title    = "すべてのコミュニケーション";
            $title_en = "All communications";
        }

        return view('communications', [
            'title'                     => $title,
            'title_en'                  => $title_en,
            'communicatated_posts'      => $communicatated_posts,
            'username'                  => $username,
            'username_partial'          => $username_partial,
            'keyword'                   => $keyword,
            'reviewed_as_using_user_at' => $account->reviewed_as_using_user_at,
        ]);
    }
}
