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
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <div>
                        <h4 class="card-title mb-0">コミュニケーション検索</h4>
                        <div class="small text-muted">Account Search</div>
                    </div>
                </div>
                <div>
                    <form action="" method="GET">
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
                        <h4 class="card-title mb-0">{{$title}}</h4>
                        <div class="small text-muted">{{$title_en}}</div>
                    </div>
                </div>
                <div>
                    @foreach($communicatated_posts as $post)
                        <a class="post-link @if($post->is_private){{'private-post'}}@endif d-flex" href="{{$post->url}}">
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
                            <div style="width: 100%">
                                <div class="justify-content-between align-items-center">
                                    <div class="names d-flex align-items-center">
                                            <div class="fullname">
                                                {{ $post->account->fullname }}
                                            </div>

                                            <div class="username">{{'@'}}{{ $post->account->username }}</div>
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

@component('components.paginator', ['paginate' => $communicatated_posts->getPaginate()])
@endcomponent


</div>
@endsection
