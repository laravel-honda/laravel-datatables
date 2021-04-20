<?php

namespace Honda\Table;

use Illuminate\Database\Eloquent\Model;

class Action
{
    public string $name;
    public string $icon = '';
    public string $iconSet;
    public bool $supportsBulk = false;

    /** @var callable */
    public $callable;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function create(string $name): self
    {
        return new static($name);
    }

    public function icon(string $icon, string $set = 'tabler'): self
    {
        $this->icon    = $icon;
        $this->iconSet = $set;

        return $this;
    }

    public function supportsBulk(): self
    {
        $this->supportsBulk = true;

        return $this;
    }

    public function execute(callable $cb): self
    {
        $this->callable = $cb;

        return $this;
    }

    public function run(Model $record)
    {
        return call_user_func($this->callable, $record);
    }
}
