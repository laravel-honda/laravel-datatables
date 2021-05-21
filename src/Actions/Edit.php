<?php

namespace Honda\Table\Actions;

use Honda\Table\Action;
use Illuminate\Database\Eloquent\Model;
use Str;

class Edit extends Action
{
    public static string $defaultIcon = 'tabler-edit';

    public function __construct(string $name = 'Edit', callable $linkBuilder = null)
    {
        parent::__construct($name);
        $linkBuilder ??= function (Model $model) {
            $modelName = strtolower(class_basename(get_class($model)));

            return route(Str::plural($modelName) . '.' . 'edit', [
                $modelName => $model,
            ]);
        };

        $this
            ->icon(static::$defaultIcon)
            ->execute(fn(Model $model) => redirect($linkBuilder($model)));
    }

    public static function create(string $name = 'Edit'): Action
    {
        return parent::create($name);
    }
}
