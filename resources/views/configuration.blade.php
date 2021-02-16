@extends('layouts.app')

@section('content')
        <div class="container">
            <form action="{{route('configuration.update')}}" method="post">
                <input name="_method" type="hidden" value="PUT">
                @csrf

                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-4">
                            <div>
                                <h4 class="card-title mb-0">拒否設定</h4>
                                <div class="small text-muted">このアプリを利用せず、他のメンバーからの利用を禁止する場合にセットしてください</div>
                            </div>
                        </div>
                        <div class="container">
                            <div class="row mb-3">
                                <div class="col-md-7">
                                    このアプリを通じて他のサロンメンバーが自動フォローすることを禁止する
                                    <div class="text-info mb-2"><small>現在、自動フォロー機能は実装されていないので将来的な機能です。</small></div>

                                </div>
                                <div class="col-md-5">
                                <input type="hidden" name="hidden_from_auto_follow" value="off">
                                <label class="c-switch c-switch-label c-switch-opposite-danger">
                                    <input class="c-switch-input" type="checkbox" {{$configuration->hidden_from_auto_follow ? 'checked="checked"' : ''}} name="hidden_from_auto_follow"><span class="c-switch-slider" data-checked="On" data-unchecked="Off"></span>
                                </label>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-7">
                                    検索にリストアップされないようにする
                                    <div class="text-info mb-2"><small>このアプリからは、アカウントは存在しない扱いになります。</small></div>
                                </div>
                                <div class="col-md-5">
                                <input type="hidden" name="hidden_from_search" value="off">
                                <label class="c-switch c-switch-label c-switch-opposite-danger">
                                    <input class="c-switch-input" type="checkbox" {{$configuration->hidden_from_search ? 'checked="checked"' : ''}} name="hidden_from_search"><span class="c-switch-slider" data-checked="On" data-unchecked="Off"></span>
                                </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-4">
                            <div>
                                <h4 class="card-title mb-0">表示設定</h4>
                                {{-- <div class="small text-muted">このアプリを利用せず、他のメンバーからの利用を禁止する場合にセットしてください</div> --}}
                            </div>
                        </div>
                        <div class="container">
                            <div class="row mb-3">
                                <div class="col-md-7">
                                    新着アカウント表示基準
                                    <div class="text-info mb-2"><small>
                                        {{config('app.name')}}では既存メンバーのフォロワさん等を辿って新メンバーを探しているため、
                                        Twitterのアカウント開設日と{{config('app.name')}}に掲載されるタイミングが異なります
                                        （そもそもTwitterアカウントを以前に作ったものを使っていることもありますし）。
                                        ダッシュボードの「新着」は、Twitterのアカウント開設日を基準にしています。</small></div>
                                </div>
                                <div class="col-md-5">
                                    @foreach(Kd9703\Constants\ShowNew::LABEL_JA as $key => $label)
                                    <div class="custom-control custom-radio mb-2">
                                        <input type="radio" id="show_new_by_{{$key}}" name="show_new_by" class="custom-control-input" value="{{$key}}"
                                        @if($key == $configuration->show_new_by) checked @endif>
                                        <label class="custom-control-label" for="show_new_by_{{$key}}">{{$label}}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-7">
                                    新着アカウント表示期間
                                    <div class="text-info mb-2"><small>
                                            ご自身の{{config('app.name')}}の「ログイン頻度・新着チェック頻度」に合わせておくと便利です。
                                        </small></div>
                                </div>
                                <div class="col-md-5">
                                    <div class="input-group">
                                        <input class="form-control" type="number" name="show_new_days" value="{{$configuration->show_new_days}}" placeholder="" autocomplete="off">
                                        <div class="input-group-append"><span class="input-group-text">日前から</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{--
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-4">
                            <div>
                                <h4 class="card-title mb-0">自動的にフォローリクエストを承認する</h4>
                                <div class="small text-muted">他のサロンメンバーさんからフォロー／フォローバックされたときに自動的に承認します</div>
                            </div>
                        </div>
                        <div class="container">
                            <div class="row mb-3">
                                <div class="col-md-7">
                                    <b>自動承認を有効にする</b>
                                    <div class="text-info mb-2"><small>対象のアカウントを自動的に承認します。対象外の場合はスルーしますので、不要であれば下の「自動拒否」も併用してください。</small></div>
                                </div>
                                <div class="col-md-5">
                                <input type="hidden" name="auto_follow_back" value="off">
                                <label class="c-switch c-switch-label c-switch-opposite-info">
                                    <input class="c-switch-input" type="checkbox" {{$configuration->auto_follow_back ? 'checked="checked"' : ''}} name="auto_follow_back"><span class="c-switch-slider" data-checked="On" data-unchecked="Off"></span>
                                </label>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-7">
                                    <b class="text-danger">自動拒否を有効にする</b>
                                    <div class="text-info mb-2"><small>自動承認と反対に、対象外のリクエストを削除します。フォローしてくれたメンバーさんは自分でチェックして自動承認したいが、メンバー以外（公開アカウントなど）からのリクエストは間違って承認しないために削除したいケースにご利用ください。</small></div>
                                </div>
                                <div class="col-md-5">
                                <input type="hidden" name="auto_reject" value="off">
                                <label class="c-switch c-switch-label c-switch-opposite-info">
                                    <input class="c-switch-input" type="checkbox" {{$configuration->auto_reject ? 'checked="checked"' : ''}} name="auto_reject"><span class="c-switch-slider" data-checked="On" data-unchecked="Off"></span>
                                </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-5 mb-4">
                            <div>
                                <h4 class="card-title mb-0">自動承認対象のアカウント</h4>
                                <div class="small text-muted">基本的には公式ルールの「鍵アカウントとアカウント名」に準じたアカウントのみが対象です。さらに条件を追加して対象アカウントを絞り込むことができます。</div>
                            </div>
                        </div>
                        <div class="container">
                            <div class="row mb-3">
                                <div class="col-md-7">
                                    １回以上ツイートしている
                                </div>
                                <div class="col-md-5">
                                <input type="hidden" name="follow_back_only_tweets_more_than" value="off">
                                <label class="c-switch c-switch-label c-switch-opposite-info">
                                    <input class="c-switch-input" type="checkbox" {{$configuration->follow_back_only_tweets_more_than ? 'checked="checked"' : ''}} name="follow_back_only_tweets_more_than"><span class="c-switch-slider" data-checked="On" data-unchecked="Off"></span>
                                </label>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-7">
                                    プロフィールにキーワードを含む
                                    <div class="text-info mb-2"><small>スペース区切りで複数のキーワードを指定でき、すべて含む場合のみ対象とします。</small></div>
                                </div>
                                <div class="col-md-5">
                                    <input class="form-control" type="text" name="follow_back_only_profile_contains" value="{{$configuration->follow_back_only_profile_contains}}" placeholder="" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                --}}

                <div class="container">
                    <div class="row">
                        <div class="col-md-3">
                            <a href="{{route('dashboard')}}" class="btn btn-default btn-block btn-lg">キャンセル</a>
                        </div>
                        <div class="offset-md-6 col-md-3">
                            <button type="submit" class="btn btn-primary btn-block btn-lg">保存</button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
@endsection

<?php /*
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-4">
                            <div>
                                <h4 class="card-title mb-0">自動的にフォローする</h4>
                                <div class="small text-muted">他のサロンメンバーさんのアカウントに自動的にフォローリクエストします</div>
                            </div>
                        </div>
                        <div class="container">
                            <div class="row mb-3">
                                <div class="col-md-7">
                                    <b>自動フォローを有効にする</b>
                                </div>
                                <div class="col-md-5">
                                <input type="hidden" name="auto_follow" value="off">
                                <label class="c-switch c-switch-label c-switch-opposite-info">
                                    <input class="c-switch-input" type="checkbox" {{$configuration->auto_follow ? 'checked="checked"' : ''}} name="auto_follow"><span class="c-switch-slider" data-checked="On" data-unchecked="Off"></span>
                                </label>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-7">
                                    1日のフォロー目標数
                                    <div class="text-info mb-2"><small>Twitterの仕様上、最大500件。アプリの力不足で達成できなくてもご容赦ください……。</small></div>
                                </div>
                                <div class="col-md-5">
                                    <input class="form-control" type="number" name="target_follow_per_day" value="{{$configuration->target_follow_per_day}}" placeholder="" autocomplete="off">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-7">
                                    おやすみ時間
                                    <div class="text-info mb-2"><small>フォローされる方に配慮して、夜間のフォローを停止する場合、その時刻を入力してください。</small></div>
                                </div>
                                <div class="col-md-5">
                                <div class="input-group">
                                    <input class="form-control" type="number" name="sleep_hour_start" value="{{$configuration->sleep_hour_start}}" placeholder="" autocomplete="off">
                                    <div class="input-group-append"><span class="input-group-text">時</span></div>
                                </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-7">
                                    活動開始時間
                                    <div class="text-info mb-2"><small>おやすみ時間を入力した場合、再開する時刻を入力してください。</small></div>
                                </div>
                                <div class="col-md-5">
                                <div class="input-group">
                                    <input class="form-control" type="number" name="sleep_hour_end" value="{{$configuration->sleep_hour_end}}" placeholder="" autocomplete="off">
                                    <div class="input-group-append"><span class="input-group-text">時</span></div>
                                </div>
                                </div>
                            </div>

                            {{-- <div class="row mb-3">
                                <div class="col-md-7">
                                    まだフォロー承認していないアカウントもフォロバする
                                    <div class="text-info mb-2"><small>このアプリからは、アカウントは存在しない扱いになります。</small></div>
                                </div>
                                <div class="col-md-5">
                                <label class="c-switch c-switch-label c-switch-opposite-info">
                                    <input class="c-switch-input" type="checkbox" {{$configuration->auto_follow_back_before_accept ? 'checked="checked"' : ''}} name="auto_follow_back_before_accept"><span class="c-switch-slider" data-checked="On" data-unchecked="Off"></span>
                                </label>
                                </div>
                            </div> --}}

                            {{-- <div class="row mb-3">
                                <div class="col-md-7">
                                    保存したリストのメンバーを自動的にフォローする
                                    <div class="text-info mb-2"><small>このアプリからは、アカウントは存在しない扱いになります。</small></div>
                                </div>
                                <div class="col-md-5">
                                <label class="c-switch c-switch-label c-switch-opposite-info">
                                    <input class="c-switch-input" type="checkbox" {{$configuration->auto_follow_in_my_list ? 'checked="checked"' : ''}} name="auto_follow_in_my_list"><span class="c-switch-slider" data-checked="On" data-unchecked="Off"></span>
                                </label>
                                </div>
                            </div> --}}

                            {{-- <div class="row mb-3">
                                <div class="col-md-7">
                                    リストを保存した他のアカウントも自動的にフォローする
                                    <div class="text-info mb-2"><small>このアプリからは、アカウントは存在しない扱いになります。</small></div>
                                </div>
                                <div class="col-md-5">
                                <label class="c-switch c-switch-label c-switch-opposite-info">
                                    <input class="c-switch-input" type="checkbox" {{$configuration->auto_follow_target_list ? 'checked="checked"' : ''}} name="auto_follow_target_list"><span class="c-switch-slider" data-checked="On" data-unchecked="Off"></span>
                                </label>
                                </div>
                            </div> --}}

                            {{-- <div class="row mb-3">
                                <div class="col-md-7">
                                    サロン垢村名簿のアカウントをフォロー
                                    <div class="text-info mb-2"><small>このアプリからは、アカウントは存在しない扱いになります。</small></div>
                                </div>
                                <div class="col-md-5">
                                <label class="c-switch c-switch-label c-switch-opposite-info">
                                    <input class="c-switch-input" type="checkbox" {{$configuration->auto_follow_members ? 'checked="checked"' : ''}} name="auto_follow_members"><span class="c-switch-slider" data-checked="On" data-unchecked="Off"></span>
                                </label>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                </div>


                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-4">
                            <div>
                                <h4 class="card-title mb-0">自動フォロー対象のアカウント</h4>
                                <div class="small text-muted">基本的には公式ルールに準じたアカウントのみが対象です。さらに条件を追加して対象アカウントを絞り込むことができます。</div>
                            </div>
                        </div>
                        <div class="container">
                            <div class="row mb-3">
                                <div class="col-md-7">
                                    アイコン画像（アバター）を設定している
                                </div>
                                <div class="col-md-5">
                                <input type="hidden" name="follow_only_set_icon" value="off">
                                <label class="c-switch c-switch-label c-switch-opposite-info">
                                    <input class="c-switch-input" type="checkbox" {{$configuration->follow_only_set_icon ? 'checked="checked"' : ''}} name="follow_only_set_icon"><span class="c-switch-slider" data-checked="On" data-unchecked="Off"></span>
                                </label>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-7">
                                    １回以上ツイートしている
                                </div>
                                <div class="col-md-5">
                                <input type="hidden" name="follow_only_tweets_more_than" value="off">
                                <label class="c-switch c-switch-label c-switch-opposite-info">
                                    <input class="c-switch-input" type="checkbox" {{$configuration->follow_only_tweets_more_than ? 'checked="checked"' : ''}} name="follow_only_tweets_more_than"><span class="c-switch-slider" data-checked="On" data-unchecked="Off"></span>
                                </label>
                                </div>
                            </div>

                            {{-- <div class="row mb-3">
                                <div class="col-md-7">
                                    必要な
                                    <div class="text-info mb-2"><small>Twitterの仕様上、最大500件。アプリの力不足で達成できなくてもご容赦ください……。</small></div>
                                </div>
                                <div class="col-md-5">
                                    <input class="form-control" type="number" name="follow_only_tweets_more_than" value="{{$configuration->follow_only_tweets_more_than}}" placeholder="" autocomplete="off">
                                </div>
                            </div>
 --}}
                            <div class="row mb-3">
                                <div class="col-md-7">
                                    プロフィールにキーワードを含む
                                    <div class="text-info mb-2"><small>スペース区切りで複数のキーワードを指定でき、すべて含む場合のみ対象とします。</small></div>
                                </div>
                                <div class="col-md-5">
                                    <input class="form-control" type="text" name="follow_only_profile_contains" value="{{$configuration->follow_only_profile_contains}}" placeholder="" autocomplete="off">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-7">
                                    フォローしなかったアカウントを再確認するまでの日数
                                    <div class="text-info mb-2"><small>ゼロにすると無期限となり、２回以上フォロー申請しません。</small></div>
                                </div>
                                <div class="col-md-5">
                                <div class="input-group">
                                    <input class="form-control" type="number" name="follow_again_in_days" value="{{$configuration->follow_again_in_days}}" placeholder="" autocomplete="off">
                                    <div class="input-group-append"><span class="input-group-text">日</span></div>
                                </div>
                                </div>
                            </div>

                            {{-- 'follow_only_keyword_contains_1'       => ['string', ''], // このキーワードセットに該当するアカウントのみフォローする --}}
                            {{-- 'follow_only_keyword_contains_2'       => ['string', ''], // このキーワードセットに該当するアカウントのみフォローする --}}
                            {{-- 'follow_only_keyword_contains_3'       => ['string', ''], // このキーワードセットに該当するアカウントのみフォローする --}}
                        </div>
                    </div>
                </div>


                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-4">
                            <div>
                                <h4 class="card-title mb-0">自動的にフォローリクエストを承認する</h4>
                                <div class="small text-muted">他のサロンメンバーさんからフォロー／フォローバックされたときに自動的に承認します</div>
                            </div>
                        </div>
                        <div class="container">
                            <div class="row mb-3">
                                <div class="col-md-7">
                                    <b>自動承認を有効にする</b>
                                    <div class="text-info mb-2"><small>対象のアカウントを自動的に承認します。対象外の場合はスルーしますので、不要であれば下の「自動拒否」も併用してください。</small></div>
                                </div>
                                <div class="col-md-5">
                                <input type="hidden" name="auto_follow_back" value="off">
                                <label class="c-switch c-switch-label c-switch-opposite-info">
                                    <input class="c-switch-input" type="checkbox" {{$configuration->auto_follow_back ? 'checked="checked"' : ''}} name="auto_follow_back"><span class="c-switch-slider" data-checked="On" data-unchecked="Off"></span>
                                </label>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-7">
                                    <b class="text-danger">自動拒否を有効にする</b>
                                    <div class="text-info mb-2"><small>自動承認と反対に、対象外のリクエストを削除します。フォローしてくれたメンバーさんは自分でチェックしたいが、メンバー以外（公開アカウントなど）からのリクエストは間違って承認しないために削除したいケースにご利用ください。</small></div>
                                </div>
                                <div class="col-md-5">
                                <input type="hidden" name="auto_reject" value="off">
                                <label class="c-switch c-switch-label c-switch-opposite-info">
                                    <input class="c-switch-input" type="checkbox" {{$configuration->auto_reject ? 'checked="checked"' : ''}} name="auto_reject"><span class="c-switch-slider" data-checked="On" data-unchecked="Off"></span>
                                </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-4">
                            <div>
                                <h4 class="card-title mb-0">自動承認対象のアカウント</h4>
                                <div class="small text-muted">基本的には公式ルールに準じたアカウントのみが対象です。さらに条件を追加して対象アカウントを絞り込むことができます。</div>
                            </div>
                        </div>
                        <div class="container">
                            <div class="row mb-3">
                                <div class="col-md-7">
                                    アイコン画像（アバター）を設定している
                                </div>
                                <div class="col-md-5">
                                <input type="hidden" name="follow_back_only_set_icon" value="off">
                                <label class="c-switch c-switch-label c-switch-opposite-info">
                                    <input class="c-switch-input" type="checkbox" {{$configuration->follow_back_only_set_icon ? 'checked="checked"' : ''}} name="follow_back_only_set_icon"><span class="c-switch-slider" data-checked="On" data-unchecked="Off"></span>
                                </label>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-7">
                                    １回以上ツイートしている
                                </div>
                                <div class="col-md-5">
                                <input type="hidden" name="follow_back_only_tweets_more_than" value="off">
                                <label class="c-switch c-switch-label c-switch-opposite-info">
                                    <input class="c-switch-input" type="checkbox" {{$configuration->follow_back_only_tweets_more_than ? 'checked="checked"' : ''}} name="follow_back_only_tweets_more_than"><span class="c-switch-slider" data-checked="On" data-unchecked="Off"></span>
                                </label>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-7">
                                    プロフィールにキーワードを含む
                                    <div class="text-info mb-2"><small>スペース区切りで複数のキーワードを指定でき、すべて含む場合のみ対象とします。</small></div>
                                </div>
                                <div class="col-md-5">
                                    <input class="form-control" type="text" name="follow_back_only_profile_contains" value="{{$configuration->follow_back_only_profile_contains}}" placeholder="" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-4">
                            <div>
                                <h4 class="card-title mb-0">フォローしている／されているアカウントがルールに準じているかチェックする</h4>
                                <div class="small text-muted">アカウントの状態は変化します。ルールにマッチしなくなったアカウントをフォロー解除します。</div>
                            </div>
                        </div>
                        <div class="container">
                            <div class="row mb-3">
                                <div class="col-md-7">
                                    フォローしているアカウントが「自動フォロー」の対象でなければフォロー解除する
                                </div>
                                <div class="col-md-5">
                                <input type="hidden" name="check_following_regulation" value="off">
                                <label class="c-switch c-switch-label c-switch-opposite-info">
                                    <input class="c-switch-input" type="checkbox" {{$configuration->check_following_regulation ? 'checked="checked"' : ''}} name="check_following_regulation"><span class="c-switch-slider" data-checked="On" data-unchecked="Off"></span>
                                </label>
                                </div>
                            </div>

                            {{-- <div class="row mb-3">
                                <div class="col-md-7">
                                    フォロワーが「自動承認」の対象でなければフォロー解除する
                                </div>
                                <div class="col-md-5">
                                <input type="hidden" name="check_follower_regulation" value="off">
                                <label class="c-switch c-switch-label c-switch-opposite-info">
                                    <input class="c-switch-input" type="checkbox" {{$configuration->check_follower_regulation ? 'checked="checked"' : ''}} name="check_follower_regulation"><span class="c-switch-slider" data-checked="On" data-unchecked="Off"></span>
                                </label>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                </div>
*/ ?>
