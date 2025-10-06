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
@endphp

<span {{ $attributes->merge(['class' => "badge badge-sm badge-{$data['color']}"]) }}>
    <i class="ni {{ $data['icon'] }} text-xs"></i> {{ $data['label'] }}
</span>
