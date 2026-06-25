<?php

namespace App\Http\Controllers\Api;

use App\Models\GuestLink;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class GuestLinkController extends ApiResourceController
{
    protected string $modelClass = GuestLink::class;

    protected function rules(?Model $record = null): array
    {
        return [
            'uuid' => [
                'sometimes',
                'uuid',
                Rule::unique('guest_links', 'uuid')->ignore($record?->id),
            ],
            'link_no' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('guest_links', 'link_no')->ignore($record?->id),
            ],
            'store_uuid' => [
                $record ? 'sometimes' : 'required',
                'uuid',
                'exists:stores,uuid',
            ],
            'token' => [
                $record ? 'sometimes' : 'required',
                'string',
                'max:100',
                Rule::unique('guest_links', 'token')->ignore($record?->id),
            ],
            'label' => [
                'nullable',
                'string',
                'max:255',
            ],
            'qr_path' => [
                'nullable',
                'string',
                'max:500',
            ],
            'expires_at' => [
                'nullable',
                'date',
            ],
            'is_active' => [
                'sometimes',
                'boolean',
            ],
        ];
    }
}
