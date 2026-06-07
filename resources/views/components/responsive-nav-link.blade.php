@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2.5 border-l-4 border-isotonic text-base font-medium text-isotonic bg-neptune/30 rounded-r-lg'
            : 'block w-full ps-3 pe-4 py-2.5 border-l-4 border-transparent text-base font-medium text-grape-mist hover:text-cheviot hover:bg-neptune/20 hover:border-grape-mist/50 transition rounded-r-lg';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
