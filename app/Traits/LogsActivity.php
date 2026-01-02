<?php

namespace App\Traits;

trait LogsActivity
{
    protected static function bootLogsActivity()
    {
        static::created(function ($model) {
            \activityLog(
                class_basename($model),
                'created',
                $model->id,
                null,
                $model->toArray()
            );
        });

        static::updated(function ($model) {
            \activityLog(
                class_basename($model),
                'updated',
                $model->id,
                $model->getOriginal(),
                $model->getChanges()
            );
        });

        static::deleted(function ($model) {
            \activityLog(
                class_basename($model),
                'deleted',
                $model->id,
                $model->toArray(),
                null
            );
        });
    }
}