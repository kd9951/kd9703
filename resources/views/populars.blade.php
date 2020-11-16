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
                <table class="table table-hover">
                    <tbody>
                    <tr>
                    @foreach($popular_accounts as $idx => $account)
                        <td>
                        <a class="account-link d-flex align-items-center mt-3" href="http://twitter.com/{{$account->username}}">
                            @if($account->img_thumnail_url)
                                <div class="c-avatar c-avatar-lg mr-3"><img class="c-avatar-img" src="{{ $account->img_thumnail_url }}" alt=""></div>
                            @endif
                            <div class="name pr-3">
                                <div class="username">{{ $account->username }}</div>
                                <div class="fullname">{{ $account->fullname }}</div>
                                💖{{ number_format($account->total_likes) }} 😊{{ number_format($account->total_follower) }}
                            </div>
                        </a>
                        </td>
                    @if ($idx % 3 == 2)
                    </tr>
                    <tr>
                    @endif
                    @endforeach
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@component('components.paginator', ['paginate' => $popular_accounts->getPaginate()])
@endcomponent

</div>
@endsection
