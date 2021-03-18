@php

    $flag = (new \Honda\Table\Support\CountryFlag())->get($convertToIso3166($value))

@endphp

<span class="text-lg">{{ $flag }}</span>
