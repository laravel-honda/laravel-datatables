<?php


namespace Honda\Table\Components;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait SearchesAccessors
{
    public function searchAccessor(Model $model, string $attribute, string $search): bool
    {
        $transform = fn($str): string => Str::of($str)->lower()->slug()->replace('-', '');
        $value = $transform($model->{$attribute});

        if (empty($value)) {
            return false;
        }

        // TODO: This is a very naive implementation there is a lot of room for improvement there
        if (!str_contains($value, $transform($search))) {
            return false;
        }

        return true;
    }
}
