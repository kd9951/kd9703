<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Kd9703\Entities\Paginate\Input as PaginateInput;
use Kd9703\Entities\Sort\Inputs as SortInputs;
use Kd9703\Resources\Interfaces\Account\Account;
use Kd9703\Resources\Interfaces\Analyze\Kpi;
use Kd9703\Resources\Interfaces\Post\Post;

class DashboardController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param Account $AccountResource
     */
    public function index(
        // TODO UseCaseであるべき]
        Account $AccountResource,
        Post $postResource,
        Kpi $KpiResource
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

        // 最近会話したアカウント
        $recent_communicatated_accounts = $AccountResource->getCommunicatingAccounts($account,
            null,
            new PaginateInput([
                'per_page' => 10,
                'page'     => 1,
            ]), new SortInputs([[
                'key'   => 'count',
                'order' => 'desc',
            ]]));

        // 最近のコミュニケーション
        $recent_communicatated_posts = $postResource->getCommunications($account,
            null,
            null,
            null,
            null,
            new PaginateInput([
                'per_page' => 5,
                'page'     => 1,
            ]), new SortInputs([[
                'key'   => 'posted_at',
                'order' => 'desc',
            ]]));

        // KPI
        $kpis = $KpiResource->getList(
            Carbon::parse('-14 days')->format('Y-m-d'),
            Carbon::now()->format('Y-m-d')
        );

        return view('dashboard', [
            'popular_accounts'               => $popular_accounts,
            'recent_accounts'                => $recent_accounts,
            'recent_communicatated_accounts' => $recent_communicatated_accounts,
            'recent_communicatated_posts'    => $recent_communicatated_posts,
            'kpis'                           => $kpis,
        ]);
    }
}
