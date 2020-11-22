<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Kd9703\Entities\Paginate\Input as PaginateInput;
use Kd9703\Entities\Sort\Inputs as SortInputs;
use Kd9703\Resources\Interfaces\Account\Account;

class DashboardController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param Account $AccountResource
     */
    public function index(
        // TODO UseCaseであるべき]
        Account $AccountResource
    ) {
        $account = Auth::user()->getAccount();

        // トップ５
        $popular_accounts = $AccountResource->search($account->media, null, new PaginateInput([
            'per_page' => 30,
            'page'     => 1,
        ]), new SortInputs([[
            'key'   => 'score',
            'order' => 'desc',
        ]]));
        $popular_accounts->suffle();
        $popular_accounts = $popular_accounts->slice(0, 5);

        // トップ５
        $recent_accounts = $AccountResource->search($account->media, null, new PaginateInput([
            'per_page' => 30,
            'page'     => 1,
        ]), new SortInputs([[
            'key'   => 'started_at',
            'order' => 'desc',
        ]]));
        $recent_accounts->suffle();
        $recent_accounts = $recent_accounts->slice(0, 5);

        $total_salon_accounts = 0; // $AccountResource->search($account->media)->count();

        return view('dashboard', [
            'total_salon_accounts' => $total_salon_accounts,
            // 'total_active_accounts' => $total_active_accounts,
            'popular_accounts'     => $popular_accounts,
            'recent_accounts'      => $recent_accounts,
        ]);
    }
}
