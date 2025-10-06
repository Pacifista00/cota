@props(['type'])

@php
    // Configuration for each trigger type
    $config = [
        'manual' => [
            'color' => 'secondary',
            'icon' => 'ni-hand-click',
            'label' => 'Manual'
        ],
        'scheduled' => [
            'color' => 'primary',
            'icon' => 'ni-calendar-grid-58',
            'label' => 'Terjadwal'
        ],
    ];

    // Get config data or default to manual
    $data = $config[$type] ?? $config['manual'];

    // Build class string explicitly
    $badgeClass = 'badge badge-sm badge-' . $data['color'];
@endphp

<span {{ $attributes->merge(['class' => $badgeClass]) }}>
    <i class="ni {{ $data['icon'] }} text-xs"></i> {{ $data['label'] }}
</span>
