<?php

namespace Kd9703\Eloquents\Twitter;

use Carbon\Carbon;
use Kd9703\Constants\LoginMethod;
use Kd9703\Constants\Prefecture;
use Kd9703\Eloquents\Model;

class Account extends Model
{
    /**
     * IDの自動インクリメントを解除
     * @var mixed
     */
    public $incrementing = false;
    /**
     * @var string
     */
    protected $keyType = 'string';

    /**
     * @var array
     */
    // protected $guarded = ['account_id', 'username', 'fullname'];
    protected $fillable = [
        // 'account_id',
        // 'username',
        // 'fullname',
        // 'login_method',
        // 'login_id', これと
        // 'password', これの追加更新は、upsertのオプション あえて隠してる
        'is_private',
        'prefecture',
        'web_url1',
        'web_url2',
        'web_url3',
        'img_thumnail_url',
        'img_cover_url',
        // 'score',
        'count_posts',
        'count_follow',
        'count_follower',
        'last_posted_at',
        'count_likes',
        // 'reviewed_at',
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    // protected $hidden = ['login_id', 'password'];
    /**
     * Carbon(DateTime)として扱うカラムを追加
     * @var array
     */
    // protected $dates = ['last_posted_at'];

    // このように
    protected function rules()
    {
        return [
            'account_id'                => $this->isNew() ? 'required|string' : 'nullable|string',
            'username'                  => 'nullable|string', //|min:1', // TODO nullは許すが空文字は許さないルールにできない
            'fullname'                  => 'nullable|string', //|min:1',
            'login_method'              => 'nullable|in:0,' . implode(',', LoginMethod::LIST),
            'login_id'                  => 'nullable|string',
            'password'                  => 'nullable|string',
            'prefecture'                => 'nullable|in:0,' . implode(',', Prefecture::LIST),
            'web_url1'                  => 'nullable|string',
            'web_url2'                  => 'nullable|string',
            'web_url3'                  => 'nullable|string',
            'img_thumnail_url'          => 'nullable|string',
            'img_cover_url'             => 'nullable|string',
            'score'                     => 'nullable|integer',
            'total_post'                => 'nullable|integer',
            'total_follow'              => 'nullable|integer',
            'total_follower'            => 'nullable|integer',
            'last_posted_at'            => 'nullable|date',
            'total_likes'               => 'nullable|integer',
            'reviewed_at'               => 'nullable|date',
            'reviewed_as_using_user_at' => 'nullable|date',
            'status_updated_at'         => 'nullable|date',
        ];
    }

    ///////////////////////////////////////////////
    // GET

    /**
     * 自分自身のアカウントではない別のユーザー情報を参照する
     * 安全のためパスワード情報を落とす
     * TODO こちらをデフォルトの挙動にしたほうが良いと思う
     *
     * @param  integer $id
     * @return void
     */
    public static function findSafety(int $id): self
    {
        $user = static::find($id);
        unset($user->login_method);
        unset($user->login_id);
        unset($user->password);
        return $user;
    }

    ///////////////////////////////////////////////
    // UPDATE

    ///////////////////////////////////////////////
    // ACCESSER / MUTATOR

    /**
     * ログインIDを複合
     * @param $value
     */
    public function getLoginIdAttribute($value)
    {
        return $value == null ? null : decrypt($value);
    }

    /**
     * ログインIDを暗号化
     * @param $value
     */
    public function setLoginIdAttribute($value)
    {
        $this->attributes['login_id'] = encrypt($value);
    }

    /**
     * パスワードを複合
     * @param $value
     */
    public function getPasswordAttribute($value)
    {
        return $value == null ? null : decrypt($value);
    }

    /**
     * パスワードを暗号化
     * @param $value
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = encrypt($value);
    }

    /**
     * ステータスの更新フラグ
     * @param $value
     */
    public function setTotalPostAttribute($value)
    {
        if (($this->attributes['total_post'] ?? 0) < $value) {
            $this->attributes['status_updated_at'] = Carbon::now()->format('Y-m-d H:i:s');
        }
        $this->attributes['total_post'] = $value;
    }

    /**
     * ステータスの更新フラグ
     * @param $value
     */
    public function setTotalFollowAttribute($value)
    {
        if (($this->attributes['total_follow'] ?? 0) < $value) {
            $this->attributes['status_updated_at'] = Carbon::now()->format('Y-m-d H:i:s');
        }
        $this->attributes['total_follow'] = $value;
    }

    /**
     * ステータスの更新フラグ
     * @param $value
     */
    public function setTotalFollowerAttribute($value)
    {
        // if (($this->attributes['total_follower'] ?? 0) < $value) {
        //     $this->attributes['status_updated_at'] = Carbon::now()->format('Y-m-d H:i:s');
        // }
        $this->attributes['total_follower'] = $value;
    }

    /**
     * ステータスの更新フラグ
     * @param $value
     */
    public function setTotalLikesAttribute($value)
    {
        if (($this->attributes['total_likes'] ?? 0) < $value) {
            $this->attributes['status_updated_at'] = Carbon::now()->format('Y-m-d H:i:s');
        }
        $this->attributes['total_likes'] = $value;
    }

}
