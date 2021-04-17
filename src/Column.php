<?php

namespace Honda\Table;

use Closure;
use Honda\Table\Components\Concerns\OrdersAccessors;
use Honda\Table\Concerns\HandlesTypes;
use Honda\Table\Concerns\ResolvesAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Column
{
    use HandlesTypes;
    use ResolvesAttributes;
    use OrdersAccessors;

    public string $name;
    public bool $searchable = false;
    public bool $sortable = true;
    public bool $copyable = false;
    public bool $hidden = false;
    protected ?string $label = null;
    protected ?Closure $valueResolver;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->valueResolver ??= function (Model $record, Column $column) {
            return $this->resolveAttribute($record, $column->name);
        };
    }

    public static function make(string $name): self
    {
        return new static($name);
    }

    public function valueResolver(callable $postProcessor): self
    {
        $this->valueResolver = $postProcessor;

        return $this;
    }

    public function copyable(): self
    {
        $this->copyable = true;

        return $this;
    }

    public function labeledAs(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function searchable(): self
    {
        $this->searchable = true;

        return $this;
    }

    public function sortable(): self
    {
        $this->sortable = true;

        return $this;
    }

    public function visible(): self
    {
        $this->hidden = false;

        return $this;
    }

    public function hidden(): self
    {
        $this->hidden = true;

        return $this;
    }

    public function renderCell(Model $record)
    {
        if ($this->sortable && $this->isAccessor($record, $this->name)) {
            $this->sortable = false;
        }

        if (!$this->shouldRender()) {
            return;
        }

        return view('tables::cells.' . Str::of($this->kind)->kebab(), array_merge($this->attributes, [
            'record' => $record,
            'column' => $this,
            'value' => $this->getValueFor($record),
        ]));
    }

    public function shouldRender(): bool
    {
        return !$this->hidden;
    }

    public function getValueFor(Model $record)
    {
        return call_user_func($this->valueResolver, $record, $this);
    }

    public function getLabel(): string
    {
        if ($this->label) {
            return $this->label;
        }

        return ucwords(str_replace(
            '#[space]',
            ' ',
            trim(preg_replace('/[^\x21-\x7E]/', '', str_replace(['_', '-', '.'], '#[space]', $this->name)))
        ));
    }
}
