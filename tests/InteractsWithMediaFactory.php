<?php

namespace Tests;

use App\GloverMediaBinder;
use Glover\Constants\Media;
use Glover\MediaFactory;

trait InteractsWithMediaFactory
{
    /**
     * @param Media $media
     */
    protected function ApplyMeidaFactory(?Media $media)
    {
        $this->app->make(GloverMediaBinder::class)->bind($media);
    }

    /**
     * @param string $interface_name
     */
    protected function assertInterfaceBound(?Media $media, string $interface_name)
    {
        $media = $media ? $media->toValue() : Media::DEFAULT;

        $map = ($this->app->make(MediaFactory::class))($media);
        $this->assertContains($interface_name, array_keys($map), "$interface_name should be defined in MediaFactory. \n" . var_export(array_keys($map), true));

        $this->assertTrue($this->app->bound($interface_name), "$interface_name should be bound in the Service Container correctry.");
    }
}
