<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;

trait HasCode
{
    public static function bootHasCode(): void
    {
        static::creating(function (Model $model): void {
            if (empty($model->code)) {
                $prefix = method_exists($model, 'codePrefix') ? $model->codePrefix() : '';
                $padding = method_exists($model, 'codePadding') ? $model->codePadding() : 0;
                
                $latest = static::query()->latest('id')->value('code');
                
                $latestNumber = 0;
                if ($latest) {
                    // Extract just the numbers from the previous code (e.g., 'ST-005' -> 5)
                    $latestNumber = (int) preg_replace('/[^0-9]/', '', $latest);
                }
                
                $nextNumber = (string) ($latestNumber + 1);
                
                if ($padding > 0) {
                    $nextNumber = str_pad($nextNumber, $padding, '0', STR_PAD_LEFT);
                }
                
                $model->code = $prefix . $nextNumber;
            }
        });
    }
}
