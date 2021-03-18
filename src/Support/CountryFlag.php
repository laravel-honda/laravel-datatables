<?php

namespace Honda\Table\Support;

use InvalidArgumentException;

class CountryFlag
{
    public const INDICATOR_OFFSET = 127397;

    protected array $aliases = [];

    public function __construct(array $aliases = [])
    {
        $this->setAliases($aliases);
    }

    public function setAliases(array $aliases): self
    {
        $this->aliases = [];

        foreach ($aliases as $alias => $countryCode) {
            $this->aliases[strtoupper($alias)] = strtoupper($countryCode);
        }

        return $this;
    }

    public function get(string $countryCode): string
    {
        if (strlen($countryCode) !== 2) {
            throw new InvalidArgumentException('Please provide a 2 character country code.');
        }

        $countryCode = strtoupper($countryCode);

        if (array_key_exists($countryCode, $this->aliases)) {
            $countryCode = $this->aliases[$countryCode];
        }

        return implode('', array_map([$this, 'toUnicode'], str_split($countryCode)));
    }

    protected function toUnicode(string $char): string
    {
        return mb_convert_encoding('&#' . $this->toRegionalIndicator($char) . ';', 'UTF-8', 'HTML-ENTITIES');
    }

    protected function toRegionalIndicator(string $char): int
    {
        return ord($char) + self::INDICATOR_OFFSET;
    }
}
