<?php

namespace App;

use Illuminate\Foundation\Application;
use Kd9703\Constants\Media;
use Kd9703\Entities\Media\Account;
use Kd9703\MediaBinder;
use Kd9703\MediaFactory;

class Kd9703MediaBinder implements MediaBinder
{
    /**
     * @param string $media
     */
    public function bind(?Account $account)
    {
        $app     = Application::getInstance();
        $factory = new MediaFactory();

        $media = $account ? $account->media : Media::DEFAULT;

        foreach ($factory($media) as $abstract => $concrete) {
            // TWITTER API にアカウントのトークンを渡す
            if ($media == Media::TWITTER && is_a($concrete, \Crawler\HttpClients\TwitterApi::class, true)) {
                $concrete = app($concrete);
                $concrete->setToken(
                    $account->account_id,
                    config('services.twitter.client_id'),
                    config('services.twitter.client_secret'),
                    $account->oauth_access_token,
                    $account->oauth_access_secret->getPlainPassword()
                );
                $app->instance($abstract, $concrete);
                continue;
            }

            $app->singleton($abstract, $concrete);
        }
    }
}
