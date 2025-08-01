{{-- resources/views/filament/pages/dashboard.blade.php --}}
<x-filament-panels::page>
    <div x-data="{ 
        loading: true,
        loadingSteps: ['stats', 'charts', 'tables'],
        currentStep: 0,
        
        init() {
            // Simulate progressive loading
            this.simulateLoading();
        },
        
        simulateLoading() {
            // Load stats first
            setTimeout(() => {
                this.currentStep = 1;
            }, 1000);
            
            // Then charts
            setTimeout(() => {
                this.currentStep = 2;
            }, 2000);
            
            // Finally tables and complete
            setTimeout(() => {
                this.loading = false;
            }, 3000);
        }
    }">
        
        {{-- Loading States --}}
        <div x-show="loading" class="space-y-8">
            {{-- Stats Loading --}}
            <div x-show="currentStep >= 0">
                <h2 class="text-xl font-semibold mb-6 text-gray-900">Dashboard Overview</h2>
                <x-filament-loading type="stats" columns="grid-cols-1 md:grid-cols-2 xl:grid-cols-4" />
            </div>
            
            {{-- Charts Loading --}}
            <div x-show="currentStep >= 1" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <x-filament-loading type="chart" />
                <x-filament-loading type="chart" />
            </div>
            
            {{-- Tables Loading --}}
            <div x-show="currentStep >= 2" class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                <x-filament-loading type="table" />
                <x-filament-loading type="table" />
            </div>
        </div>
        
        {{-- Actual Content (shown when loading is complete) --}}
        <div x-show="!loading" x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="opacity-0 transform scale-95" 
             x-transition:enter-end="opacity-100 transform scale-100" 
             class="space-y-8">
             
            {{-- Real Dashboard Content Here --}}
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                {{-- Your actual stats widgets --}}
                @livewire('stats-overview-widget')
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Your actual chart widgets --}}
                @livewire('revenue-chart-widget')
                @livewire('user-growth-chart-widget')
            </div>
            
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                {{-- Your actual table widgets --}}
                @livewire('recent-orders-widget')
                @livewire('top-products-widget')
            </div>
        </div>
        
        {{-- Loading Progress Indicator --}}
        <div x-show="loading" class="fixed bottom-6 right-6 bg-white rounded-lg shadow-lg border border-gray-200 p-4">
            <div class="flex items-center space-x-3">
                <div class="animate-spin h-5 w-5 border-2 border-blue-500 border-t-transparent rounded-full"></div>
                <span class="text-sm text-gray-600">Loading dashboard...</span>
            </div>
            
            {{-- Progress Bar --}}
            <div class="mt-3 w-48 bg-gray-200 rounded-full h-2">
                <div class="bg-blue-500 h-2 rounded-full transition-all duration-500" 
                     :style="`width: ${(currentStep + 1) * 33.33}%`"></div>
            </div>
        </div>
    </div>
</x-filament-panels::page>

<style>
/* Additional loading animation styles */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in-up {
    animation: fadeInUp 0.6s ease-out;
}

/* Staggered loading animation */
.loading-stagger > * {
    opacity: 0;
    animation: fadeInUp 0.6s ease-out forwards;
}

.loading-stagger > *:nth-child(1) { animation-delay: 0.1s; }
.loading-stagger > *:nth-child(2) { animation-delay: 0.2s; }
.loading-stagger > *:nth-child(3) { animation-delay: 0.3s; }
.loading-stagger > *:nth-child(4) { animation-delay: 0.4s; }
</style>
