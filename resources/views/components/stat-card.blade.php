@props([
    'title' => '',
    'value' => '',
    'change' => null,
    'changeLabel' => 'vs last month',
    'icon' => 'fa-chart-line',
    'iconColor' => 'text-yellow-400',
    'bgColor' => 'bg-blue-900',
    'changeColor' => 'text-green-500'
])

@php
// Map icon names to Lucide icons
$iconMap = [
    'fa-users' => 'users',
    'fa-user-check' => 'user-check',
    'fa-calendar' => 'calendar',
    'fa-check-circle' => 'check-circle',
    'fa-chart-line' => 'trending-up',
    'fa-building' => 'building',
    'fa-file-text' => 'file-text',
    'fa-folder' => 'folder',
    'fa-scale' => 'scale',
    'fa-clock' => 'clock',
    'fa-activity' => 'activity',
    'fa-user' => 'user',
    'fa-inbox' => 'inbox',
    'fa-shield-alt' => 'shield-alert',
    'fa-shield' => 'shield',
    'fa-alert-triangle' => 'alert-triangle',
    'fa-times-circle' => 'x-circle',
];
$lucideIcon = $iconMap[$icon] ?? 'chart';
@endphp

<div class="bg-white rounded-xl border border-gray-100 p-6 shadow-lg card-hover stat-card hover:shadow-xl transition-all duration-300">
    <div class="flex items-center justify-between">
        <!-- Content on LEFT -->
        <div class="flex-1">
            <h3 class="text-sm font-medium text-gray-600 uppercase tracking-wide mb-1">{{ $title }}</h3>
            <p class="text-3xl font-bold text-gray-800 mb-1">{{ $value }}</p>
            @if($change !== null)
                <div class="flex items-center mt-3">
                    @php
                        $arrowClass = $change >= 0 ? 'fa-arrow-up text-green-500' : 'fa-arrow-down text-red-500';
                        $textColor = $change >= 0 ? 'text-green-500' : 'text-red-500';
                    @endphp
                    <span class="text-sm font-medium flex items-center {{ $textColor }}">
                        <i class="fa-solid {{ $arrowClass }} mr-1"></i>
                        {{ number_format(abs($change), 2) }}%
                    </span>
                    <span class="text-sm text-gray-500 ml-2">{{ $changeLabel }}</span>
                </div>
            @endif
        </div>
        
        <!-- Blue Icon Box on RIGHT -->
        <div class="w-20 h-20 rounded-xl flex items-center justify-center {{ $bgColor }} flex-shrink-0 ml-4">
            <i data-lucide="{{ $lucideIcon }}" class="{{ $iconColor }}" style="width: 1.75rem; height: 1.75rem; stroke-width: 2;"></i>
        </div>
    </div>
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush






