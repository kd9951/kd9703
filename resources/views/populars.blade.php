@extends('layouts.app')


@section('content')
<style>
.account-link {
    color: #3c4b64;
}
.account-link .name{
    overflow: hidden;
}
.account-link .username{
    font-weight: bold;
    font-size: 80%;
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

<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <div>
                        <h4 class="card-title mb-0">人気のアカウント</h4>
                        <div class="small text-muted">Popular Accounts</div>
                    </div>
                </div>
                    @foreach($popular_accounts as $idx => $account)
                        <a class="account-link d-flex align-items-center mt-3" href="http://twitter.com/{{$account->username}}">
                            @if($account->img_thumnail_url)
                                <div class="c-avatar c-avatar-lg mr-3"><img class="c-avatar-img" src="{{ $account->img_thumnail_url }}" alt=""></div>
                            @endif
                            <div style="width: 100%">
                                <div class="d-lg-flex justify-content-between"> {{-- 上段 --}}
                                    <div class="names">
                                            <div class="username">{{ $account->username }}</div>
                                            <div class="fullname">{{ $account->fullname }}</div>
                                    </div>
                                    <div class="specs d-flex justify-content-end hide-md-down">
                                        @if($account->prefecture)<div class="ml-2"><b>{{ Kd9703\Constants\Prefecture::TEXT_JPN[$account->prefecture->toValue()] }}</b></div>@endif
                                        <div class="ml-2"><b>{{ number_format($account->total_follow) }}</b> フォロー中</div>
                                        <div class="ml-2"><b>{{ number_format($account->total_follower) }}</b> フォロワー</div>
                                        <div class="ml-2"><b>{{ number_format($account->total_post) }}</b> ポスト</div>
                                    </div>
                                </div>
                                <div class="mt-1"> {{-- 下段 --}}
                                        <div class="description">{{ $account->description }}</div>
                                </div>
                            </div>
                        </a>
                    @endforeach
            </div>
        </div>
    </div>
</div>

@component('components.paginator', ['paginate' => $popular_accounts->getPaginate()])
@endcomponent

</div>
@endsection
