<?php

namespace Kd9703\Eloquents\Twitter;

use Datetime;
use Kd9703\Constants\Follow\FollowedBackType;
use Kd9703\Constants\Follow\FollowMethod;
use Kd9703\Eloquents\Model;

class Follow extends Model
{
    /**
     * @var array
     */
    protected $guarded = ['id'];
    /**
     * Carbon(DateTime)として扱うカラムを追加
     * @var array
     */
    // protected $dates = ['followed_at', 'followed_back_at', 'unfollowed_at'];

    /**
     * @param  int      $from_account_id
     * @param  int      $to_account_id
     * @param  Datetime $date
     * @param  int      $initiator_id
     * @return mixed
     */
    public function follow(string $from_account_id, string $to_account_id, Datetime $date, int $initiator_id = null): ?self
    {
        $self = static::follows($from_account_id, $to_account_id)->first();
        if ($self !== null) {
            throw new \LogicException('すでにフォローしています');
        }

        // フォローを記録
        $self = static::create([
            'from_account_id' => $from_account_id,
            'to_account_id'   => $to_account_id,
            'followed_at'     => $date,
            'follow_method'   => $initiator_id,
        ]);

        // // フォローしたときにはすでにフォローされていた。相互フォロー成立。
        $back = static::follows($to_account_id, $from_account_id)->first();
        if ($back !== null) {
            $back->followed_back    = true;
            $back->followed_back_at = $date;
            $back->save();
            $self->followed_back    = true;
            $self->followed_back_at = $date;
            $self->save();
        }

        return $self;
    }

    /**
     * @param  int      $from_account_id
     * @param  int      $to_account_id
     * @param  Datetime $date
     * @return mixed
     */
    public function followBack(string $from_account_id, string $to_account_id, Datetime $date): ?self
    {
        $self = static::follows($from_account_id, $to_account_id)->first();
        if ($self !== null) {
            return $self;
            // throw new \LogicExcetpion('すでにフォローバックしています');
        }

        // 逆フォロー（元フォロー）を探す
        $src = static::follows($to_account_id, $from_account_id)->first();

        if ($src === null) {
            // 無いなら自然増
            $type = FollowedBackType::ORGANIC;
        } else {
            // あればそのイニシエータによってバックのタイプを記録
            switch ($src->follow_method) {
                case FollowMethod::MANUAL;
                    $type = FollowedBackType::MANUAL;
                    break;
                case FollowMethod::AUTO;
                    $type = FollowedBackType::AUTO;
                    break;
                default:
                    throw new \LogicException('もとのフォローのイニシエータが不明');
                    // $type = 0;
            }
        }

        // フォローバックを記録
        $self = static::create([
            'from_account_id'    => $from_account_id,
            'to_account_id'      => $to_account_id,
            'followed_at'        => $date,
            'followed_back_type' => $type,
        ]);

        // 逆フォローでもフォローバックを記録「相互フォロー成立」
        if ($src !== null) {
            $src->followed_back    = true;
            $src->followed_back_at = $self->followed_at;
            $src->save();
            $self->followed_back    = true;
            $self->followed_back_at = $src->followed_at;
            $self->save();
        }

        return $self;
    }

    /**
     * @param int      $from_account_id
     * @param int      $to_account_id
     * @param Datetime $date
     * @param int      $initiator_id
     */
    public function unfollow(string $from_account_id, string $to_account_id, Datetime $date, int $initiator_id = null): bool
    {
        $self = static::follows($from_account_id, $to_account_id)->first();
        if ($self === null) {
            // エラー出さずに黙殺でも良いかもしれない
            return true;
            throw new \LogicExcetpion('フォローしていません');
        }

        $self->unfollowed        = true;
        $self->unfollowed_at     = $date;
        $self->unfollowed_method = $initiator_id;
        $self->save();

        // 逆フォローを探す
        $back = static::follows($to_account_id, $from_account_id)->first();

        if ($back !== null) {
            // あれば相互フォローを解除
            $self->followed_back = false;
            $self->save();
            $back->followed_back = false;
            $back->save();
        }

        return true;
    }

    ///////////////////////////////////////////////
    // SCOPE
    // DBのインデックスと対応しているので他のScopeを組み合わせない

    /**
     * EXISTS
     */
    public function scopeFollows($q, string $from_account_id, string $to_account_id)
    {
        $q->where('unfollowed', 0)->where('from_account_id', $from_account_id)->where('to_account_id', $to_account_id);
    }

    /**
     * EXISTS
     */
    public function scopeOwnedBy($q, string $account_id)
    {
        $q->where('unfollowed', 0)->where('from_account_id', $account_id);
    }

    /**
     * EXISTS
     */
    public function scopeFollowedBy($q, string $account_id, string $follower_id)
    {
        $q->where('unfollowed', 0)->where('from_account_id', $follower_id)->where('to_account_id', $account_id);
    }

    /**
     * 有効なフォロー（フォロー数）
     * FOLLOW_COUNT
     */
    public function scopeCurrentFollow($q, string $account_id)
    {
        $q->where('unfollowed', 0)->where('from_account_id', $account_id);
    }

    /**
     * 有効なフォローバック（フォロワ数）
     * FOLLOWER_COUNT
     */
    public function scopeCurrentFollower($q, string $account_id)
    {
        $q->where('unfollowed', 0)->where('to_account_id', $account_id);
    }

    /**
     * その日にフォローした
     * FOLLOW_DAILY_COUNT
     */
    public function scopeFollowedIn($q, string $account_id, DateTime $date)
    {
        $q->where('from_account_id', $account_id)->whereDate('followed_at', $date);
    }

    /**
     * その日にフォローして規定期間内にフォローバックされた
     * FOLLOW_DAILY_COUNT
     */
    public function scopeFollowedAndBackIn($q, string $account_id, DateTime $date)
    {
        $q->followedIn($account_id, $date)->whereDate('followed_back_at', '>=', $date);
    }

    /**
     * その日にフォローされた
     * FOLLOWER_DAILY_COUNT
     */
    public function scopeFollowedBackIn($q, string $account_id, DateTime $date)
    {
        $q->where('to_account_id', $account_id)->whereDate('followed_at', $date);
    }

    /**
     * その日にフォロー解除した
     * UNFOLLOW_DAILY_COUNT
     */
    public function scopeUnfollowedIn($q, string $account_id, DateTime $date)
    {
        $q->where('from_account_id', $account_id)->whereDate('unfollowed_at', $date);
    }
}
