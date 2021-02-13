@extends('layouts.center')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card-group">
                <div class="card p-4">
                    <div class="card-body">
                        <h1 class="mb-3">PIN Login</h1>
                        {{-- <h4 class="mb-5">認証番号を取得する</h4> --}}

                        <p class="mb-3">ボタンをクリックして認証番号を取得してください。</p>

                        <a href="{{$callback_url}}" class="btn btn-block btn-lg btn-info" target="_blank">
                            認証番号を取得
                        </a>


                        <p class="mt-5 mb-3">取得した番号を入力して「ログイン」してください。</p>

                        <form method="GET" action="{{route('auth.social.pin-auth',['provider'=>$provider])}}">
                                @csrf
                                <input name="provider" value="{{$provider}}" type="hidden" />
                                <input name="oauth_token" value="{{$oauth_token}}" type="hidden" />

                            <div class="row">
                                <div class="mb-4 col-12">
                                    <input name="oauth_verifier" value="" type="text" class="form-control" style=" font-size: 3em; text-align: center; "/>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-4">
                                    <a href="{{route('welcome')}}" class="btn btn-block btn-lg btn-secondary px-4" type="submit">キャンセル</a>
                                </div>

                                <div class="col-4 ml-auto">
                                    <button class="btn btn-block btn-lg btn-primary px-4" type="submit">ログインする</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
