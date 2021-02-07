@extends('layouts.app')


@section('content')
<style>
.account-link,
.post-link {
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
.account-link:hover,
.post-link:hover {
    background-color: #e0f0ff;
    text-decoration: none;
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

.sub-avatar {
    width: 32px !important;
    height: 32px !important;
    position: absolute;
    right: -6px;
    bottom: -16px;
    min-width: 24px !important;
}

.sub-avatar .c-avatar-img {
    border: 2px solid #fff;
}

.post-body {
    font-size: 0.9em;
}

.post-link .names {
    font-size: 0.85em;
    color: #0008;
}

.post-link .fullname {
    font-weight: bold;
    margin-right: 0.5em;
    color: #3c4b64;
}

.body-secret {
    font-size: 0.8em;
    background: #0000000C;
    padding: 0.75em 2em;
    border: 1px solid #00000020;
    border-radius: 0.5em;
}
</style>

<div class="container">

@php
$show_new_by = Auth::user()->config('show_new_by');
$show_new_date = Carbon\Carbon::parse('-' . Auth::user()->config('show_new_days') . ' days')->format('Y-m-d H:i:s');
@endphp

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
                        {{-- <div class="text-value-lg">{{(($kpis[0] ?? null) && $kpis[0]->salon_accounts_active) ? number_format($kpis[0]->salon_accounts_active) : 'not available'}}</div> --}}
                        <div class="text-value-lg">not available</div>
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
                        <h4 class="card-title mb-0">アカウント検索</h4>
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
                            @foreach(config('app.salon') == 'progress' ? [
                                'DMはご遠慮ください',
                                'ラグナリオ',
                                'ウォルクス',
                                'アリスカーナ',
                                'フラーシア',
                                '芸人',
                                '音楽',
                                '舞台',
                                'Youtuber',
                                '配信',
                                'XENO',
                                '大阪 エンジニア',
                                'クラファン',
                            ] : [
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
                            <div style="width: calc(100% - 48px - 1rem)">
                                <div class="justify-content-between align-items-center">
                                    <div class="names">
                                            <div class="fullname">
                                                {{ $account->fullname }}
                                                @if(
                                                    $show_new_by == Kd9703\Constants\ShowNew::BY_CREATED_AT
                                                    && $account->created_at >= $show_new_date
                                                    || $show_new_by == Kd9703\Constants\ShowNew::BY_STARTED_AT
                                                    && $account->started_at >= $show_new_date
                                                )
                                                <span class="badge bg-warning text-white">NEW</span>
                                                @endif
                                            </div>

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
                            <div style="width: calc(100% - 48px - 1rem)">
                                <div class="justify-content-between align-items-center">
                                    <div class="names">
                                            <div class="fullname">
                                                {{ $account->fullname }}
                                                @if(
                                                    $show_new_by == Kd9703\Constants\ShowNew::BY_CREATED_AT
                                                    && $account->created_at >= $show_new_date
                                                    || $show_new_by == Kd9703\Constants\ShowNew::BY_STARTED_AT
                                                    && $account->started_at >= $show_new_date
                                                )
                                                <span class="badge bg-warning text-white">NEW</span>
                                                @endif
                                            </div>

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
<div class="row">
    <div class="col-md-6 col-xl-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <div>
                        <h4 class="card-title mb-0">コミュニケーション検索</h4>
                        <div class="small text-muted">Account Search</div>
                    </div>
                </div>
                <div>
                    <form action="{{route('communications.index')}}" method="GET">
                        <div class="mb-3">
                            <label for="exampleInputEmail1">ユーザー名・アカウント名（部分一致）</label>
                            <input type="username_partial" class="form-control" id="username_partial" name="username_partial">
                        </div>

                        <div class="mb-3">
                            <label for="exampleInputEmail1">キーワード</label>
                            <input type="keyword" class="form-control" id="keyword" name="keyword">
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
                        <h4 class="card-title mb-0">よく会話しているアカウント</h4>
                        <div class="small text-muted">Recentry Communicated Accounts</div>
                    </div>
                    <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with buttons">
                        <a  href="{{route('communicating-accounts.index')}}" class="btn btn-default" type="button"> すべて見る </a>
                    </div>
                </div>
                <div>
                    @foreach($recent_communicatated_accounts as $account)
                        @if($account->username)
                        <a class="account-link d-flex" href="{{route('communications.index', ['username' => $account->username])}}">
                            @if($account->img_thumnail_url)
                                <div class="c-avatar c-avatar-lg mr-3"><img class="c-avatar-img" src="{{ $account->img_thumnail_url }}" alt=""></div>
                            @endif
                            <div style="width: calc(100% - 48px - 1rem)">
                                <div class="justify-content-between align-items-center">
                                    <div class="names">
                                            <div class="fullname">
                                                {{ $account->fullname }}
                                                @if(
                                                    $show_new_by == Kd9703\Constants\ShowNew::BY_CREATED_AT
                                                    && $account->created_at >= $show_new_date
                                                    || $show_new_by == Kd9703\Constants\ShowNew::BY_STARTED_AT
                                                    && $account->started_at >= $show_new_date
                                                )
                                                <span class="badge bg-warning text-white">NEW</span>
                                                @endif
                                            </div>

                                            <div class="username">{{ $account->username }}</div>
                                            <div class="location">{{ $account->location }}</div>
                                    </div>
                                </div>
                            </div>
                        </a>
                        @endif
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
                        <h4 class="card-title mb-0">最近のコミュニケーション</h4>
                        <div class="small text-muted">Recentry Communicated Posts</div>
                    </div>
                    <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with buttons">
                        <a  href="{{route('communications.index')}}" class="btn btn-default" type="button"> すべて見る </a>
                    </div>
                </div>
                <div>
                    @foreach($recent_communicatated_posts as $post)
                        <a class="post-link d-flex" href="{{$post->url}}">
                            @if($post->account_id == Auth::user()->getAccount()->account_id)
                                @if($post->account->img_thumnail_url)
                                    <div class="c-avatar c-avatar-lg mr-3">
                                        <img class="c-avatar-img" src="{{ $post->account->img_thumnail_url }}" alt="">
                                        @if($post->in_reply_to_account && $post->account_id != $post->in_reply_to_account_id)
                                        <div class="sub-avatar c-avatar"><img class="c-avatar-img" src="{{ $post->in_reply_to_account->img_thumnail_url }}" alt=""></div>
                                        @endif
                                    </div>
                                @endif
                            @else
                                @if($post->account->img_thumnail_url)
                                    <div class="c-avatar c-avatar-lg mr-3"><img class="c-avatar-img" src="{{ $post->account->img_thumnail_url }}" alt=""></div>
                                @endif
                            @endif
                            <div style="width: calc(100% - 48px - 1rem)">
                                <div class="justify-content-between align-items-center">
                                    <div class="names d-flex align-items-center">
                                            <div class="fullname">
                                                {{ $post->account->fullname }}
                                            </div>

                                            {{-- <div class="username">{{'@'}}{{ $post->account->username }}</div> --}}
                                            <div class="username ml-auto">{{ date('n月j日', strtotime($post->posted_at)) }}</div>
                                    </div>
                                </div>
                                @if(
                                    $post->account_id == Auth::user()->getAccount()->account_id
                                    || $post->in_reply_to_account_id == Auth::user()->getAccount()->account_id
                                    || $post->is_private && in_array(Auth::user()->getAccount()->account_id, $post->recipient_account_ids) && count($post->recipient_account_ids) == 1
                                )
                                <div class="post-body mt-1">
                                    {{$post->body}}
                                </div>
                                @else
                                <div class="body-secret">このツイートはフォローしていないアカウントのツイートかもしれないので表示を制限しています。クリックして公式サイトで確認してください。</div>
                                @endif
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
