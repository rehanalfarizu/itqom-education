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

                {{-- Table Loading --}}
                <x-admin-table-loading :rows="6" :columns="4" :showPagination="false" />
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
                                <h3 class="text-lg font-semibold text-gray-900">Course Enrollment Overview</h3>
                                <p class="text-sm text-gray-500">Monitor student enrollments and course performance</p>
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

                        {{-- Total Enrollments --}}
                        <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-lg border border-green-200 p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="p-2 bg-green-500 rounded-lg">
                                    <x-heroicon-o-users class="h-4 w-4 text-white" />
                                </div>
                                <x-heroicon-o-arrow-trending-up class="h-4 w-4 text-green-600" />
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-green-900">{{ $data['totalEnrollments'] }}</div>
                                <div class="text-sm text-green-700">Total Enrollments</div>
                            </div>
                        </div>

                        {{-- Completed Courses --}}
                        <div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg border border-purple-200 p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="p-2 bg-purple-500 rounded-lg">
                                    <x-heroicon-o-check-circle class="h-4 w-4 text-white" />
                                </div>
                                <x-heroicon-o-arrow-trending-up class="h-4 w-4 text-purple-600" />
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-purple-900">{{ $data['completedCourses'] }}</div>
                                <div class="text-sm text-purple-700">Completed Courses</div>
                            </div>
                        </div>

                        {{-- Completion Rate --}}
                        <div class="bg-gradient-to-r from-orange-50 to-orange-100 rounded-lg border border-orange-200 p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="p-2 bg-orange-500 rounded-lg">
                                    <x-heroicon-o-chart-bar class="h-4 w-4 text-white" />
                                </div>
                                <x-heroicon-o-arrow-trending-up class="h-4 w-4 text-orange-600" />
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-orange-900">{{ $data['enrollmentStats']['completion_rate'] }}%</div>
                                <div class="text-sm text-orange-700">Completion Rate</div>
                            </div>
                        </div>
                    </div>

                    {{-- Recent Enrollments Table --}}
                    <div class="space-y-6">
                        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h4 class="text-lg font-semibold text-gray-900">Recent Enrollments</h4>
                                <p class="text-sm text-gray-500">Latest student course enrollments</p>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50 border-b border-gray-200">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enrolled Date</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($data['recentEnrollments'] as $enrollment)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <div class="h-8 w-8 bg-blue-500 rounded-full flex items-center justify-center">
                                                        <span class="text-white text-sm font-medium">
                                                            {{ strtoupper(substr($enrollment['user_name'], 0, 1)) }}
                                                        </span>
                                                    </div>
                                                    <div class="ml-3">
                                                        <div class="text-sm font-medium text-gray-900">{{ $enrollment['user_name'] }}</div>
                                                        <div class="text-sm text-gray-500">{{ $enrollment['user_email'] }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $enrollment['course_title'] }}</div>
                                                <div class="text-sm text-gray-500">{{ Str::limit($enrollment['course_description'], 50) }}</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $enrollment['progress_percentage'] }}%"></div>
                                                </div>
                                                <div class="text-xs text-gray-500 mt-1">{{ $enrollment['progress_percentage'] }}%</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                @if($enrollment['is_completed'])
                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                        Completed
                                                    </span>
                                                @else
                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        In Progress
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                {{ $enrollment['enrolled_at']?->format('M d, Y') ?? 'N/A' }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Popular Courses --}}
                        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h4 class="text-lg font-semibold text-gray-900">Popular Courses</h4>
                                <p class="text-sm text-gray-500">Most enrolled courses</p>
                            </div>

                            <div class="p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach($data['popularCourses'] as $course)
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <h5 class="font-medium text-gray-900">{{ $course['course_title'] }}</h5>
                                                <p class="text-sm text-gray-600 mt-1">{{ Str::limit($course['course_description'], 60) }}</p>
                                            </div>
                                            <div class="ml-4 text-right">
                                                <div class="text-2xl font-bold text-blue-600">{{ $course['enrollment_count'] }}</div>
                                                <div class="text-xs text-gray-500">enrollments</div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>

<style>
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
