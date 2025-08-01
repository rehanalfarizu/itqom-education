{{-- resources/views/filament/widgets/course-overview.blade.php --}}
<x-filament-widgets::widget>
    <x-filament::section>
        <div x-data="{
            loading: true,

            init() {
                // Auto load after component mount
                setTimeout(() => {
                    this.loading = false;
                    $wire.loadData();
                }, 2000);
            }
        }">

            {{-- Loading State --}}
            <div x-show="loading" x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">

                {{-- Widget Header Loading --}}
                <div class="mb-6 animate-pulse">
                    <div class="flex items-center justify-between">
                        <div class="space-y-2">
                            <div class="h-6 bg-gray-200 rounded w-48 shimmer"></div>
                            <div class="h-4 bg-gray-200 rounded w-32 shimmer"></div>
                        </div>
                        <div class="flex space-x-2">
                            <div class="h-8 bg-gray-200 rounded-lg w-20 shimmer"></div>
                            <div class="h-8 bg-gray-200 rounded-lg w-24 shimmer"></div>
                        </div>
                    </div>
                </div>

                {{-- Stats Cards Loading --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                    @for($i = 0; $i < 4; $i++)
                    <div class="bg-white rounded-lg border border-gray-200 p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div class="h-8 w-8 bg-blue-200 rounded shimmer"></div>
                            <div class="h-4 w-4 bg-gray-200 rounded shimmer"></div>
                        </div>
                        <div class="space-y-2">
                            <div class="h-6 bg-gray-200 rounded w-16 shimmer"></div>
                            <div class="h-3 bg-gray-200 rounded w-20 shimmer"></div>
                        </div>
                    </div>
                    @endfor
                </div>

                {{-- Course Cards Loading (YouTube Style) --}}
                <x-youtube-loading :cards="6" columns="grid-cols-1 md:grid-cols-2 lg:grid-cols-3" />
            </div>

            {{-- Actual Content --}}
            <div x-show="!loading" x-transition:enter="transition ease-out duration-500"
                 x-transition:enter-start="opacity-0 transform translate-y-4"
                 x-transition:enter-end="opacity-100 transform translate-y-0">

                @if(!$isLoading)
                    @php $data = $this->getViewData(); @endphp

                    {{-- Header --}}
                    <div class="mb-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Course Overview</h3>
                                <p class="text-sm text-gray-500">Manage and monitor your courses</p>
                            </div>
                            <div class="flex space-x-2">
                                <x-filament::button
                                    color="gray"
                                    size="sm"
                                    icon="heroicon-o-arrow-path"
                                    wire:click="$refresh"
                                >
                                    Refresh
                                </x-filament::button>
                                <x-filament::button
                                    size="sm"
                                    icon="heroicon-o-plus"
                                    href="{{ route('filament.admin.resources.courses.create') }}"
                                >
                                    New Course
                                </x-filament::button>
                            </div>
                        </div>
                    </div>

                    {{-- Stats Cards --}}
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                        {{-- Total Courses --}}
                        <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg border border-blue-200 p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="p-2 bg-blue-500 rounded-lg">
                                    <x-heroicon-o-academic-cap class="h-4 w-4 text-white" />
                                </div>
                                <x-heroicon-o-arrow-trending-up class="h-4 w-4 text-blue-600" />
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-blue-900">{{ $data['totalCourses'] }}</div>
                                <div class="text-sm text-blue-700">Total Courses</div>
                            </div>
                        </div>

                        {{-- Active Courses --}}
                        <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-lg border border-green-200 p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="p-2 bg-green-500 rounded-lg">
                                    <x-heroicon-o-check-circle class="h-4 w-4 text-white" />
                                </div>
                                <x-heroicon-o-arrow-trending-up class="h-4 w-4 text-green-600" />
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-green-900">{{ $data['activeCourses'] }}</div>
                                <div class="text-sm text-green-700">Active Courses</div>
                            </div>
                        </div>

                        {{-- Total Students --}}
                        <div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg border border-purple-200 p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="p-2 bg-purple-500 rounded-lg">
                                    <x-heroicon-o-users class="h-4 w-4 text-white" />
                                </div>
                                <x-heroicon-o-arrow-trending-up class="h-4 w-4 text-purple-600" />
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-purple-900">{{ $data['totalStudents'] }}</div>
                                <div class="text-sm text-purple-700">Total Students</div>
                            </div>
                        </div>

                        {{-- Growth Rate --}}
                        <div class="bg-gradient-to-r from-orange-50 to-orange-100 rounded-lg border border-orange-200 p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="p-2 bg-orange-500 rounded-lg">
                                    <x-heroicon-o-chart-bar class="h-4 w-4 text-white" />
                                </div>
                                <x-heroicon-o-arrow-trending-up class="h-4 w-4 text-orange-600" />
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-orange-900">{{ $data['courseStats']['growth_rate'] }}%</div>
                                <div class="text-sm text-orange-700">Growth Rate</div>
                            </div>
                        </div>
                    </div>

                    {{-- Recent Courses Grid (YouTube Style) --}}
                    <div class="space-y-6">
                        <h4 class="text-lg font-semibold text-gray-900">Recent Courses</h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($data['recentCourses'] as $course)
                            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg transition-all duration-300 group">
                                {{-- Thumbnail --}}
                                <div class="relative aspect-video bg-gray-100">
                                    @if($course['thumbnail'])
                                        <img src="{{ $course['thumbnail'] }}" alt="{{ $course['title'] }}"
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center">
                                            <x-heroicon-o-academic-cap class="h-12 w-12 text-white" />
                                        </div>
                                    @endif

                                    {{-- Duration Badge --}}
                                    <div class="absolute bottom-2 right-2 bg-black/70 text-white text-xs px-2 py-1 rounded">
                                        {{ $course['duration'] }}
                                    </div>

                                    {{-- Play Button --}}
                                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                        <div class="bg-white/90 rounded-full p-3">
                                            <x-heroicon-o-play class="h-6 w-6 text-gray-900" />
                                        </div>
                                    </div>
                                </div>

                                {{-- Content --}}
                                <div class="p-4 space-y-3">
                                    {{-- Title --}}
                                    <h5 class="font-medium text-gray-900 line-clamp-2 group-hover:text-blue-600 transition-colors">
                                        {{ $course['title'] }}
                                    </h5>

                                    {{-- Description --}}
                                    <p class="text-sm text-gray-600 line-clamp-2">
                                        {{ $course['description'] }}
                                    </p>

                                    {{-- Stats --}}
                                    <div class="flex items-center justify-between text-sm text-gray-500">
                                        <div class="flex items-center space-x-4">
                                            <span>{{ $course['students_count'] }} students</span>
                                            <span>{{ $course['level'] }}</span>
                                        </div>
                                        <div class="flex items-center space-x-1">
                                            <x-heroicon-o-star class="h-4 w-4 text-yellow-400" />
                                            <span>{{ $course['rating'] }}</span>
                                        </div>
                                    </div>

                                    {{-- Price & Actions --}}
                                    <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                                        <div class="text-lg font-semibold text-gray-900">
                                            ${{ number_format($course['price'], 2) }}
                                        </div>
                                        <div class="flex space-x-2">
                                            <x-filament::button
                                                color="gray"
                                                size="sm"
                                                icon="heroicon-o-eye"
                                                href="{{ route('filament.admin.resources.courses.view', $course['id']) }}"
                                            >
                                                View
                                            </x-filament::button>
                                            <x-filament::button
                                                size="sm"
                                                icon="heroicon-o-pencil"
                                                href="{{ route('filament.admin.resources.courses.edit', $course['id']) }}"
                                            >
                                                Edit
                                            </x-filament::button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        {{-- View All Button --}}
                        <div class="text-center pt-4">
                            <x-filament::button
                                color="gray"
                                outlined
                                href="{{ route('filament.admin.resources.courses.index') }}"
                            >
                                View All Courses
                            </x-filament::button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.shimmer {
    background: linear-gradient(
        90deg,
        #f1f5f9 0%,
        #e2e8f0 20%,
        #e2e8f0 40%,
        #f1f5f9 100%
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
</style>
