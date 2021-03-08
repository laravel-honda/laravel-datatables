<span>
    @if (!is_numeric($value))
        {{ $value }}
    @else
        {{ $symbol . number_format($value, 2, $decimalSeparator, $thousandsSeparator) }}
    @endif
</span>
