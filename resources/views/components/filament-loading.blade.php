@php
    $type = $type ?? 'stats'; // stats, chart, table, overview
    $columns = $columns ?? 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4';
@endphp

@if($type === 'stats')
{{-- Stats Cards Loading --}}
<div class="grid {{ $columns }} gap-6">
    @for($i = 0; $i < 4; $i++)
    <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
        {{-- Icon --}}
        <div class="flex items-center justify-between">
            <div class="h-12 w-12 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg shimmer"></div>
            <div class="h-6 w-6 bg-gray-200 rounded shimmer"></div>
        </div>

        {{-- Value --}}
        <div class="space-y-2">
            <div class="h-8 bg-gray-200 rounded w-20 shimmer"></div>
            <div class="h-4 bg-gray-200 rounded w-24 shimmer"></div>
        </div>

        {{-- Trend --}}
        <div class="flex items-center space-x-2">
            <div class="h-4 w-4 bg-green-200 rounded shimmer"></div>
            <div class="h-3 bg-gray-200 rounded w-16 shimmer"></div>
        </div>
    </div>
    @endfor
</div>

@elseif($type === 'chart')
{{-- Chart Widget Loading --}}
<div class="bg-white rounded-xl border border-gray-200 p-6">
    {{-- Chart Header --}}
    <div class="flex items-center justify-between mb-6">
        <div class="space-y-2">
            <div class="h-6 bg-gray-200 rounded w-32 shimmer"></div>
            <div class="h-4 bg-gray-200 rounded w-48 shimmer"></div>
        </div>
        <div class="flex space-x-2">
            <div class="h-8 bg-gray-200 rounded-lg w-16 shimmer"></div>
            <div class="h-8 bg-gray-200 rounded-lg w-20 shimmer"></div>
        </div>
    </div>

    {{-- Chart Area --}}
    <div class="relative h-80 bg-gray-50 rounded-lg overflow-hidden">
        {{-- Y-axis labels --}}
        <div class="absolute left-0 top-0 bottom-0 w-12 flex flex-col justify-between py-4">
            @for($i = 0; $i < 6; $i++)
            <div class="h-3 bg-gray-200 rounded w-8 shimmer"></div>
            @endfor
        </div>

        {{-- Chart bars/lines --}}
        <div class="ml-12 mr-4 h-full flex items-end justify-between">
            @for($i = 0; $i < 12; $i++)
            <div class="bg-blue-200 rounded-t w-8 shimmer" style="height: {{ rand(20, 80) }}%"></div>
            @endfor
        </div>

        {{-- X-axis labels --}}
        <div class="absolute bottom-0 left-12 right-4 flex justify-between py-2">
            @for($i = 0; $i < 6; $i++)
            <div class="h-3 bg-gray-200 rounded w-6 shimmer"></div>
            @endfor
        </div>
    </div>

    {{-- Legend --}}
    <div class="flex items-center justify-center space-x-6 mt-4">
        @for($i = 0; $i < 3; $i++)
        <div class="flex items-center space-x-2">
            <div class="h-3 w-3 bg-blue-200 rounded-full shimmer"></div>
            <div class="h-3 bg-gray-200 rounded w-16 shimmer"></div>
        </div>
        @endfor
    </div>
</div>

@elseif($type === 'table')
{{-- Table Widget Loading --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    {{-- Table Header --}}
    <div class="border-b border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div class="space-y-2">
                <div class="h-6 bg-gray-200 rounded w-40 shimmer"></div>
                <div class="h-4 bg-gray-200 rounded w-32 shimmer"></div>
            </div>
            <div class="h-9 bg-blue-200 rounded-lg w-24 shimmer"></div>
        </div>
    </div>

    {{-- Table Content --}}
    <div class="overflow-hidden">
        @for($row = 0; $row < 5; $row++)
        <div class="flex items-center justify-between p-4 border-b border-gray-100 last:border-b-0">
            {{-- Profile --}}
            <div class="flex items-center space-x-3">
                <div class="h-10 w-10 bg-gray-200 rounded-full shimmer"></div>
                <div class="space-y-1">
                    <div class="h-4 bg-gray-200 rounded w-24 shimmer"></div>
                    <div class="h-3 bg-gray-200 rounded w-16 shimmer"></div>
                </div>
            </div>

            {{-- Status --}}
            <div class="h-6 bg-green-200 rounded-full w-16 shimmer"></div>

            {{-- Value --}}
            <div class="text-right space-y-1">
                <div class="h-4 bg-gray-200 rounded w-16 shimmer"></div>
                <div class="h-3 bg-gray-200 rounded w-12 shimmer"></div>
            </div>
        </div>
        @endfor
    </div>

    {{-- Table Footer --}}
    <div class="border-t border-gray-200 p-4 text-center">
        <div class="h-4 bg-gray-200 rounded w-32 mx-auto shimmer"></div>
    </div>
</div>

@elseif($type === 'overview')
{{-- Overview Dashboard Loading --}}
<div class="space-y-8">
    {{-- Welcome Section --}}
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6">
        <div class="flex items-center justify-between">
            <div class="space-y-3">
                <div class="h-8 bg-blue-200 rounded w-48 shimmer"></div>
                <div class="h-5 bg-blue-200 rounded w-64 shimmer"></div>
                <div class="h-4 bg-blue-200 rounded w-40 shimmer"></div>
            </div>
            <div class="h-24 w-24 bg-blue-200 rounded-full shimmer"></div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @for($i = 0; $i < 4; $i++)
        <div class="bg-white rounded-lg border border-gray-200 p-4 text-center space-y-3">
            <div class="h-12 w-12 bg-gray-200 rounded-lg mx-auto shimmer"></div>
            <div class="h-4 bg-gray-200 rounded w-16 mx-auto shimmer"></div>
        </div>
        @endfor
    </div>

    {{-- Recent Activity --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="space-y-4">
            <div class="h-6 bg-gray-200 rounded w-32 shimmer"></div>

            @for($i = 0; $i < 4; $i++)
            <div class="flex items-center space-x-4 p-3 bg-gray-50 rounded-lg">
                <div class="h-8 w-8 bg-gray-200 rounded-full shimmer"></div>
                <div class="flex-1 space-y-1">
                    <div class="h-4 bg-gray-200 rounded w-48 shimmer"></div>
                    <div class="h-3 bg-gray-200 rounded w-24 shimmer"></div>
                </div>
                <div class="h-3 bg-gray-200 rounded w-16 shimmer"></div>
            </div>
            @endfor
        </div>
    </div>
</div>

@endif

<style>
/* Enhanced Filament-style shimmer */
.shimmer {
    background: linear-gradient(
        110deg,
        #f1f5f9 0%,
        #e2e8f0 20%,
        #cbd5e1 40%,
        #e2e8f0 60%,
        #f1f5f9 100%
    );
    background-size: 200% 100%;
    animation: filament-shimmer 2s ease-in-out infinite;
}

@keyframes filament-shimmer {
    0% {
        background-position: -200% 0;
    }
    100% {
        background-position: 200% 0;
    }
}

/* Card hover effect during loading */
.bg-white:hover .shimmer {
    animation-duration: 1.5s;
}

/* Gradient shimmer variants */
.from-blue-100 .shimmer,
.bg-blue-200.shimmer {
    background: linear-gradient(
        110deg,
        #dbeafe 0%,
        #bfdbfe 20%,
        #93c5fd 40%,
        #bfdbfe 60%,
        #dbeafe 100%
    );
    background-size: 200% 100%;
}

.from-green-100 .shimmer,
.bg-green-200.shimmer {
    background: linear-gradient(
        110deg,
        #dcfce7 0%,
        #bbf7d0 20%,
        #86efac 40%,
        #bbf7d0 60%,
        #dcfce7 100%
    );
    background-size: 200% 100%;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .shimmer {
        animation-duration: 2.5s;
    }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .shimmer {
        background: linear-gradient(
            110deg,
            #1e293b 0%,
            #334155 20%,
            #475569 40%,
            #334155 60%,
            #1e293b 100%
        );
        background-size: 200% 100%;
    }
}
</style>
