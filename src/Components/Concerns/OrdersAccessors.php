<?php


namespace Honda\Table\Components\Concerns;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait OrdersAccessors
{
    public function isAccessor(Model $model, string $column): bool
    {
        return method_exists($model, 'get' . Str::of($column)->camel()->ucfirst() . 'Attribute');
    }
}
