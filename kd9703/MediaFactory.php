<?php

namespace Kd9703;

use Kd9703\Constants\Media;
use Kd9703\Entities\Media\Account;

/**
 * アプリ内で指定しているインターフェースとそのアプリ内の実体との対応表
 * あくまで参考情報であって、完璧じゃない。
 * 実際にどうやってインスタンスを初期化してインジェクトするかはフレームワークのしごと。
 */
class MediaFactory
{
    const MEDIA = [
        Media::DEFAULT=> [

            /////////////////////////////////////////////////////////////////////////////////////
            \Kd9703\Resources\Interfaces\Transaction::class                      => \Kd9703\Resources\Kd9703\Transaction::class,
            \Kd9703\Resources\Interfaces\Account\Account::class                  => \Kd9703\Resources\Kd9703\Account\Account::class,
            \Kd9703\Resources\Interfaces\Account\FollowerAccount::class          => \Kd9703\Resources\Kd9703\Account\FollowerAccount::class,
            \Kd9703\Resources\Interfaces\Account\FollowingAccount::class         => \Kd9703\Resources\Kd9703\Account\FollowingAccount::class,
            \Kd9703\Resources\Interfaces\Account\FollowingProposedAccount::class => \Kd9703\Resources\Kd9703\Account\FollowingProposedAccount::class,
            \Kd9703\Resources\Interfaces\Analyze\DailyTotal::class               => \Kd9703\Resources\Kd9703\Analyze\DailyTotal::class,

            \Kd9703\Resources\Interfaces\Tag\Tag::class         => \Kd9703\Resources\Kd9703\Tag\Tag::class,
            \Kd9703\Resources\Interfaces\Tag\WatchingTag::class => \Kd9703\Resources\Kd9703\Tag\WatchingTag::class,

            \Kd9703\Resources\Interfaces\Follow\Follower::class  => \Kd9703\Resources\Kd9703\Follow\Follower::class,
            \Kd9703\Resources\Interfaces\Follow\Following::class => \Kd9703\Resources\Kd9703\Follow\Following::class,

            \Kd9703\Resources\Interfaces\Notice\Notice::class => \Kd9703\Resources\Kd9703\Notice\Notice::class,
            \Kd9703\Resources\Interfaces\Like\Like::class     => \Kd9703\Resources\Kd9703\Like\Like::class,

            \Kd9703\Resources\Interfaces\Owner\Configuration::class => \Kd9703\Resources\Kd9703\Owner\Configuration::class,
            \Kd9703\Resources\Interfaces\Job\Job::class             => \Kd9703\Resources\Kd9703\Job\Job::class,

            /////////////////////////////////////////////////////////////////////////////////////
            \Crawler\HttpClientInterface::class           => null,
            \Crawler\ParserInterface::class               => null,
            \Kd9703\Logger\Interfaces\SystemLogger::class => \Kd9703\Logger\SystemLogger::class,
            \Kd9703\Logger\Interfaces\OwnerLogger::class  => \Kd9703\Logger\OwnerLogger::class,
        ],

        Media::TWITTER => [
            \Crawler\HttpClientInterface::class => \Crawler\HttpClients\Goutte::class,
            \Crawler\ParserInterface::class     => \Crawler\Parsers\DomCrawler::class,
        ],
    ];

    /**
     * @param string $media
     */
    public function __invoke(string $media, ?Account $account = null)
    {
        return array_merge(
            self::MEDIA[Media::DEFAULT],
            self::MEDIA[$media] ?? []
        );
    }
}
