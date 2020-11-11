<?php

namespace Kd9703\Eloquents\Twitter;

use DateTime;
use Kd9703\Constants\Notice\NoticeType;
use Kd9703\Eloquents\Model;

class Notice extends Model
{
    /**
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Carbon(DateTime)として扱うカラムを追加
     * @var array
     */
    // protected $dates = ['notified_at'];

    // このように
    protected function rules()
    {
        return [
            'id'              => 'nullable|integer',
            'account_id'      => 'filled|string',
            'notified_at'     => 'filled|date',
            'notice_type'     => 'nullable|in:' . implode(',', NoticeType::LIST),
            'title'           => 'nullable|string',
            'body'            => 'nullable|string',
            'from_account_id' => 'nullable|integer',
            'post_id'         => 'nullable|integer',
        ];
    }

    /**
     * @param  int     $id
     * @param  string  $username
     * @param  string  $fullname
     * @param  array   $options
     * @return mixed
     */
    public function upsert(?int $id, ?int $account_id, ?DateTime $notified_at, ?int $notice_type, ?string $title, ?string $body, ?int $from_account_id, ?int $post_id): self
    {
        $self = $this->getIfExists($id, $account_id, $notified_at, $notice_type, $title, $body, $from_account_id);
        $self = $self ?? new static();
        return $self->updateSelf($account_id, $notified_at, $notice_type, $title, $body, $from_account_id, $post_id);
    }

    /**
     * 同じ条件のレコードがあれば取得
     * @param  int      $account_id
     * @param  DateTime $notified_at
     * @param  int      $notice_type
     * @param  string   $title
     * @param  string   $body
     * @param  int      $from_account_id
     * @return mixed
     */
    public function getIfExists(?int $id, ?int $account_id, ?DateTime $notified_at, ?int $notice_type, ?string $title, ?string $body, ?int $from_account_id): ?self
    {
        // ID を持つレコードを準備
        $self = $id ? static::find($id) : null;

        // NOT NULL
        $from_account_id = $from_account_id ?: 0;

        // 重複レコードを探す
        $self = $self ?? static::where(compact('account_id', 'notified_at', 'notice_type', 'from_account_id'))->first();

        return $self;
    }

    /**
     * @param  int      $account_id
     * @param  DateTime $notified_at
     * @param  int      $notice_type
     * @param  string   $title
     * @param  string   $body
     * @param  int      $from_account_id
     * @return mixed
     */
    public function updateSelf(?int $account_id, ?DateTime $notified_at, ?int $notice_type, ?string $title, ?string $body, ?int $from_account_id, ?int $post_id): self
    {
        $data = compact('account_id', 'notified_at', 'notice_type', 'title', 'body', 'from_account_id', 'post_id');

        // データセット
        $this->fill($data);

        $this->save();

        return $this;
    }

    ///////////////////////////////////////////////
    // SCOPE
    // DBのインデックスと対応しているので他のScopeを組み合わせない

    /**
     * 対象者の通知一覧(日時順)や最終取得日など
     * INDEX KEY : USER_NOTIFIED_DATE
     */
    public function scopeOwnedBy($q, int $account_id)
    {
        $q->where('account_id', $account_id);
    }

}
