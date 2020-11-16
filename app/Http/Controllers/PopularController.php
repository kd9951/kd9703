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
 * 人気のアカウント
 */
class PopularController extends BaseController
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

        $popular_accounts = $AccountResource->getPops($account->media, new PaginateInput([
            'per_page' => $request->perPage ?? 50,
            'page'     => $request->page,
        ]));

        return view('populars', [
            'popular_accounts' => $popular_accounts,
        ]);
    }
}
