@extends('layouts.app')


@section('content')
<style>
.account-link {
    color: #3c4b64;
    margin: 1.5rem -0.5rem -0.5rem;
    padding: 0.5rem;
    border-radius: 0.25rem;
}
.account-link .name{
    overflow: hidden;
}
.account-link .username{
    display: inline-block;
    font-weight: bold;
    font-size: 80%;
}
.location {
    display: inline-block;
    font-size: 80%;
    margin-left: 0.5em;
}
.account-link .fullname{
    font-weight: bold;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.account-link:hover {
    background-color: #e0f0ff;
}
.account-link .c-avatar {
    min-width: 48px;
}

.account-link .description {
    font-size: 0.9em;
    color: var(--dark);
}

.specs > div {
    font-size: 0.9em;
}
</style>

<div class="container">

        {{-- アプリ利用者数
        アプリアクティブ利用者数
        アプリ利用拒否者数
        プロフィール更新数 --}}

    <div class="row">
        <div class="col-sm-6 col-lg-3">
            <div class="card text-white bg-gradient-primary">
                <div class="card-body card-body pb0 d-flex justify-content-between align-items-start">
                    <div>
                    <div class="text-value-lg">{{($kpis[0] ?? null) ? number_format($kpis[0]->salon_accounts_total) : 'not available'}}</div>
                    <div>確認サロンアカウント数</div>
                    </div>
                </div>
                {{-- <div class="c-chart-wrapper mt-3 mx-3" style="height:70px;"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
                    <canvas class="chart chartjs-render-monitor" id="card-chart1" height="70" style="display: block;" width="256"></canvas>
                </div> --}}
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card text-white bg-gradient-info">
                <div class="card-body card-body pb0 d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-value-lg">{{(($kpis[0] ?? null) && $kpis[0]->salon_accounts_active) ? number_format($kpis[0]->salon_accounts_active) : 'not available'}}</div>
                        <div>アクティブアカウント数</div>
                    </div>
                </div>
                {{-- <div class="c-chart-wrapper mt-3 mx-3" style="height:70px;"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
                <canvas class="chart chartjs-render-monitor" id="card-chart2" height="70" width="256" style="display: block;"></canvas>
                <div id="card-chart2-tooltip" class="c-chartjs-tooltip top" style="opacity: 0; left: 201.982px; top: 124.882px;"><div class="c-tooltip-header"><div class="c-tooltip-header-item">July</div></div><div class="c-tooltip-body"><div class="c-tooltip-body-item"><span class="c-tooltip-body-item-color" style="background-color: rgb(51, 153, 255);"></span><span class="c-tooltip-body-item-label">My First dataset</span><span class="c-tooltip-body-item-value">11</span></div></div></div></div> --}}
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card text-white bg-gradient-success">
                <div class="card-body card-body pb0 d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-value-lg">{{($kpis[0] ?? null) ? number_format($kpis[0]->started_accounts_2w) : 'not available'}}</div>
                        <div>過去2週間に利用開始したアカウント</div>
                    </div>
                </div>
                {{-- <div class="c-chart-wrapper mt-3 mx-3" style="height:70px;"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
                <canvas class="chart chartjs-render-monitor" id="card-chart2" height="70" width="256" style="display: block;"></canvas>
                <div id="card-chart2-tooltip" class="c-chartjs-tooltip top" style="opacity: 0; left: 201.982px; top: 124.882px;"><div class="c-tooltip-header"><div class="c-tooltip-header-item">July</div></div><div class="c-tooltip-body"><div class="c-tooltip-body-item"><span class="c-tooltip-body-item-color" style="background-color: rgb(51, 153, 255);"></span><span class="c-tooltip-body-item-label">My First dataset</span><span class="c-tooltip-body-item-value">11</span></div></div></div></div> --}}
            </div>
        </div>
    </div>

    @if(Auth::user()->isAdmin())
    <div class="row">
        <div class="col-sm-6 col-lg-3">
            <div class="card text-white bg-gradient-primary">
                <div class="card-body card-body pb0 d-flex justify-content-between align-items-start">
                    <div>
                    <div class="text-value-lg">{{($kpis[0] ?? null) ? number_format($kpis[0]->registered_accounts_total) : 'not available'}}</div>
                    <div>アプリ利用者数</div>
                    </div>
                </div>
                {{-- <div class="c-chart-wrapper mt-3 mx-3" style="height:70px;"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
                    <canvas class="chart chartjs-render-monitor" id="card-chart1" height="70" style="display: block;" width="256"></canvas>
                </div> --}}
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card text-white bg-gradient-danger">
                <div class="card-body card-body pb0 d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-value-lg">{{($kpis[0] ?? null) ? number_format($kpis[0]->rejected_accounts_total) : 'not available'}}</div>
                        <div>アプリ利用拒否者数</div>
                    </div>
                </div>
                {{-- <div class="c-chart-wrapper mt-3 mx-3" style="height:70px;"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
                <canvas class="chart chartjs-render-monitor" id="card-chart2" height="70" width="256" style="display: block;"></canvas>
                <div id="card-chart2-tooltip" class="c-chartjs-tooltip top" style="opacity: 0; left: 201.982px; top: 124.882px;"><div class="c-tooltip-header"><div class="c-tooltip-header-item">July</div></div><div class="c-tooltip-body"><div class="c-tooltip-body-item"><span class="c-tooltip-body-item-color" style="background-color: rgb(51, 153, 255);"></span><span class="c-tooltip-body-item-label">My First dataset</span><span class="c-tooltip-body-item-value">11</span></div></div></div></div> --}}
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card text-white bg-gradient-success">
                <div class="card-body card-body pb0 d-flex justify-content-between align-items-start">
                    <div>
                    <div class="text-value-lg">{{($kpis[1] ?? null) ? number_format($kpis[1]->reviewed_accounts) : 'not available'}}</div>
                    <div>プロフィール更新数</div>
                    </div>
                </div>
                {{-- <div class="c-chart-wrapper mt-3 mx-3" style="height:70px;"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
                    <canvas class="chart chartjs-render-monitor" id="card-chart1" height="70" style="display: block;" width="256"></canvas>
                </div> --}}
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card text-white bg-gradient-info">
                <div class="card-body card-body pb0 d-flex justify-content-between align-items-start">
                    <div>
                    <div class="text-value-lg">{{($kpis[0] ?? null) ? number_format($kpis[0]->api_called_total) : 'not available'}}</div>
                    <div>TwitterAPI コール数</div>
                    </div>
                </div>
                {{-- <div class="c-chart-wrapper mt-3 mx-3" style="height:70px;"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
                    <canvas class="chart chartjs-render-monitor" id="card-chart1" height="70" style="display: block;" width="256"></canvas>
                </div> --}}
            </div>
        </div>
    </div>
    @endif

<div class="row">
    <div class="col-md-6 col-xl-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <div>
                        <h4 class="card-title mb-0">検索</h4>
                        <div class="small text-muted">Account Search</div>
                    </div>
                </div>
                <div>
                    <form action="search" method="GET">
                        <div class="mb-3">
                            <label for="exampleInputEmail1">キーワード</label>
                            <input type="keyword" class="form-control" id="keyword" name="keyword">
                        </div>

                        <div class=" mb-3">
                            @foreach([
                                '芸人 絵本作家',
                                '吉本 芸人',
                                '飲食店',
                                'ヘアサロン',
                                '経営して',
                                'Youtuber',
                                'テレビ ディレクタ',
                                '大阪 エンジニア',
                                'フットサル',
                                '子育て',
                            ] as $word)
                            <a class="btn btn-light btn-sm mr-1 mb-2" href="/search?keyword={{urlencode($word)}}">{{$word}}</a>
                            @endforeach
                        </div>

                        <button type="submit" class="btn-block btn btn-primary">検索</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <div>
                        <h4 class="card-title mb-0">注目のアカウント</h4>
                        <div class="small text-muted">Featured Accounts</div>
                    </div>
                    <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with buttons">
                        <a  href="{{route('populars.index')}}" class="btn btn-default" type="button"> すべて見る </a>
                    </div>
                </div>
                <div>
                    @foreach($popular_accounts as $account)
                        <a class="account-link d-flex" href="http://twitter.com/{{$account->username}}">
                            @if($account->img_thumnail_url)
                                <div class="c-avatar c-avatar-lg mr-3"><img class="c-avatar-img" src="{{ $account->img_thumnail_url }}" alt=""></div>
                            @endif
                            <div style="width: 100%">
                                <div class="justify-content-between align-items-center">
                                    <div class="names">
                                            <div class="fullname">{{ $account->fullname }}</div>
                                            <div class="username">{{ $account->username }}</div>
                                            <div class="location">{{ $account->location }}</div>
                                    </div>

                                    <div class="specs mt-1 d-flex justify-content-end">
                                        <div class=""><b>{{ number_format($account->total_follow) }}</b> フォロー中</div>
                                        <div class="ml-2"><b>{{ number_format($account->total_follower) }}</b> フォロワー</div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <div>
                        <h4 class="card-title mb-0">最近始めたアカウント</h4>
                        <div class="small text-muted">Recentry Joined Accounts</div>
                    </div>
                    <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with buttons">
                        <a  href="{{route('recents.index')}}" class="btn btn-default" type="button"> すべて見る </a>
                    </div>
                </div>
                <div>
                    @foreach($recent_accounts as $account)
                        <a class="account-link d-flex" href="http://twitter.com/{{$account->username}}">
                            @if($account->img_thumnail_url)
                                <div class="c-avatar c-avatar-lg mr-3"><img class="c-avatar-img" src="{{ $account->img_thumnail_url }}" alt=""></div>
                            @endif
                            <div style="width: 100%">
                                <div class="justify-content-between align-items-center">
                                    <div class="names">
                                            <div class="fullname">{{ $account->fullname }}</div>
                                            <div class="username">{{ $account->username }}</div>
                                            <div class="location">{{ $account->location }}</div>
                                    </div>

                                    <div class="specs mt-1 d-flex justify-content-end">
                                        <div class=""><b>{{ number_format($account->total_follow) }}</b> フォロー中</div>
                                        <div class="ml-2"><b>{{ number_format($account->total_follower) }}</b> フォロワー</div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

</div>



</div>
@endsection
