<?php
namespace Kd9703\Resources\Kd9703\Notice;

use Kd9703\Constants\Media;
use Kd9703\Entities\Media\Account;
use Kd9703\Entities\Media\Notice as NoticeEntity;
use Kd9703\Entities\Media\Notices;
use Kd9703\Resources\Kd9703\Tools\EloquentAdapter;
use Kd9703\Resources\Interfaces\Account\Account as AccountResource;
use Kd9703\Resources\Interfaces\Notice\Notice as NoticeInterface;

/**
 * 通知
 */
class Notice implements NoticeInterface
{
    use EloquentAdapter;

    /**
     * @param Follow $follow
     */
    public function __construct(AccountResource $AccountResource)
    {
        $this->Resources['Account'] = $AccountResource;
    }

    const NOTICE_COLUMNS = [
        // 'media',
        'notice_id',
        'account_id',
        // 'account',
        'from_account_id',
        // 'from_account',
        'notified_at',
        'notice_type',
        'title',
        'body',
        'post_id',
        // 'post',
    ];

    /**
     * 最新の通知を1件取得
     * 1件も取得されてないときはNULL
     *
     * @param  Account $account
     * @return void
     */
    public function getLatest(Account $account): ?NoticeEntity
    {
        $eloquent = $this->getEloquent($account->media, 'Notice');

        $notice = $eloquent->where('account_id', $account->account_id)
            ->select(self::NOTICE_COLUMNS)
            ->orderBy('notified_at', 'desc')->first();

        if (!$notice) {
            return null;
        }

        $notice = new NoticeEntity($notice->toArray());

        $notice->media           = $account->media;
        $notice->account         = $account;
        $notice->from_account_id = $notice->from_account_id;
        $notice->from_account    = $notice->from_account_id ? $this->Resources['Account']->getOne($notice->media, $notice->from_account_id) : null;

        return $notice;
    }

    /**
     * シンプルに単一のタグ情報を永続化する
     *
     * @param  Account $account
     * @return void
     */
    public function storeOne(NoticeEntity $notice): void
    {
        // TODO バリデーションルール タグの保存可能数とかも
        if (!isset($notice->account_id)) {
            return;
        }

        $eloquent = $this->getEloquent($notice->media, 'Notice');

        if ($notice->notice_id) {
            $model = $eloquent->find($notice->notice_id);
        } else {
            $query = $eloquent->query();
            $query = $query->where('notified_at', $notice->notified_at);
            $query = $query->where('notice_type', $notice->notice_type);
            $query = $query->where('notified_at', $notice->notified_at);
            $query = $query->where('account_id', $notice->account_id);
            if ($notice->from_account_id) {
                $query = $query->where('from_account_id', $notice->from_account_id);
            } else {
                $query = $query->whereNull('from_account_id');
            }
            $model = $query->first();
        }

        $model = $model ?: $eloquent;

        foreach (self::NOTICE_COLUMNS as $column) {
            if (!is_null($notice->$column)) {
                $model->$column = $notice->$column;
            }
        }

        $model->save();
    }

    /**
     * 複数一括のタグ情報を永続化する
     *
     * @param  Account $account
     * @return void
     */
    public function storeList(Notices $notices): void
    {
        // FIXME ループしてたら一括する意味ないよね
        foreach ($notices as $notice) {
            $this->storeOne($notice);
        }
    }
}
