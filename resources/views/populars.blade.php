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

@php $paginate = $popular_accounts->getPaginate() @endphp
@php $paginator = $paginate->getPaginator(10) @endphp
<nav aria-label="Page navigation example">
<ul class="pagination justify-content-end">
    @if($paginate->prev_page)
        <li class="page-item"><a class="page-link" href="?page=1">◀</a></li>
    @else
        <li class="page-item disabled"><span class="page-link">◀</span></li>
    @endif

    @if($paginator->show_first)
        <li class="page-item"><a class="page-link" href="?page=1">1</a></li>
    @endif

    @if($paginator->show_forward_dash)
        <li class="page-item">……</li>
    @endif

    @foreach($paginator->pages_forward as $page)
        <li class="page-item"><a class="page-link" href="?page={{$page}}">{{$page}}</a></li>
    @endforeach

        <li class="page-item active"><span class="page-link">{{$paginate->current_page}}</span></li>

    @foreach($paginator->pages_backward as $page)
        <li class="page-item"><a class="page-link" href="?page={{$page}}">{{$page}}</a></li>
    @endforeach

    @if($paginator->show_backward_dash)
        <li class="page-item">……</li>
    @endif

    @if($paginator->show_last)
        <li class="page-item"><a class="page-link" href="?page={{$paginate->last_page}}">{{$paginate->last_page}}</a></li>
    @endif
    @if($paginate->next_page)
        <li class="page-item"><a class="page-link" href="?page={{$paginate->next_page}}">▶</a></li>
    @else
        <li class="page-item disabled"><span class="page-link">▶</span></li>
    @endif
</ul>
</nav>
{{--
'total'        => ['integer', null], // 総アイテム数
'per_page'     => ['integer', null], // 1ページのアイテム数
'current_page' => ['integer', null], // 現在のページ １～last_page
'prev_page'    => ['integer', null], // 前のページ なければNULL
'next_page'    => ['integer', null], // 次のページ なければNULL
'last_page'    => ['integer', null], // 最後のページ（最大のページ番号）
'from'         => ['integer', null], // 最初のアイテム 1～total
'to'           => ['integer', null], // 最後のアイテム 1～total
];

'show_first'         => 'integer', // [<<] を活性化するか
'show_forward_dash'  => 'integer', // ... を表示するか
'pages_forward'      => 'array of integer', // 現在ページの前に表示するボタン
'pages_backward'     => 'array of integer', // 後に表示するボタン
'show_backward_dash' => 'integer', // ... を表示するか
'show_last'          => 'integer', // [>>] を活性化するか --}}


</div>
@endsection
