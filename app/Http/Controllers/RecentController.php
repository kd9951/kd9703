<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Kd9703\Entities\Paginate\Input as PaginateInput;
use Kd9703\Entities\Sort\Inputs as SortInputs;
use Kd9703\Resources\Interfaces\Account\Account;

/**
 * 最近始めたアカウント
 */
class RecentController extends BaseController
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

        $accounts = $AccountResource->search($account->media, null, new PaginateInput([
            'per_page' => $request->perPage ?? 30,
            'page'     => $request->page,
        ]), new SortInputs([[
            'key'   => 'started_at',
            'order' => 'desc',
        ]]));
        
        return view('accounts', [
            'title'    => '最近始めたアカウント',
            'title_en' => 'Recentry Joined Accounts',
            'accounts' => $accounts,
        ]);
    }
}
