@php
    $rows = $rows ?? 8;
    $columns = $columns ?? 5;
    $showHeader = $showHeader ?? true;
    $showActions = $showActions ?? true;
    $showPagination = $showPagination ?? true;
@endphp

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    {{-- Table Header --}}
    @if($showHeader)
    <div class="border-b border-gray-200 bg-gray-50/50">
        <div class="px-6 py-4 flex items-center justify-between">
            {{-- Title & Search --}}
            <div class="flex items-center space-x-4">
                <div class="h-6 bg-gray-200 rounded w-32 shimmer"></div>
                <div class="h-9 bg-gray-200 rounded-lg w-64 shimmer"></div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center space-x-3">
                <div class="h-9 bg-gray-200 rounded-lg w-20 shimmer"></div>
                <div class="h-9 bg-gray-200 rounded-lg w-24 shimmer"></div>
                <div class="h-9 bg-blue-200 rounded-lg w-28 shimmer"></div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="px-6 pb-4 flex items-center space-x-4">
            <div class="h-8 bg-gray-200 rounded-lg w-24 shimmer"></div>
            <div class="h-8 bg-gray-200 rounded-lg w-32 shimmer"></div>
            <div class="h-8 bg-gray-200 rounded-lg w-28 shimmer"></div>
            <div class="h-6 bg-gray-300 rounded w-16 shimmer ml-auto"></div>
        </div>
    </div>
    @endif

    {{-- Table Content --}}
    <div class="overflow-x-auto">
        <table class="w-full">
            {{-- Column Headers --}}
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    {{-- Checkbox Column --}}
                    <th class="w-12 px-6 py-3">
                        <div class="h-4 w-4 bg-gray-200 rounded shimmer"></div>
                    </th>

                    {{-- Data Columns --}}
                    @for($i = 0; $i < $columns; $i++)
                    <th class="px-6 py-3 text-left">
                        <div class="h-4 bg-gray-200 rounded w-{{ rand(16, 32) }} shimmer"></div>
                    </th>
                    @endfor

                    {{-- Actions Column --}}
                    @if($showActions)
                    <th class="w-24 px-6 py-3">
                        <div class="h-4 bg-gray-200 rounded w-16 shimmer"></div>
                    </th>
                    @endif
                </tr>
            </thead>

            {{-- Table Rows --}}
            <tbody class="divide-y divide-gray-200">
                @for($row = 0; $row < $rows; $row++)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    {{-- Checkbox --}}
                    <td class="px-6 py-4">
                        <div class="h-4 w-4 bg-gray-200 rounded shimmer"></div>
                    </td>

                    {{-- Data Cells --}}
                    @for($col = 0; $col < $columns; $col++)
                    <td class="px-6 py-4">
                        @if($col === 0)
                            {{-- Primary column with avatar/image --}}
                            <div class="flex items-center space-x-3">
                                <div class="h-8 w-8 bg-gray-200 rounded-full shimmer"></div>
                                <div class="space-y-1">
                                    <div class="h-4 bg-gray-200 rounded w-24 shimmer"></div>
                                    <div class="h-3 bg-gray-200 rounded w-16 shimmer"></div>
                                </div>
                            </div>
                        @elseif($col === 1)
                            {{-- Status badge --}}
                            <div class="h-6 bg-gray-200 rounded-full w-20 shimmer"></div>
                        @elseif($col === 2)
                            {{-- Progress bar --}}
                            <div class="space-y-2">
                                <div class="h-2 bg-gray-200 rounded-full w-full shimmer"></div>
                                <div class="h-3 bg-gray-200 rounded w-12 shimmer"></div>
                            </div>
                        @else
                            {{-- Regular text --}}
                            <div class="space-y-1">
                                <div class="h-4 bg-gray-200 rounded w-{{ rand(16, 40) }} shimmer"></div>
                            </div>
                        @endif
                    </td>
                    @endfor

                    {{-- Actions --}}
                    @if($showActions)
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-2">
                            <div class="h-8 w-8 bg-gray-200 rounded shimmer"></div>
                            <div class="h-8 w-8 bg-gray-200 rounded shimmer"></div>
                            <div class="h-8 w-8 bg-gray-200 rounded shimmer"></div>
                        </div>
                    </td>
                    @endif
                </tr>
                @endfor
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($showPagination)
    <div class="border-t border-gray-200 bg-gray-50/30 px-6 py-4">
        <div class="flex items-center justify-between">
            {{-- Results Info --}}
            <div class="flex items-center space-x-2">
                <div class="h-4 bg-gray-200 rounded w-32 shimmer"></div>
                <div class="h-6 bg-gray-200 rounded w-16 shimmer"></div>
            </div>

            {{-- Pagination Controls --}}
            <div class="flex items-center space-x-2">
                <div class="h-8 w-8 bg-gray-200 rounded shimmer"></div>
                <div class="h-8 w-8 bg-gray-200 rounded shimmer"></div>
                <div class="h-8 w-8 bg-blue-200 rounded shimmer"></div>
                <div class="h-8 w-8 bg-gray-200 rounded shimmer"></div>
                <div class="h-8 w-8 bg-gray-200 rounded shimmer"></div>
                <div class="h-8 w-8 bg-gray-200 rounded shimmer"></div>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
/* Refined shimmer for admin tables */
.shimmer {
    background: linear-gradient(
        90deg,
        #f8fafc 0%,
        #e2e8f0 20%,
        #e2e8f0 40%,
        #f8fafc 100%
    );
    background-size: 200% 100%;
    animation: admin-shimmer 1.8s ease-in-out infinite;
}

@keyframes admin-shimmer {
    0% {
        background-position: -200% 0;
    }
    100% {
        background-position: 200% 0;
    }
}

/* Staggered animation effect */
tr:nth-child(1) .shimmer { animation-delay: 0s; }
tr:nth-child(2) .shimmer { animation-delay: 0.1s; }
tr:nth-child(3) .shimmer { animation-delay: 0.2s; }
tr:nth-child(4) .shimmer { animation-delay: 0.3s; }
tr:nth-child(5) .shimmer { animation-delay: 0.4s; }
tr:nth-child(6) .shimmer { animation-delay: 0.5s; }
tr:nth-child(7) .shimmer { animation-delay: 0.6s; }
tr:nth-child(8) .shimmer { animation-delay: 0.7s; }

/* Dark mode support */
.dark .shimmer {
    background: linear-gradient(
        90deg,
        #1e293b 0%,
        #334155 20%,
        #334155 40%,
        #1e293b 100%
    );
    background-size: 200% 100%;
}

/* Loading pulse overlay */
.table-loading::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(
        to bottom,
        transparent 0%,
        rgba(255,255,255,0.1) 50%,
        transparent 100%
    );
    animation: loading-sweep 2s ease-in-out infinite;
}

@keyframes loading-sweep {
    0% { transform: translateY(-100%); }
    50% { transform: translateY(0%); }
    100% { transform: translateY(100%); }
}
</style>
