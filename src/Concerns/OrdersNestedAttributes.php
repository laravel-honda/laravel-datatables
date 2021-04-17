<?php

namespace Honda\Table\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

trait OrdersNestedAttributes
{
    public function orderHasOne(Builder $builder, string $parentTable, string $relatedTable, string $column, string $direction): Builder
    {
        $relatedColumnOnRelation = Str::singular($parentTable);

        return $builder
            ->join($relatedTable, "$relatedTable.{$relatedColumnOnRelation}_id", '=', "$parentTable.id")
            ->orderBy("$relatedTable.$column", $direction);
    }

    public function orderBelongsTo(Builder $builder, string $parentTable, string $relatedTable, string $column, string $direction): Builder
    {
        $relatedColumnOnModel = Str::singular($relatedTable);

        return $builder
            ->join($relatedTable, "{$relatedTable}.id", '=', "{$parentTable}.{$relatedColumnOnModel}_id")
            ->orderBy("{$relatedTable}.{$column}", $direction);
    }

    public function orderHasMany(HasMany $relation, Builder $builder, string $parentTable, string $relatedTable, string $column, string $direction): Builder
    {
        $relatedColumnOnRelation = Str::singular($parentTable);

        return $builder->orderBy(
            $relation->getModel()::select($column)
                ->whereColumn("$relatedTable.{$relatedColumnOnRelation}_id", "$parentTable.id")
                ->latest()
                ->take(1),
            $direction
        );
    }
}
