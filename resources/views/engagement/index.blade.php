@extends('layouts.app')

@section('content')
        <div class="container">

            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <h4 class="card-title mb-0">エンゲージメントランキング</h4>
                            <div class="small text-muted">Engagement Ranking</div>
                        </div>
                        {{-- <div class="btn-toolbar d-none d-md-block" role="toolbar" aria-label="Toolbar with buttons">
                            <a  href="{{route('engagements.index')}}" class="btn btn-default" type="button"> すべて見る </a>
                        </div> --}}
                    </div>
                    <table class="table table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>from_account_id</th>
                                <th>recent</th>
                                <th>total</th>
                                <th>total_comment</th>
                                <th>recent_comment</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($engagements as $engagement)
                            <tr>
                                <td>https://greensnap.jp/user/{{$engagement['from_account_id']}}</td>
                                <td>{{$engagement['recent']}}</td>
                                <td>{{$engagement['total']}}</td>
                                <td>{{$engagement['total_comment']}}</td>
                                <td>{{$engagement['recent_comment']}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{ $engagements->links() }}

        </div>
@endsection
