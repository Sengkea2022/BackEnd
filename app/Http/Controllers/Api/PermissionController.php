<?php

namespace App\Http\Controllers\Api;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Model;

class PermissionController extends ApiResourceController
{
    protected string $modelClass = Permission::class;

    protected function rules(?Model $record = null): array
    {
        return [];
    }

    protected function resolveRecord(string $identifier): Model
    {
        return Permission::where('id', $identifier)->orWhere('slug', $identifier)->firstOrFail();
    }
}
