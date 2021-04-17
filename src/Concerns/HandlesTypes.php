<?php

namespace Honda\Table\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HandlesTypes
{
    public string $kind = 'default';
    /**
     * @var array<string, mixed>
     */
    protected array $attributes = [];

    public function asDate(string $format = 'F j, Y'): self
    {
        $this->kind = 'date';
        $this->attributes = compact('format');

        return $this;
    }

    public function asDateTime(string $format = 'F j, Y H:i:s'): self
    {
        $this->kind = 'date';
        $this->attributes = compact('format');

        return $this;
    }

    public function asTime(string $format = 'H:i'): self
    {
        $this->kind = 'date';
        $this->attributes = compact('format');

        return $this;
    }

    public function asBool(string $color = 'blue'): self
    {
        $this->kind = 'boolean';
        $this->attributes = compact('color');

        return $this;
    }

    public function asLink(callable $builder = null): self
    {
        $builder ??= function ($model): string {
            if (!$model instanceof Model) {
                return $model;
            }

            $name = strtolower(class_basename($model));

            return route(Str::plural($name) . '.show', [
                $name => $model,
            ]);
        };
        $this->kind = 'link';
        $this->attributes = compact('builder');

        return $this;
    }

    public function asIp(): self
    {
        $this->kind = 'ip';

        return $this;
    }

    public function asDateDiff(): self
    {
        $this->kind = 'date-diff';

        return $this;
    }

    public function asImage(string $disk = null, int $height = 40, int $width = null, bool $rounded = false): self
    {
        $this->kind = 'image';
        $this->attributes = compact('disk', 'height', 'width', 'rounded');

        return $this;
    }

    public function asStatus(callable $statusType): self
    {
        $this->kind = 'status';
        $this->attributes = compact('statusType');

        return $this;
    }

    public function asCurrency(string $symbol = '$', string $decimalSeparator = '.', string $thousandsSeparator = ','): self
    {
        $this->kind = 'currency';
        $this->attributes = compact('symbol', 'decimalSeparator', 'thousandsSeparator');

        return $this;
    }

    public function asEmail(): self
    {
        $this->kind = 'email';

        return $this;
    }

    public function containsActions(): self
    {
        $this->kind = 'actions';

        return $this;
    }

    public function asPhone(): self
    {
        $this->kind = 'phone';

        return $this;
    }

    public function asCountry(?callable $convertToIso3166 = null): self
    {
        $convertToIso3166 ??= fn($_) => $_;
        $this->kind = 'country';
        $this->attributes = compact('convertToIso3166');

        return $this;
    }
}
