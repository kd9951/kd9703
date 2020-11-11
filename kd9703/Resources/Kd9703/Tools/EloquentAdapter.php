<?php
namespace Kd9703\Resources\Kd9703\Tools;

use Kd9703\Constants\Media;
use Kd9703\Eloquents\Model;

trait EloquentAdapter
{
    /**
     * @param Media  $media
     * @param string $model_name
     */
    protected function getEloquent(?Media $media, string $model_name): Model
    {
        switch ((string) $media) {
            case Media::TWITTER:
                $namespace = 'Kd9703\Eloquents\Twitter';
                break;
            default:
                $namespace = 'Kd9703\Eloquents\Kd9703';
                break;
        }

        $class = $namespace . '\\' . ucfirst($model_name);

        return new $class();
    }
}
