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
                        <div class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-xl border border-slate-200 p-6 hover:shadow-md transition-all duration-300">
                            <div class="flex items-center justify-between mb-4">
                                <div class="p-3 bg-blue-100 rounded-xl">
                                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                                <div class="text-xs text-slate-500 font-medium">Total</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-slate-800 mb-1">{{ $data['totalCourses'] }}</div>
                                <div class="text-sm text-slate-600">Available Courses</div>
                            </div>
                        </div>
                        
                        {{-- Total Enrollments --}}
                        <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl border border-emerald-200 p-6 hover:shadow-md transition-all duration-300">
                            <div class="flex items-center justify-between mb-4">
                                <div class="p-3 bg-emerald-100 rounded-xl">
                                    <svg class="h-6 w-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <div class="text-xs text-emerald-600 font-medium">+{{ $data['enrollmentStats']['this_month'] }}</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-slate-800 mb-1">{{ $data['totalEnrollments'] }}</div>
                                <div class="text-sm text-slate-600">Student Enrollments</div>
                            </div>
                        </div>
                        
                        {{-- Completed Courses --}}
                        <div class="bg-gradient-to-br from-violet-50 to-violet-100 rounded-xl border border-violet-200 p-6 hover:shadow-md transition-all duration-300">
                            <div class="flex items-center justify-between mb-4">
                                <div class="p-3 bg-violet-100 rounded-xl">
                                    <svg class="h-6 w-6 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="text-xs text-violet-600 font-medium">Done</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-slate-800 mb-1">{{ $data['completedCourses'] }}</div>
                                <div class="text-sm text-slate-600">Completed Courses</div>
                            </div>
                        </div>
                        
                        {{-- Completion Rate --}}
                        <div class="bg-gradient-to-br from-amber-50 to-amber-100 rounded-xl border border-amber-200 p-6 hover:shadow-md transition-all duration-300">
                            <div class="flex items-center justify-between mb-4">
                                <div class="p-3 bg-amber-100 rounded-xl">
                                    <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    </svg>
                                </div>
                                <div class="text-xs text-amber-600 font-medium">Rate</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-slate-800 mb-1">{{ $data['enrollmentStats']['completion_rate'] }}%</div>
                                <div class="text-sm text-slate-600">Completion Rate</div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Recent Enrollments Table --}}
                    <div class="space-y-6">
                        <div class="bg-slate-50/50 rounded-xl border border-slate-200 overflow-hidden shadow-sm">
                            <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-slate-100">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-lg font-semibold text-slate-800">Recent Enrollments</h4>
                                        <p class="text-sm text-slate-600">Latest student course enrollments</p>
                                    </div>
                                    <div class="text-xs text-slate-500 bg-slate-200 px-3 py-1 rounded-full">
                                        {{ count($data['recentEnrollments']) }} entries
                                    </div>
                                </div>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-slate-100/70 border-b border-slate-200">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-600 uppercase tracking-wider">Student</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-600 uppercase tracking-wider">Course</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-600 uppercase tracking-wider">Progress</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-600 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-600 uppercase tracking-wider">Enrolled Date</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @forelse($data['recentEnrollments'] as $enrollment)
                                        <tr class="hover:bg-slate-50/50 transition-colors duration-150">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <div class="h-10 w-10 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-full flex items-center justify-center shadow-sm">
                                                        <span class="text-white text-sm font-semibold">
                                                            {{ strtoupper(substr($enrollment['user_name'], 0, 1)) }}
                                                        </span>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-slate-800">{{ $enrollment['user_name'] }}</div>
                                                        <div class="text-sm text-slate-500">{{ $enrollment['user_email'] }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-medium text-slate-800">{{ $enrollment['course_title'] }}</div>
                                                <div class="text-sm text-slate-500">{{ Str::limit($enrollment['course_description'], 50) }}</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="w-full bg-slate-200 rounded-full h-2.5 mb-1">
                                                    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-2.5 rounded-full transition-all duration-300" 
                                                         style="width: {{ $enrollment['progress_percentage'] }}%"></div>
                                                </div>
                                                <div class="text-xs text-slate-600 font-medium">{{ $enrollment['progress_percentage'] }}%</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                @if($enrollment['is_completed'])
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 border border-emerald-200">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        Completed
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700 border border-amber-200">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        In Progress
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm text-slate-600">
                                                {{ $enrollment['enrolled_at']?->format('M d, Y') ?? 'N/A' }}
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-8 text-center">
                                                <div class="text-slate-500">
                                                    <svg class="mx-auto h-12 w-12 text-slate-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                    </svg>
                                                    <p class="text-sm font-medium">No enrollments yet</p>
                                                    <p class="text-xs">Enrollments will appear here once students start joining courses</p>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Popular Courses --}}
                        <div class="bg-slate-50/50 rounded-xl border border-slate-200 overflow-hidden shadow-sm">
                            <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-slate-100">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-lg font-semibold text-slate-800">Popular Courses</h4>
                                        <p class="text-sm text-slate-600">Most enrolled courses this month</p>
                                    </div>
                                    <div class="text-xs text-slate-500 bg-slate-200 px-3 py-1 rounded-full">
                                        {{ count($data['popularCourses']) }} courses
                                    </div>
                                </div>
                            </div>

                            <div class="p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @forelse($data['popularCourses'] as $index => $course)
                                    <div class="bg-gradient-to-br from-slate-50 to-slate-100/50 rounded-xl border border-slate-200 p-5 hover:shadow-md transition-all duration-300 hover:border-slate-300">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center flex-1">
                                                <div class="flex-shrink-0 mr-4">
                                                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-xl flex items-center justify-center shadow-sm">
                                                        <span class="text-white text-sm font-bold">{{ $index + 1 }}</span>
                                                    </div>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <h5 class="font-semibold text-slate-800 mb-1">{{ $course['course_title'] }}</h5>
                                                    <p class="text-sm text-slate-600 line-clamp-2">{{ Str::limit($course['course_description'], 80) }}</p>
                                                </div>
                                            </div>
                                            <div class="ml-4 text-right flex-shrink-0">
                                                <div class="text-2xl font-bold text-indigo-600">{{ $course['enrollment_count'] }}</div>
                                                <div class="text-xs text-slate-500 font-medium">students</div>
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="col-span-full bg-slate-50/30 rounded-xl border border-slate-200 p-8 text-center">
                                        <div class="text-slate-500">
                                            <svg class="mx-auto h-12 w-12 text-slate-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                            </svg>
                                            <p class="text-sm font-medium text-slate-700">No popular courses yet</p>
                                            <p class="text-xs text-slate-500">Course popularity will be tracked as enrollments increase</p>
                                        </div>
                                    </div>
                                    @endforelse
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
