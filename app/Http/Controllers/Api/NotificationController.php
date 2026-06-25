<?php

namespace App\Http\Controllers\Api;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class NotificationController extends ApiResourceController
{
    protected string $modelClass = Notification::class;

    protected function rules(?Model $record = null): array
    {
        return [
            'uuid' => [
                'sometimes',
                'uuid',
                Rule::unique('notifications', 'uuid')->ignore($record?->id),
            ],
            'user_uuid' => [
                $record ? 'sometimes' : 'required',
                'uuid',
                'exists:users,uuid',
            ],
            'order_uuid' => [
                $record ? 'sometimes' : 'required',
                'uuid',
                'exists:orders,uuid',
            ],
            'type' => [
                $record ? 'sometimes' : 'required',
                'string',
                'max:100',
            ],
            'title' => [
                $record ? 'sometimes' : 'required',
                'string',
                'max:255',
            ],
            'body' => [
                $record ? 'sometimes' : 'required',
                'string',
            ],
            'read_at' => [
                'nullable',
                'date',
            ],
        ];
    }
}
