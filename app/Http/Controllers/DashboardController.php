<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Kd9703\Entities\Paginate\Input as PaginateInput;
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
        $popular_accounts = $AccountResource->getPops($account->media, new PaginateInput([
            'per_page' => 50,
            'page'     => 1,
        ]));
        $popular_accounts->suffle();
        $popular_accounts = $popular_accounts->slice(0, 5);

        $total_salon_accounts = 0; // $AccountResource->search($account->media)->count();

        return view('dashboard', [
            'total_salon_accounts' => $total_salon_accounts,
            // 'total_active_accounts' => $total_active_accounts,
            'popular_accounts'     => $popular_accounts,
        ]);
    }
}
