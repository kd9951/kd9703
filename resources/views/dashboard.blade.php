@extends('layouts.app')


@section('content')
<style>
.account-link {
    color: #3c4b64;
}
.account-link:hover {
    background-color: #e0f0ff;
}
.account-link .c-avatar {
    min-width: 48px;
}

</style>

<div class="container">

<div class="row"></div>
    <div class="col-md-4">
        <div class="card">
            <a class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <div>
                        <h4 class="card-title mb-0">人気のアカウント</h4>
                        <div class="small text-muted">Popular Accounts</div>
                    </div>
                    {{-- <div class="btn-toolbar d-none d-md-block" role="toolbar" aria-label="Toolbar with buttons">
                        <a  href="{{route('engagements.index')}}" class="btn btn-default" type="button"> すべて見る </a>
                    </div> --}}
                </div>
                <table class="table table-hover">
                    <tbody>
                        @foreach($popular_accounts as $account)
                        <a class="account-link d-flex align-items-center mb-3" href="http://twitter.com/{{$account->username}}}" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                            @if($account->img_thumnail_url)
                                <div class="c-avatar c-avatar-lg mr-3"><img class="c-avatar-img" src="{{ $account->img_thumnail_url }}" alt=""></div>
                            @endif
                            <div class="pr-3">
                                <small><b>{{ $account->username }}</b></small><br>
                                {{ $account->fullname }}<br>
                                💖{{ number_format($account->total_likes) }} 😊{{ number_format($account->total_follower) }}
                            </div>
                        </a>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>



        </div>
@endsection
