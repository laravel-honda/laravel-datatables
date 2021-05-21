<?php

namespace Honda\Table\Actions;

use Honda\Table\Action;
use Illuminate\Database\Eloquent\Model;

class Delete extends Action
{
    public static string $defaultIcon = 'tabler-edit';

    public function __construct(string $name = 'Delete')
    {
        parent::__construct($name);

        $this->supportsBulk()
            ->icon(static::$defaultIcon)
            ->execute(function (Model $model) {
                $model->delete();
            });
    }

    public static function create(string $name = 'Delete'): Action
    {
        return parent::create($name);
    }
}
