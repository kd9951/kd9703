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
.private-post {
    background-color: #2eb85c11;
}

.post-link .fullname {
    font-weight: bold;
    margin-right: 0.5em;
    color: #3c4b64;
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
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <div>
                        <h4 class="card-title mb-0">コミュニケーション検索</h4>
                        <div class="small text-muted">Communication Search</div>
                    </div>
                </div>
                <div>
                    <form action="{{route('communications.index')}}" method="GET">
                        @if(!$username)
                        <div class="mb-3">
                            <label for="exampleInputEmail1">ユーザー名・アカウント名（部分一致）</label>
                            <input type="username_partial" class="form-control" id="username_partial" name="username_partial" value="{{$username_partial}}">
                        </div>
                        @endif

                        <div class="mb-3">
                            <label for="exampleInputEmail1">キーワード</label>
                            <input type="keyword" class="form-control" id="keyword" name="keyword" value="{{$keyword}}">
                        </div>

                        <button type="submit" class="btn-block btn btn-primary mt-3">検索</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <div>
                        <h4 class="card-title mb-0">よく会話しているアカウント</h4>
                        <div class="small text-muted">Recentry Communicated Accounts</div>
                    </div>
                    {{-- <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with buttons">
                        <a  href="{{route('recents.index')}}" class="btn btn-default" type="button"> すべて見る </a>
                    </div> --}}
                </div>
                <div>
                    @foreach($communicatated_accounts as $account)
                        @if($account->username)
                        <a class="account-link d-flex" href="{{route('communications.index', ['username' => $account->username])}}">
                            @if($account->img_thumnail_url)
                                <div class="c-avatar c-avatar-lg mr-3"><img class="c-avatar-img" src="{{ $account->img_thumnail_url }}" alt=""></div>
                            @endif
                            <div style="width: 100%">
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

</div>

@component('components.paginator', ['paginate' => $communicatated_accounts->getPaginate()])
@endcomponent


</div>
@endsection
