<?php

namespace Honda\Table\Actions;

use Illuminate\Database\Eloquent\Model;
use Honda\Table\Action;

class Delete extends Action
{
    public function __construct(string $name = 'Delete')
    {
        parent::__construct($name);

        $this->supportsBulk()
            ->icon('trash')
            ->execute(function (Model $model) {
                $model->delete();
            });
    }

    public static function create(string $name = 'Delete'): Action
    {
        return parent::create($name);
    }
}
