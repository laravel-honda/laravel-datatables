<?php

namespace Honda\Table\Actions;

use Illuminate\Database\Eloquent\Model;
use Honda\Table\Action;

class Edit extends Action
{
    public function __construct(string $name = 'Edit')
    {
        parent::__construct($name);

        $this
            ->icon('pencil-alt')
            ->execute(function (Model $model) {
                $model->delete();
            });
    }

    public static function create(string $name = 'Edit'): Action
    {
        return parent::create($name);
    }
}
