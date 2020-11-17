@extends('layouts.center')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Lexend+Deca&display=swap" rel="stylesheet">

<style>
.jumbotron-fluid {
    width: 100%;
    background: #ffffffd6;
    box-shadow: 0 0 20px 2px #49b6f7;
}
.display-4 {
    font-family: 'Lexend Deca', sans-serif;
    text-shadow: 0 0 15px white;
}
.lead {
    font-weight: bold;
}
body {
    background-color: #00aced !important;
    background-image: url(assets/bg.jpg);
    background-position: center top;
    background-size: cover;
    font-family: 'Noto Sans JP', sans-serif;
}
</style>

        <div class="jumbotron jumbotron-fluid">
        <div class="container">
            <h1 class="display-4">SALON TWITTER MANAGER</h1>
            <p class="lead mb-5">西野亮廣エンタメ研究所専用Twitterアカウント検索サービス</p>

            <p class="mb-5">
                サロンメンバーさんの鍵アカウントを検索できるサービスを目指し、現在開発中のWEBサービスです。
                <br>
                人気、著名、アクティブに活動しているアカウントを見つけたり、自分の興味関心に近いアカウントをおすすめしたり、見つけたアカウントやおすすめのアカウントを自動的にフォローする機能も搭載していければと考えています。<br>
            </p>
            <a href="{{route('auth.social.login',['provider'=>'twitter'])}}" class="btn btn-lg btn-block btn-twitter mb-5" type="button">Twitterでログイン または 登録して利用開始</a>

            <div class="card p-3 bg-warning text-white text-center">
                <h5 class="mb-3">このツールは現在開発中です</h5>
                <p style="font:90%">ログインしてご利用いただくことはできますが、びっくりするくらい機能がないし、動かない画面やボタンがあったりします。ログインされる場合は、その点、ご理解・ご了承くださいませ。</p>
                <p style="font:90%">なにかお気づきの点や、トラブル等ありましたら、開発者Twitterアカウント <a class="text-white" href="https://twitter.com/salonkentarohar"><b>@salonkentarohar</b></a> やGitHub <a class="text-white" href="https://github.com/kd9951/kd9703"><b>kd9951/kd9703</b></a> にご一報いただけますようお願いします。</p>
            </div>

            <h5 class="mt-5">使い方とポイント</h5>

            <ul class="list-unstyled mt-3">
                <li class="mb-2">追加情報は不要です。ワンクリックで登録が完了します。</li>
                <li class="mb-2">ログインに利用されたTwitterアカウントに対して、なにか操作したり、投稿したり、お知らせを通知したりといったことは<span class="text-danger"><b>一切おこないません。</b></span></li>
                <li class="mb-2">複数のTwitterアカウントをお持ちの方は、先に「サロン用アカウント」でログインしておいてください。</li>
                <li class="mb-2"><span class="text-danger"><b>リストから除外してほしい（自動フォローや検索してほしくない）</b></span>場合は、そのアカウントで登録して、「除外」オプションをセットしてください。</li>
            </ul>

            <h5 class="mt-5">サロン専用Twitter関連情報</h5>

            <ul class="list-unstyled mt-3">
                <li class="mb-2"><a href="https://shojiki.jp/nisihinosalon-twitter/">西野サロン『サロン垢』まとめ</a> ショージキ株式会社さん</li>
                <li class="mb-2"><a href="https://twitter.com/salon_ukai2">@salon_ukai2</a> サロン垢自動フォローツールとアカウント一覧・テーマ別リスト 鵜飼さん</li>
            </ul>

            <h5 class="mt-5">お問い合わせ</h5>

            <p>開発状況ややっていることなどをTwitterにて配信しています。フォローしていただけると励みになります。</p>

            <ul class="list-unstyled mt-3">
                <li class="mb-2"><b><a href="https://twitter.com/salonkentarohar">@salonkentarohar</a> 開発・管理運営 原田（<a href="https://letterpot.otogimachi.jp/users/83058">レターポット</a>）</b></li>
                <li class="mb-2"><b><a class="" href="https://github.com/kd9951/kd9703"><b>kd9951/kd9703</b></a> GitHubレポジトリ</b></li>
            </ul>

        </div>
        </div>
@endsection
