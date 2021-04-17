<?php

namespace Honda\Table\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait ResolvesAttributes
{
    public function resolveAttribute(Model $model, string $attribute)
    {
        if (!str_contains($attribute, '.')) {
            return $model->getAttribute($attribute);
        }

        $split    = explode('.', $attribute);
        $resolved = $model;
        foreach ($split as $part) {
            if ($resolved === null) {
                return null;
            }

            if (method_exists($resolved, $part) && $resolved->{$part}() instanceof HasMany) {
                $resolved = $resolved->{$part}()->latest()->take(1)->first();
            } else {
                $resolved = $resolved->{$part};
            }
        }

        return $resolved;
    }
}
