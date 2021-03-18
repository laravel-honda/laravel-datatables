<?php

namespace Honda\Tables;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Honda\Tables\Concerns\HandlesTypes;

class Column
{
    use HandlesTypes;

    public string $name;
    public bool $searchable = false;
    public bool $sortable   = false;
    public bool $hidden     = false;
    /** @var callable */
    public $showLinkGuesser;
    protected ?string $label = null;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->guessShowLink(function (Model $model) {
            $name = strtolower(class_basename($model));

            return route(Str::plural($name) . '.show', [
                $name => $model,
            ]);
        });
    }

    public function guessShowLink(callable $guesser): self
    {
        $this->showLinkGuesser = $guesser;

        return $this;
    }

    public static function make(string $name): self
    {
        return new static($name);
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

    public function renderCell($record)
    {
        if (!$this->shouldRender()) {
            return;
        }

        return view('tables::cells.' . Str::of($this->kind)->kebab(), array_merge($this->attributes, [
            'record' => $record,
            'column' => $this,
            'value'  => $record->{$this->name},
        ]));
    }

    public function shouldRender(): bool
    {
        return !$this->hidden;
    }

    public function showLinkFor(Model $model)
    {
        $cb = $this->showLinkGuesser;

        return $cb($model);
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
