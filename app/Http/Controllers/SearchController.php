<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Kd9703\Entities\Paginate\Input as PaginateInput;
use Kd9703\Resources\Interfaces\Account\Account;

/**
 * 検索
 */
class SearchController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param Account $AccountResource
     */
    public function index(
        Request $request,
        // TODO UseCaseであるべき
        Account $AccountResource
    ) {
        $account = Auth::user()->getAccount();

        $accounts = $AccountResource->search($account->media, 
            $request->keyword,
            new PaginateInput([
                'per_page' => $request->perPage ?? 30,
                'page'     => $request->page,
            ])
        );

        $keywords = $request->keyword;
        $keywords = preg_replace('/[、，]/u', ',', $keywords);
        $keywords = preg_replace('/[\s;:,]+/', ' ', $keywords);
        $keywords = trim($keywords);
        if ($keywords) {
            $title = "「{$keywords}」の検索結果";
            $title_en = "Search results for '$keywords'";
        } else {
            $title = "すべてのサロンアカウント";
            $title_en = "All of salon accounts";
        }
        
        return view('accounts', [
            'title' => $title,
            'title_en' => $title_en,
            'accounts' => $accounts,
        ]);
    }
}
