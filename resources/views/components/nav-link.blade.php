@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-3 py-2 text-sm font-medium text-isotonic border-b-2 border-isotonic'
            : 'inline-flex items-center px-3 py-2 text-sm font-medium text-grape-mist hover:text-cheviot border-b-2 border-transparent hover:border-grape-mist/50 transition';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
