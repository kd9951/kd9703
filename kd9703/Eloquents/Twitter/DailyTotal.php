<?php

namespace Kd9703\Eloquents\Twitter;

use DateTime;
use Kd9703\Eloquents\Model;

class DailyTotal extends Model
{
    /**
     * @param  int      $from_account_id
     * @param  int      $to_account_id
     * @param  Datetime $date
     * @param  int      $initiator_id
     * @return mixed
     */
    public function upsert(int $account_id, DateTime $date, array $data): self
    {
        $self = static::where('account_id', $account_id)->whereDate('date', $date)->first();
        $self = $self ?? new static(['account_id' => $account_id, 'date' => $date]);
        $self->fill($data);
        $self->save();

        return $self;
    }
}
