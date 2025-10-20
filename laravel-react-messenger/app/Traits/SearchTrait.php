<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait SearchTrait
{
    public function applySearch(
        Builder $query,
        ?string $search,
        array $fields = [],
        array $relations = [],
        string $matchType = 'starts_with'
    ): Builder {
        $search = trim($search);

        if (empty($search) || (empty($fields) && empty($relations))) {
            return $query;
        }

        $likePattern = $this->buildLikePattern($search, $matchType);

        $query->where(function ($q) use ($fields, $relations, $likePattern) {
            $hasAnyCondition = false;

            if (!empty($fields)) {
                $this->applyFieldSearch($q, $fields, $likePattern);
                $hasAnyCondition = true;
            }

            if (!empty($relations)) {
                $this->applyRelationSearch($q, $relations, $likePattern);
                $hasAnyCondition = true;
            }

            if (!$hasAnyCondition) {
                $q->whereRaw('0 = 1');
            }
        });

        return $query;
    }

    private function buildLikePattern(string $search, string $matchType): string
    {
        return match ($matchType) {
            'contains'   => '%' . $search . '%',
            'ends_with'  => '%' . $search,
            'exact'      => $search,
            default      => $search . '%',
        };
    }

    private function applyFieldSearch(Builder $query, array $fields, string $likePattern): void
    {
        foreach ($fields as $field) {
            if ($field === 'full_name') {
                $query->orWhereRaw("LOWER(CONCAT(first_name, ' ', last_name)) LIKE ?", [strtolower($likePattern)]);
            }else{
                $query->orWhereRaw("LOWER($field) LIKE ?", [strtolower($likePattern)]);
            }
        }
    }

    private function applyRelationSearch(Builder $query, array $relations, string $likePattern): void
    {
        foreach ($relations as $relation => $fields) {
            $query->orWhereHas($relation, function ($relationQuery) use ($fields, $likePattern) {
                $table = $relationQuery->getModel()->getTable();
                $relationQuery->where(function ($innerQuery) use ($fields, $likePattern, $table) {
                    foreach ($fields as $field) {
                        if ($field === 'full_name') {
                            $innerQuery->orWhereRaw("LOWER(CONCAT($table.first_name, ' ', $table.last_name)) LIKE ?", [strtolower($likePattern)]);
                        } else {
                            $innerQuery->orWhereRaw("LOWER($table.$field) LIKE ?", [strtolower($likePattern)]);
                        }
                    }
                });
            });
        }
    }
}
