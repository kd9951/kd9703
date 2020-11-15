<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Kd9703\Resources\Interfaces\Account\Account;

class DashboardController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function index(
        // TODO UseCaseであるべき
        Account $AccountResource
    )
    {
        $account = Auth::user()->getAccount();

        $popular_accounts = $AccountResource->getPops($account->media, 20);

        return view('dashboard', [
            'popular_accounts' => $popular_accounts
        ]);
    }
}
