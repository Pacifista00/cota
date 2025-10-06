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

    // Build class string explicitly
    $badgeClass = 'badge badge-sm badge-' . $color;
@endphp

<span {{ $attributes->merge(['class' => $badgeClass]) }}>
    {{ $label }}
</span>
