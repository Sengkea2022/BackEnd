<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class ApiResourceController extends Controller
{
    /**
     * @var class-string<Model>
     */
    protected string $modelClass;

    /**
     * @var list<string>
     */
    protected array $with = [];

    protected int $defaultPerPage = 15;

    protected int $maxPerPage = 100;

    abstract protected function rules(?Model $record = null): array;

    protected function query(): Builder
    {
        return $this->modelClass::query()->with($this->with);
    }

    protected function resolveRecord(string $uuid): Model
    {
        return $this->query()->where('uuid', $uuid)->firstOrFail();
    }

    public function index(Request $request): JsonResponse
    {
        $query = $this->query()
            ->latest('id')
            ->applySearch($request->input('search'))
            ->applyFilter($request->input('filter'));

        if (! $this->shouldPaginate($request)) {
            $records = $query->get();

            return response()->json([
                'data' => $records,
                'meta' => [
                    'total' => $records->count(),
                ],
            ]);
        }

        $paginator = $query->paginate($this->perPage($request))->appends($request->query());

        return response()->json([
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $record = $this->modelClass::query()->create($request->validate($this->rules()));

        return response()->json([
            'data' => $record->fresh($this->with),
        ], 201);
    }

    public function show(string $uuid): JsonResponse
    {
        return response()->json([
            'data' => $this->resolveRecord($uuid),
        ]);
    }

    public function update(Request $request, string $uuid): JsonResponse
    {
        $record = $this->resolveRecord($uuid);
        $record->update($request->validate($this->rules($record)));

        return response()->json([
            'data' => $record->fresh($this->with),
        ]);
    }

    public function destroy(string $uuid): JsonResponse
    {
        $record = $this->resolveRecord($uuid);
        $record->delete();

        return response()->json([
            'message' => 'Deleted successfully',
        ]);
    }

    protected function shouldPaginate(Request $request): bool
    {
        return filter_var($request->input('paginate', true), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true;
    }

    protected function perPage(Request $request): int
    {
        $perPage = (int) $request->input('per_page', $this->defaultPerPage);

        if ($perPage < 1) {
            return $this->defaultPerPage;
        }

        return min($perPage, $this->maxPerPage);
    }
}
