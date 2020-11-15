<?php

namespace App\Listeners;

use Kd9703\MediaBinder;

/**
 * サービスコンテナの結合メディアを切り替える
 * ユーザー情報が切り替わったときにコールされる
 */
class BindMedia
{
    /**
     * @var MediaBinder
     */
    protected $mediaBinder;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(
        MediaBinder $mediaBinder
    ) {
        $this->mediaBinder = $mediaBinder;
    }

    /**
     * Handle the event.
     *
     * @param  object $event
     * @return void
     */
    public function handle($event)
    {
        $account = $event->user->getAccount();

        if ($account) {
            $this->mediaBinder->bind($account);
        }
    }
}
