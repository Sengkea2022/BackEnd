<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;

trait HasSequentialNumber
{
    abstract protected function numberColumn(): string;

    public static function bootHasSequentialNumber(): void
    {
        static::creating(function (Model $model): void {
            $column = $model->numberColumn();

            if (empty($model->{$column})) {
                $latestNumber = static::query()->latest('id')->value($column);
                $model->{$column} = (string) (((int) ($latestNumber ?? 0)) + 1);
            }
        });
    }
}
