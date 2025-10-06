@props(['status'])

@php
    // Map status to badge colors
    $colors = [
        'success' => 'success',
        'failed' => 'danger',
        'pending' => 'warning',
    ];

    // Get color from enum value or default to secondary
    $statusValue = is_object($status) ? $status->value : $status;
    $color = $colors[$statusValue] ?? 'secondary';

    // Get label
    $label = is_object($status) ? $status->label() : ucfirst($statusValue);
@endphp

<span {{ $attributes->merge(['class' => "badge badge-sm badge-{$color}"]) }}>
    {{ $label }}
</span>
