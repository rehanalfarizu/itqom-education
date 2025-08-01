@php
    $cards = $cards ?? 12;
    $columns = $columns ?? 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4';
@endphp

<div class="space-y-6">
    {{-- Header Skeleton --}}
    <div class="flex items-center justify-between animate-pulse">
        <div class="space-y-2">
            <div class="h-8 bg-gray-200 rounded-lg w-48 shimmer"></div>
            <div class="h-4 bg-gray-200 rounded w-32 shimmer"></div>
        </div>
        <div class="flex space-x-3">
            <div class="h-10 bg-gray-200 rounded-lg w-24 shimmer"></div>
            <div class="h-10 bg-gray-200 rounded-lg w-32 shimmer"></div>
        </div>
    </div>

    {{-- Cards Grid --}}
    <div class="grid {{ $columns }} gap-6">
        @for($i = 0; $i < $cards; $i++)
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow duration-300">
            {{-- Thumbnail Skeleton --}}
            <div class="relative">
                <div class="aspect-video bg-gray-200 shimmer"></div>
                {{-- Duration Badge --}}
                <div class="absolute bottom-2 right-2">
                    <div class="h-5 w-12 bg-gray-800/60 rounded shimmer"></div>
                </div>
                {{-- Play Button --}}
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="h-12 w-12 bg-gray-300 rounded-full shimmer"></div>
                </div>
            </div>

            {{-- Content Skeleton --}}
            <div class="p-4 space-y-3">
                {{-- Title --}}
                <div class="space-y-2">
                    <div class="h-4 bg-gray-200 rounded w-full shimmer"></div>
                    <div class="h-4 bg-gray-200 rounded w-4/5 shimmer"></div>
                </div>

                {{-- Channel Info --}}
                <div class="flex items-center space-x-3">
                    <div class="h-8 w-8 bg-gray-200 rounded-full shimmer"></div>
                    <div class="flex-1 space-y-1">
                        <div class="h-3 bg-gray-200 rounded w-24 shimmer"></div>
                        <div class="h-3 bg-gray-200 rounded w-16 shimmer"></div>
                    </div>
                </div>

                {{-- Stats --}}
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="h-3 bg-gray-200 rounded w-16 shimmer"></div>
                        <div class="h-3 bg-gray-200 rounded w-20 shimmer"></div>
                    </div>
                    <div class="h-6 w-6 bg-gray-200 rounded shimmer"></div>
                </div>

                {{-- Tags --}}
                <div class="flex flex-wrap gap-2">
                    <div class="h-5 bg-gray-200 rounded-full w-12 shimmer"></div>
                    <div class="h-5 bg-gray-200 rounded-full w-16 shimmer"></div>
                    <div class="h-5 bg-gray-200 rounded-full w-14 shimmer"></div>
                </div>
            </div>
        </div>
        @endfor
    </div>

    {{-- Load More Button Skeleton --}}
    <div class="text-center">
        <div class="inline-block h-12 w-32 bg-gray-200 rounded-lg shimmer"></div>
    </div>
</div>

<style>
/* Enhanced YouTube-like shimmer effect */
.shimmer {
    background: linear-gradient(
        90deg,
        #f0f0f0 0%,
        #e8e8e8 20%,
        #e8e8e8 40%,
        #f0f0f0 100%
    );
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
    0% {
        background-position: -200% 0;
    }
    100% {
        background-position: 200% 0;
    }
}

/* Pulse animation for additional effect */
@keyframes pulse-subtle {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.8;
    }
}

.animate-pulse {
    animation: pulse-subtle 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Dark mode support */
.dark .shimmer {
    background: linear-gradient(
        90deg,
        #374151 0%,
        #4b5563 20%,
        #4b5563 40%,
        #374151 100%
    );
    background-size: 200% 100%;
}

/* Hover effects */
.group:hover .shimmer {
    animation-duration: 1s;
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .shimmer {
        animation-duration: 1.8s;
    }
}
</style>
