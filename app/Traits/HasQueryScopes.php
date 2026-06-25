<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

trait HasQueryScopes
{
    public function scopeApplySearch(Builder $query, ?array $search = null): Builder
    {
        if (empty($search)) {
            return $query;
        }

        $allowedColumns = $this->searchableColumns();

        return $query->when($search, function (Builder $builder) use ($search, $allowedColumns) {
            if (!empty($search['key']) && array_key_exists('value', $search)) {
                if (in_array($search['key'], $allowedColumns, true) && $search['value'] !== null && $search['value'] !== '') {
                    $builder->where($search['key'], 'like', '%'.$search['value'].'%');
                }

                return;
            }

            foreach ($search as $column => $value) {
                if (!in_array($column, $allowedColumns, true) || $value === null || $value === '') {
                    continue;
                }

                $builder->where($column, 'like', '%'.$value.'%');
            }
        });
    }

    public function scopeApplyFilter(Builder $query, ?array $filter = null): Builder
    {
        if (empty($filter)) {
            return $query;
        }

        $allowedColumns = $this->filterableColumns();

        return $query->when($filter, function (Builder $builder) use ($filter, $allowedColumns) {
            if (!empty($filter['key']) && array_key_exists('value', $filter)) {
                $this->applyFilterCondition($builder, $filter['key'], $filter['value'], $allowedColumns);

                return;
            }

            foreach ($filter as $column => $value) {
                $this->applyFilterCondition($builder, $column, $value, $allowedColumns);
            }
        });
    }

    protected function searchableColumns(): array
    {
        return property_exists($this, 'searchable') ? $this->searchable : $this->getFillable();
    }

    protected function filterableColumns(): array
    {
        if (property_exists($this, 'filterable')) {
            return $this->filterable;
        }

        return array_values(array_unique(array_merge($this->getFillable(), ['uuid', 'created_at', 'updated_at'])));
    }

    private function applyFilterCondition(Builder $builder, string $column, mixed $value, array $allowedColumns): void
    {
        if (!in_array($column, $allowedColumns, true) || $value === null || $value === '') {
            return;
        }

        if (is_array($value) && count($value) === 2 && $this->isDate($value[0]) && $this->isDate($value[1])) {
            $builder->whereDate($column, '>=', $value[0])
                ->whereDate($column, '<=', $value[1]);

            return;
        }

        $builder->where($column, $value);
    }

    private function isDate(mixed $value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        try {
            return Carbon::createFromFormat('Y-m-d', $value) !== false;
        } catch (\Throwable) {
            return false;
        }
    }
}
