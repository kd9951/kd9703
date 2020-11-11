@extends('layouts.center')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card-group">
                <div class="card p-4">
                    <div class="card-body">
                        <h1 class="mb-3">Register</h1>
                        <h4 class="mb-5">ソーシャルアカウントで登録</h4>

                        <div class="d-flex mb-5">
                            <div class="c-avatar c-avatar-xl mr-3"><img class="c-avatar-img" src="{{$avatar}}" alt="user@email.com"></div>
                            <div>
                                <div class="h4">{{$nickname}}</div>
                                <div class="h5">{{$name}}</div>
                            </div>
                        </div>

                        <p class="mb-4">このアカウントは登録されていません。登録して利用を開始しますか？</p>
                        <ul class="text-info mb-5">
                            <li class="mb-2">追加情報は不要です。ワンクリックで登録が完了します。</li>
                        </ul>

                        <div class="row">
                            <div class="col-4">
                                <a href="{{route('welcome')}}" class="btn btn-block btn-lg btn-secondary px-4" type="submit">キャンセル</a>
                            </div>

                            <div class="col-4 ml-auto">
                                <form method="POST" action="{{route('auth.social.register',['provider'=>$provider])}}">
                                    @csrf
                                    <input name="provider" value="{{$provider}}" type="hidden" />
                                    <input name="id" value="{{$id}}" type="hidden" />
                                    <input name="nickname" value="{{$nickname}}" type="hidden" />
                                    <input name="name" value="{{$name}}" type="hidden" />
                                    <input name="email" value="{{$email}}" type="hidden" />
                                    <input name="avatar" value="{{$avatar}}" type="hidden" />

                                    <button class="btn btn-block btn-lg btn-primary px-4" type="submit">登録する</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
