<?php
namespace Kd9703\Resources\Kd9703\Like;

use Kd9703\Constants\Media;
use Kd9703\Constants\Notice\NoticeType;
use Kd9703\Entities\Media\Account;
use Kd9703\Resources\Kd9703\Tools\EloquentAdapter;
use Kd9703\Resources\Interfaces\Like\Like as LikeInterface;

/**
 * いいね
 */
class Like implements LikeInterface
{
    use EloquentAdapter;

    /**
     * その日のいいね数を取得
     *
     * @param  Account   $account
     * @param  string    $date      集計対象日 なければ開始からの総計
     * @return integer
     */
    public function getCount(Account $account, string $date = null): int
    {
        // 本当はLikeテーブルを使いたいけど無いので実装はNoticeを見る
        $eloquent = $this->getEloquent($account->media, 'Notice');
        $eloquent = $eloquent->where('account_id', $account->account_id);
        $eloquent = $eloquent->whereIn('notice_type', [NoticeType::LIKED_COMMENT, NoticeType::LIKED_POST]);

        // その日
        if ($date) {
            $date     = date('Y-m-d', strtotime($date));
            $eloquent = $eloquent->whereBetween('notified_at', ["$date 00:00:00", "$date 23:59:59"]);
        }

        // TODO トータルは前日からの差分でいいはず

        return $eloquent->count();
    }
}
