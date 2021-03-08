@php

    $flag = (new \Starts\Tables\Support\CountryFlag())->get($convertToIso3166($value))

@endphp

<span class="text-lg">{{ $flag }}</span>
