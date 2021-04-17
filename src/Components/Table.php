<?php

namespace Honda\Table\Components;

use Exception;
use Honda\Table\Action;
use Honda\Table\Column;
use Honda\Table\Concerns\OrdersNestedAttributes;
use Honda\Table\Concerns\ResolvesAttributes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

abstract class Table extends Component
{
    use WithPagination;
    use ResolvesAttributes;
    use OrdersNestedAttributes;

    public static string $model;

    public $search;
    public $recordsPerPage = 15;
    public $sortColumn = null;
    public $sortDirection = 'asc';
    public $selected = [];

    protected $queryString = [
        'sortColumn', 'search',
        'recordsPerPage' => ['except' => 15],
        'sortDirection' => ['except' => 'asc'],
        'page' => ['except' => 1],
    ];

    public function sortBy($column): void
    {
        if ($this->sortColumn === $column) {
            switch ($this->sortDirection) {
                case 'asc':
                    $this->sortDirection = 'desc';

                    break;
                case 'desc':
                    $this->sortColumn = null;
                    $this->sortDirection = 'asc';

                    break;
            }

            return;
        }

        $this->sortColumn = $column;
        $this->sortDirection = 'asc';
    }

    public function selectAll(): void
    {
        $records = $this->records()->get();

        if (count($records) === count($this->selected)) {
            $this->selected = [];

            return;
        }

        $this->selected = collect($records)->map->getKey()->toArray();
    }

    protected function records(): Builder
    {
        /** @var Builder $query */
        $query = $this::guessModelClass()::query();
        $table = $query->getModel()->getTable();
        $query->select($table . '.*');

        if ($this->search !== null && $this->search !== '' && $this->hasAnySearchableColumns()) {
            collect($this->columns())
                ->filter->searchable
                ->each(function ($column, $index) use (&$query) {
                    $search = Str::lower($this->search);

                    $first = $index === 0;

                    if (Str::of($column->name)->contains('.')) {
                        $relationship = (string)Str::of($column->name)->beforeLast('.');

                        $query = $query->{$first ? 'whereHas' : 'orWhereHas'}(
                            $relationship,
                            function ($query) use ($column, $search) {
                                $columnName = (string)Str::of($column->name)->afterLast('.');

                                return $query->whereRaw("LOWER({$columnName}) LIKE ?", ["%{$search}%"]);
                            },
                        );

                        return;
                    }

                    $query = $query->{$first ? 'where' : 'orWhere'}(
                        fn($query) => $query->whereRaw("LOWER({$column->name}) LIKE ?", ["%{$search}%"]),
                    );
                });
        }

        if (!empty($this->sortColumn)) {
            if (!str_contains($this->sortColumn, '.')) {
                $query->orderBy($this->sortColumn, $this->sortDirection);
            } else {
                [$relation, $column] = explode('.', $this->sortColumn);
                $relationObject = $query->getModel()->{$relation}();
                $relatedTable = Str::plural($relation);

                switch (get_class($relationObject)) {
                    case HasMany::class:
                        $this->orderHasMany(
                            $relationObject,
                            $query,
                            $table,
                            $relatedTable,
                            $column,
                            $this->sortDirection
                        );
                        break;
                    case HasOne::class:
                        $this->orderHasOne(
                            $query,
                            $table,
                            $relatedTable,
                            $column,
                            $this->sortDirection
                        );
                        break;
                    case BelongsTo::class:
                        $this->orderBelongsTo(
                            $query,
                            $table,
                            $relatedTable,
                            $column,
                            $this->sortDirection
                        );
                        break;
                    default:
                        throw new Exception(sprintf('Relation of type [%s}] is not supported', get_class($relationObject)));
                }
            }
        }

        return $query;
    }

    public static function guessModelClass(): string
    {
        static::$model ??= (config('tables.models_namespace') . str_replace(
                'Table',
                '',
                class_basename(static::class)
            ));

        return static::$model;
    }

    public function hasAnySearchableColumns(): bool
    {
        return collect($this->columns())->filter->searchable->isNotEmpty();
    }

    /**
     * @return Column[]
     */
    abstract public function columns(): array;

    public function updatedRecordsPerPage(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->selected = [];

        $this->resetPage();
    }

    public function select($record): void
    {
        if (!in_array($record, $this->selected)) {
            $this->selected[] = $record;

            return;
        }

        unset($this->selected[array_search($record, $this->selected)]);
    }

    public function render()
    {
        return view($this->viewName(), [
            'records' => $this->records()->paginate($this->recordsPerPage),
            'selected' => $this->selected,
            'columns' => $this->columns(),
            'actions' => $this->actions(),
        ]);
    }

    protected function viewName(): string
    {
        return 'tables::table';
    }

    /**
     * @return Action[]
     */
    public function actions(): array
    {
        return [];
    }

    public function paginationView(): string
    {
        return 'tables::pagination';
    }

    public function action($record, int $action)
    {
        $record = $this->records()->where('id', $record)->first();
        $action = $this->actions()[$action];

        $this->resetPage();

        return $action->run($record);
    }

    public function bulkAction(string $action): void
    {
        $records = $this->records()->whereIn('id', $this->selected)->get();
        $action = $this->actions()[$action];

        foreach ($records as $record) {
            $action->run($record);
        }

        $this->resetPage();
    }
}
