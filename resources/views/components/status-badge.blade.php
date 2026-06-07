@props(['status'])

@php
$classes = match ($status) {
    'menunggu' => 'bg-pacific/60 text-neptune border-pacific',
    'proses' => 'bg-neptune/15 text-neptune border-neptune/30',
    'selesai' => 'bg-isotonic/30 text-midnight border-isotonic/50',
    default => 'bg-grape-mist/40 text-midnight border-grape-mist',
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full border {$classes}"]) }}>
    {{ ucfirst($status) }}
</span>
