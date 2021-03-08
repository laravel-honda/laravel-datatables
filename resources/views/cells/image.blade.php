@php
    $disk ??= config('filesystems.default')
@endphp
<a href="{{ Storage::disk($disk)->url($value) }}">
    <img
        src="{{ Storage::disk($disk)->url($value) }}"
        alt=""
        style="height: {{ $height }}px; width: {{ $width ?? 'auto' }}"
        class="inline max-w-full object-cover object-center {{ $rounded ? 'rounded-full' : 'rounded-lg' }}"
    />
</a>
