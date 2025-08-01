@php
    $skeletonCards = $skeletonCards ?? 6;
    $showHeader = $showHeader ?? true;
    $showStats = $showStats ?? true;
@endphp

<div class="space-y-6">
    {{-- Header Skeleton --}}
    @if($showHeader)
    <div class="flex items-center justify-between animate-pulse">
        <div class="space-y-2">
            <div class="h-8 bg-gray-200 rounded-lg w-48"></div>
            <div class="h-4 bg-gray-200 rounded w-32"></div>
        </div>
        <div class="flex space-x-3">
            <div class="h-10 bg-gray-200 rounded-lg w-24"></div>
            <div class="h-10 bg-gray-200 rounded-lg w-32"></div>
        </div>
    </div>
    @endif

    {{-- Stats Cards Skeleton --}}
    @if($showStats)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @for($i = 0; $i < 4; $i++)
        <div class="bg-white rounded-xl border border-gray-200 p-6 animate-pulse">
            <div class="flex items-center justify-between">
                <div class="space-y-2">
                    <div class="h-4 bg-gray-200 rounded w-20"></div>
                    <div class="h-8 bg-gray-200 rounded w-16"></div>
                </div>
                <div class="h-12 w-12 bg-gray-200 rounded-lg"></div>
            </div>
            <div class="mt-4">
                <div class="h-2 bg-gray-200 rounded-full w-full">
                    <div class="h-2 bg-gray-300 rounded-full w-3/4"></div>
                </div>
            </div>
        </div>
        @endfor
    </div>
    @endif

    {{-- Main Content Skeleton --}}
    <div class="bg-white rounded-xl border border-gray-200">
        {{-- Toolbar Skeleton --}}
        <div class="border-b border-gray-200 p-6">
            <div class="flex items-center justify-between animate-pulse">
                <div class="flex items-center space-x-4">
                    <div class="h-10 bg-gray-200 rounded-lg w-64"></div>
                    <div class="h-10 bg-gray-200 rounded-lg w-24"></div>
                    <div class="h-10 bg-gray-200 rounded-lg w-20"></div>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="h-10 bg-gray-200 rounded-lg w-16"></div>
                    <div class="h-10 bg-gray-200 rounded-lg w-28"></div>
                </div>
            </div>
        </div>

        {{-- Table Header Skeleton --}}
        <div class="border-b border-gray-200 bg-gray-50/50">
            <div class="grid grid-cols-6 gap-4 p-4 animate-pulse">
                <div class="h-4 bg-gray-200 rounded w-4"></div>
                <div class="h-4 bg-gray-200 rounded w-20"></div>
                <div class="h-4 bg-gray-200 rounded w-24"></div>
                <div class="h-4 bg-gray-200 rounded w-16"></div>
                <div class="h-4 bg-gray-200 rounded w-18"></div>
                <div class="h-4 bg-gray-200 rounded w-12"></div>
            </div>
        </div>

        {{-- Table Rows Skeleton --}}
        <div class="divide-y divide-gray-200">
            @for($i = 0; $i < $skeletonCards; $i++)
            <div class="grid grid-cols-6 gap-4 p-4 items-center animate-pulse hover:bg-gray-50/50">
                {{-- Checkbox --}}
                <div class="h-4 w-4 bg-gray-200 rounded"></div>

                {{-- Title with Image --}}
                <div class="flex items-center space-x-3">
                    <div class="h-12 w-12 bg-gray-200 rounded-lg flex-shrink-0"></div>
                    <div class="space-y-2 flex-1">
                        <div class="h-4 bg-gray-200 rounded w-32"></div>
                        <div class="h-3 bg-gray-200 rounded w-24"></div>
                    </div>
                </div>

                {{-- Category --}}
                <div class="space-y-1">
                    <div class="h-6 bg-gray-200 rounded-full w-16"></div>
                </div>

                {{-- Status --}}
                <div class="space-y-1">
                    <div class="h-6 bg-gray-200 rounded-full w-20"></div>
                </div>

                {{-- Date --}}
                <div class="space-y-1">
                    <div class="h-4 bg-gray-200 rounded w-24"></div>
                    <div class="h-3 bg-gray-200 rounded w-16"></div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center space-x-2">
                    <div class="h-8 w-8 bg-gray-200 rounded"></div>
                    <div class="h-8 w-8 bg-gray-200 rounded"></div>
                    <div class="h-8 w-8 bg-gray-200 rounded"></div>
                </div>
            </div>
            @endfor
        </div>

        {{-- Pagination Skeleton --}}
        <div class="border-t border-gray-200 px-6 py-4">
            <div class="flex items-center justify-between animate-pulse">
                <div class="h-4 bg-gray-200 rounded w-32"></div>
                <div class="flex items-center space-x-2">
                    <div class="h-8 w-8 bg-gray-200 rounded"></div>
                    <div class="h-8 w-8 bg-gray-200 rounded"></div>
                    <div class="h-8 w-8 bg-gray-200 rounded"></div>
                    <div class="h-8 w-8 bg-gray-200 rounded"></div>
                    <div class="h-8 w-8 bg-gray-200 rounded"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Enhanced YouTube-like shimmer animation */
@keyframes shimmer {
    0% {
        background-position: -1000px 0;
    }
    100% {
        background-position: 1000px 0;
    }
}

.animate-pulse {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 1000px 100%;
    animation: shimmer 2s infinite linear;
}

.animate-pulse > * {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 1000px 100%;
    animation: shimmer 2s infinite linear;
}

/* Dark mode support */
.dark .animate-pulse {
    background: linear-gradient(90deg, #374151 25%, #4b5563 50%, #374151 75%);
    background-size: 1000px 100%;
}

.dark .animate-pulse > * {
    background: linear-gradient(90deg, #374151 25%, #4b5563 50%, #374151 75%);
    background-size: 1000px 100%;
}
</style>
