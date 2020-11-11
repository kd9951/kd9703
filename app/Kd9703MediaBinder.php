<?php

namespace App;

use Illuminate\Foundation\Application;
use Kd9703\Constants\Media;
use Kd9703\MediaBinder;
use Kd9703\MediaFactory;

class Kd9703MediaBinder implements MediaBinder
{
    /**
     * @param string $media
     */
    public function bind(?Media $media)
    {
        $app     = Application::getInstance();
        $factory = new MediaFactory();

        $media = $media ? $media->toValue() : Media::DEFAULT;

        foreach ($factory($media) as $abstract => $concrete) {
            $app->singleton($abstract, $concrete);
        }
    }
}
