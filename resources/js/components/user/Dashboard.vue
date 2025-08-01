<template>
    <section class="bg-gradient-to-br from-blue-50 to-purple-50 min-h-screen text-gray-800 font-sans">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 pt-8 sm:pt-16 pb-12">

            <!-- Header yang lebih ramah -->
            <div class="mb-8 text-center">
                <div class="flex items-center justify-center gap-3 mb-4">
                    <div class="animate-bounce">
                        <span class="text-4xl">👋</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-purple-800">
                        Hai, <span class="text-purple-600">{{ userName }}</span>!
                    </h1>
                </div>
                <div class="bg-white/70 backdrop-blur-sm rounded-xl p-4 shadow-sm">
                    <p class="text-lg text-gray-700 leading-relaxed">
                        <span v-if="purchasedCourses.length > 0" class="flex items-center justify-center gap-2">
                            <span>🎯</span>
                            <span>Siap melanjutkan perjalanan coding hari ini?</span>
                        </span>
                        <span v-else class="flex items-center justify-center gap-2">
                            <span>🚀</span>
                            <span>Yuk mulai perjalanan coding pertamamu!</span>
                        </span>
                    </p>
                </div>
            </div>

            <!-- Loading State yang lebih menarik -->
            <div v-if="loading" class="text-center py-16">
                <div class="flex flex-col items-center gap-4">
                    <div class="relative">
                        <div class="w-16 h-16 border-4 border-purple-200 border-t-purple-600 rounded-full animate-spin"></div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-2xl animate-pulse">📚</span>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <p class="text-purple-600 font-semibold text-lg">{{ loadingMessage }}</p>
                        <p class="text-gray-500 text-sm">Sebentar ya, lagi disiapkan nih...</p>
                    </div>
                </div>
            </div>

            <!-- Post-Purchase Success -->
            <div v-else-if="isPostPurchaseScenario && purchasedCourses.length === 0" class="text-center py-16">
                <div class="bg-white rounded-2xl shadow-lg p-8 mx-auto max-w-md">
                    <div class="animate-bounce mb-4">
                        <span class="text-6xl">🎉</span>
                    </div>
                    <h2 class="text-2xl font-bold text-green-600 mb-2">Yeay! Pembelian Berhasil!</h2>
                    <p class="text-gray-600 mb-4">Course kamu sedang disiapkan...</p>
                    <div class="w-12 h-12 border-4 border-green-200 border-t-green-600 rounded-full animate-spin mx-auto"></div>
                </div>
            </div>

            <!-- Success Notification -->
            <div v-if="showPurchaseNotification" class="mb-6">
                <div class="bg-gradient-to-r from-green-400 to-green-500 text-white rounded-2xl p-6 shadow-lg">
                    <div class="flex items-center gap-4">
                        <div class="text-4xl animate-bounce">🎊</div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold mb-1">Selamat!</h3>
                            <p class="text-green-100">{{ latestPurchasedCourse }} sudah siap dipelajari!</p>
                            <p v-if="redirectCountdown > 0" class="text-sm text-green-200 mt-2">
                                Otomatis ke course dalam {{ redirectCountdown }} detik...
                            </p>
                        </div>
                        <button @click="dismissNotification" 
                                class="text-white hover:text-green-200 text-2xl font-bold transition-colors">
                            ×
                        </button>
                    </div>
                </div>
            </div>

            <!-- Course yang Sudah Dibeli -->
            <div v-if="!loading && purchasedCourses && purchasedCourses.length > 0" class="mb-12">
                <div class="flex items-center gap-3 mb-6">
                    <span class="text-3xl">📚</span>
                    <h2 class="text-2xl font-bold text-purple-800">Course Kamu</h2>
                    <span class="bg-purple-100 text-purple-600 px-3 py-1 rounded-full text-sm font-semibold">
                        {{ purchasedCourses.length }} Course
                    </span>
                </div>
                
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="course in purchasedCourses" :key="course.id"
                         class="group bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100">
                        
                        <!-- Course Header -->
                        <div class="p-6 pb-4">
                            <div class="flex items-start justify-between mb-3">
                                <h3 class="font-bold text-lg text-purple-800 group-hover:text-purple-600 transition-colors leading-tight">
                                    {{ course.title }}
                                </h3>
                                <div v-if="activeProgram && activeProgram.id === course.id"
                                     class="flex-shrink-0 w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                            </div>
                            
                            <p class="text-sm text-gray-500 mb-4">
                                Dibeli: {{ formatDate(course.purchased_at) }}
                            </p>

                            <!-- Progress Visual yang Lebih Menarik -->
                            <div v-if="courseProgresses[course.id]" class="mb-4">
                                <div class="flex items-center justify-between text-sm mb-2">
                                    <span class="text-gray-600">Progress</span>
                                    <span class="font-semibold text-purple-600">
                                        {{ Math.round(courseProgresses[course.id].percentage) }}%
                                    </span>
                                </div>
                                
                                <div class="w-full bg-gray-200 h-3 rounded-full overflow-hidden">
                                    <div class="bg-gradient-to-r from-purple-500 to-blue-500 h-3 rounded-full transition-all duration-700 relative"
                                         :style="{ width: courseProgresses[course.id].percentage + '%' }">
                                        <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-between text-xs text-gray-500 mt-2">
                                    <span>{{ courseProgresses[course.id].completed }}/{{ courseProgresses[course.id].total }} materi</span>
                                    <span v-if="courseProgresses[course.id].lastAccessed">
                                        {{ formatRelativeTime(courseProgresses[course.id].lastAccessed) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Status Badges -->
                            <div class="flex flex-wrap gap-2 mb-4">
                                <span class="inline-flex items-center gap-1 bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full font-medium">
                                    <span>✓</span> Aktif
                                </span>
                                <span v-if="activeProgram && activeProgram.id === course.id"
                                      class="inline-flex items-center gap-1 bg-orange-100 text-orange-700 text-xs px-2 py-1 rounded-full font-medium animate-pulse">
                                    <span>🔥</span> Sedang Dipelajari
                                </span>
                                <span v-if="isCourseCompleted(course.id)"
                                      class="inline-flex items-center gap-1 bg-purple-100 text-purple-700 text-xs px-2 py-1 rounded-full font-medium">
                                    <span>🎓</span> Selesai
                                </span>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <div class="px-6 pb-6">
                            <button @click="startLearning(course)"
                                    class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold py-3 px-4 rounded-xl transition-all duration-300 transform hover:scale-105 hover:shadow-lg">
                                <span class="flex items-center justify-center gap-2">
                                    <span>{{ getActionButtonText(course) }}</span>
                                    <span>{{ getActionButtonIcon(course) }}</span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else-if="!loading && (!purchasedCourses || purchasedCourses.length === 0) && !isPostPurchaseScenario" class="mb-12">
                <div class="bg-white rounded-3xl shadow-lg p-12 text-center max-w-2xl mx-auto">
                    <div class="animate-float mb-6">
                        <span class="text-8xl">📖</span>
                    </div>
                    <h2 class="text-3xl font-bold text-purple-800 mb-4">Siap Memulai Perjalanan?</h2>
                    <p class="text-gray-600 text-lg mb-8 leading-relaxed">
                        Ribuan teman sudah memulai journey coding mereka.<br>
                        Yuk pilih course yang sesuai dengan impian kamu!
                    </p>
                    <a href="/course" 
                       class="inline-flex items-center gap-3 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold px-8 py-4 rounded-xl transition-all duration-300 transform hover:scale-105 hover:shadow-xl">
                        <span>Lihat Course Available</span>
                        <span class="text-xl">🚀</span>
                    </a>
                </div>
            </div>

            <!-- Dashboard Cards yang Lebih User-Friendly -->
            <div v-if="!loading && purchasedCourses.length > 0" class="grid md:grid-cols-3 gap-6 mb-10">
                
                <!-- Program Aktif -->
                <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-500 rounded-xl flex items-center justify-center">
                            <span class="text-white text-xl">🎯</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800">Course Aktif</h3>
                            <p class="text-sm text-gray-500">Lagi kamu pelajari</p>
                        </div>
                    </div>
                    
                    <div v-if="activeProgram" class="space-y-3">
                        <h4 class="font-semibold text-purple-800 hover:text-purple-600 cursor-pointer transition-colors"
                            @click="startLearning(activeProgram)">
                            {{ activeProgram.title }}
                        </h4>
                        
                        <div v-if="currentProgress.total > 0" class="space-y-2">
                            <div class="w-full bg-gray-200 h-2 rounded-full">
                                <div class="bg-gradient-to-r from-purple-500 to-indigo-500 h-2 rounded-full transition-all duration-500"
                                     :style="{ width: currentProgress.percentage + '%' }"></div>
                            </div>
                            <p class="text-sm text-gray-600">
                                {{ currentProgress.completed }}/{{ currentProgress.total }} materi 
                                ({{ Math.round(currentProgress.percentage) }}%)
                            </p>
                        </div>
                        
                        <div class="flex gap-2">
                            <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full font-medium">
                                Aktif 🔥
                            </span>
                        </div>
                    </div>
                    
                    <div v-else class="text-center py-4">
                        <p class="text-gray-500 mb-3">Belum ada course aktif</p>
                        <a href="/course" class="text-purple-600 hover:text-purple-700 font-medium text-sm">
                            Pilih Course →
                        </a>
                    </div>
                </div>

                <!-- Progress Keseluruhan -->
                <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-blue-500 rounded-xl flex items-center justify-center">
                            <span class="text-white text-xl">📈</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800">Progress Belajar</h3>
                            <p class="text-sm text-gray-500">Total kemajuan kamu</p>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="relative">
                            <div class="w-full bg-gray-200 h-4 rounded-full">
                                <div class="bg-gradient-to-r from-green-500 to-blue-500 h-4 rounded-full transition-all duration-700 relative"
                                     :style="{ width: currentProgress.percentage + '%' }">
                                    <div class="absolute inset-0 bg-white/20 animate-pulse rounded-full"></div>
                                </div>
                            </div>
                            <span class="absolute right-2 top-0 text-xs font-bold text-gray-700 h-4 flex items-center">
                                {{ Math.round(currentProgress.percentage) }}%
                            </span>
                        </div>
                        
                        <p class="text-sm text-gray-600">
                            {{ currentProgress.completed }} dari {{ currentProgress.total }} materi selesai
                        </p>
                        
                        <div v-if="overallStats.totalCourses > 1" class="text-xs text-gray-500 bg-gray-50 rounded-lg p-2">
                            Total: {{ overallStats.totalCompleted }}/{{ overallStats.totalMaterials }} materi
                            dari {{ overallStats.totalCourses }} course
                        </div>
                    </div>
                </div>

                <!-- Level & Achievement -->
                <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl flex items-center justify-center">
                            <span class="text-white text-xl">🏆</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800">Level Kamu</h3>
                            <p class="text-sm text-gray-500">Keep up the great work!</p>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-2xl text-purple-700">Level {{ userLevel }}</span>
                            <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-sm font-bold">
                                {{ userXP }} XP
                            </span>
                        </div>
                        
                        <div class="space-y-2">
                            <div class="w-full bg-gray-200 h-3 rounded-full">
                                <div class="bg-gradient-to-r from-yellow-500 to-orange-500 h-3 rounded-full transition-all duration-500"
                                     :style="{ width: levelProgress + '%' }"></div>
                            </div>
                            <p class="text-xs text-gray-500 text-center">
                                {{ levelProgress }}% menuju Level {{ userLevel + 1 }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div v-if="!loading" class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
                <a :href="routes.course"
                   class="group bg-white rounded-2xl shadow-lg hover:shadow-xl p-6 text-center transition-all duration-300 transform hover:scale-105">
                    <div class="text-4xl mb-3 group-hover:animate-bounce">📚</div>
                    <h3 class="font-bold text-purple-700 mb-1">Course</h3>
                    <p class="text-xs text-gray-500">Lihat semua course</p>
                </a>
                
                <a :href="routes.tanyaMentor"
                   class="group bg-white rounded-2xl shadow-lg hover:shadow-xl p-6 text-center transition-all duration-300 transform hover:scale-105">
                    <div class="text-4xl mb-3 group-hover:animate-bounce">💬</div>
                    <h3 class="font-bold text-purple-700 mb-1">Tanya Mentor</h3>
                    <p class="text-xs text-gray-500">Diskusi & bantuan</p>
                </a>
                
                <a :href="routes.certificate"
                   class="group bg-white rounded-2xl shadow-lg hover:shadow-xl p-6 text-center transition-all duration-300 transform hover:scale-105">
                    <div class="text-4xl mb-3 group-hover:animate-bounce">🎓</div>
                    <h3 class="font-bold text-purple-700 mb-1">Sertifikat</h3>
                    <p class="text-xs text-gray-500">Lihat pencapaian</p>
                </a>
                
                <a :href="routes.reward"
                   class="group bg-white rounded-2xl shadow-lg hover:shadow-xl p-6 text-center transition-all duration-300 transform hover:scale-105">
                    <div class="text-4xl mb-3 group-hover:animate-bounce">🎁</div>
                    <h3 class="font-bold text-purple-700 mb-1">Reward</h3>
                    <p class="text-xs text-gray-500">Hadiah & voucher</p>
                </a>
            </div>

            <!-- Motivational Stats -->
            <div v-if="!loading && purchasedCourses.length > 0" 
                 class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-3xl p-8 text-white text-center mb-10">
                <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
                    <div class="text-center">
                        <div class="text-3xl font-bold">{{ overallStats.totalCompleted }}</div>
                        <div class="text-purple-200 text-sm">Materi Selesai</div>
                    </div>
                    <div class="hidden sm:block w-px h-12 bg-purple-400"></div>
                    <div class="text-center">
                        <div class="text-3xl font-bold">{{ completedCoursesCount }}</div>
                        <div class="text-purple-200 text-sm">Course Selesai</div>
                    </div>
                    <div class="hidden sm:block w-px h-12 bg-purple-400"></div>
                    <div class="text-center">
                        <div class="text-3xl font-bold">Level {{ userLevel }}</div>
                        <div class="text-purple-200 text-sm">Your Level</div>
                    </div>
                </div>
                <p class="mt-4 text-purple-100">
                    🎉 Kamu sudah bergabung dengan <strong>12,000+</strong> Gen Z yang belajar coding!
                </p>
            </div>

            <!-- Notifikasi dengan Design yang Lebih Menarik -->
            <div v-if="!loading" class="bg-white rounded-3xl shadow-lg p-8">
                <div class="flex items-center gap-3 mb-6">
                    <span class="text-3xl">🔔</span>
                    <h2 class="text-2xl font-bold text-purple-800">Update Terbaru</h2>
                </div>
                
                <div class="space-y-4">
                    <div v-for="notification in notifications.slice(0, 3)" :key="notification.id"
                         class="flex items-start gap-4 p-4 bg-gray-50 rounded-2xl hover:bg-gray-100 transition-colors">
                        <div class="flex-shrink-0">
                            <span :class="notification.badgeClass">{{ notification.type }}</span>
                        </div>
                        <div class="flex-1">
                            <p class="text-gray-700">{{ notification.message }}</p>
                            <p v-if="notification.timestamp" class="text-xs text-gray-400 mt-1">
                                {{ formatRelativeTime(notification.timestamp) }}
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-6">
                    <button class="text-purple-600 hover:text-purple-700 font-medium text-sm">
                        Lihat Semua Notifikasi →
                    </button>
                </div>
            </div>

            <!-- Debug Buttons (hanya untuk development) -->
            <div v-if="!loading && purchasedCourses.length === 0 && !isPostPurchaseScenario" 
                 class="mt-8 text-center space-x-4">
                <button @click="manualRefreshCourses"
                        class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-xl font-medium transition-colors">
                    🔄 Refresh Data
                </button>
                <button @click="debugCourseData"
                        class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-xl font-medium transition-colors">
                    🔧 Debug
                </button>
            </div>
        </div>
    </section>
</template>

<script>
import axios from 'axios';

export default {
    name: 'UserFriendlyDashboard',
    props: {
        user: {
            type: Object,
            default: () => ({ name: '' })
        },
        laravelRoutes: {
            type: Object,
            default: () => ({
                dashboard: '/dashboard',
                profileShow: '/user/profile',
                passwordChange: '/user/password/change',
                reward: '/user/reward',
                logout: '/logout',
                certificate: '/certificate',
                course: '/course',
                tanyaMentor: '/tanya_mentor',
            })
        },
        assetBaseUrl: {
            type: String,
            default: '/'
        }
    },

    data() {
        return {
            // UI State
            loading: true,
            loadingMessage: 'Menyiapkan dashboard kamu...',

            // Course Data
            purchasedCourses: [],
            activeProgram: null,
            courseProgresses: {},
            courseStructures: {},

            // Progress Data
            userLevel: 1,
            levelProgress: 0,
            userXP: 100,

            // Notification Data
            showPurchaseNotification: false,
            latestPurchasedCourse: '',
            redirectCountdown: 0,
            redirectTimer: null,
            notifications: [
                {
                    id: 1,
                    type: 'Selamat Datang',
                    badgeClass: 'inline-block bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-medium',
                    message: '🎉 Selamat datang di Dunia Coding! Siap memulai journey coding yang seru?',
                    timestamp: new Date().toISOString()
                }
            ]
        };
    },

    computed: {
        userName() {
            return this.getUserName();
        },
        routes() {
            return this.laravelRoutes;
        },
        currentProgress() {
            if (!this.activeProgram || !this.courseProgresses[this.activeProgram.id]) {
                return { completed: 0, total: 0, percentage: 0 };
            }
            return this.courseProgresses[this.activeProgram.id];
        },
        isPostPurchaseScenario() {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get('payment_success') === 'true';
        },
        overallStats() {
            let totalCompleted = 0;
            let totalMaterials = 0;
            let totalCourses = this.purchasedCourses.length;

            Object.values(this.courseProgresses).forEach(progress => {
                totalCompleted += progress.completed || 0;
                totalMaterials += progress.total || 0;
            });

            return {
                totalCompleted,
                totalMaterials,
                totalCourses,
                overallPercentage: totalMaterials > 0 ? (totalCompleted / totalMaterials) * 100 : 0
            };
        },
        completedCoursesCount() {
            return Object.values(this.courseProgresses).filter(progress =>
                progress.total > 0 && progress.completed === progress.total
            ).length;
        }
    },

    async mounted() {
        console.log('🎯 Dashboard starting...');
        this.setupAxios();
        this.setupProgressListeners();

        const urlParams = new URLSearchParams(window.location.search);
        const paymentSuccess = urlParams.get('payment_success');
        const courseTitle = urlParams.get('course_title');

        if (paymentSuccess === 'true' && courseTitle) {
            await this.handlePostPurchaseFlow(decodeURIComponent(courseTitle));
        } else {
            await this.loadNormalDashboard();
        }
    },

    beforeUnmount() {
        this.cleanupListeners();
    },

    methods: {
        // Setup methods
        setupAxios() {
            if (!axios.defaults.baseURL) {
                axios.defaults.baseURL = 'https://itqom-platform-aa0ffce6a276.herokuapp.com';
            }

            axios.defaults.headers.common['Accept'] = 'application/json';
            axios.defaults.headers.common['Content-Type'] = 'application/json';

            const token = localStorage.getItem('authToken');
            if (token) {
                axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            }
        },

        setupProgressListeners() {
            window.addEventListener('courseProgressUpdated', this.handleCourseProgressUpdate);
            window.addEventListener('courseProgressSync', this.handleCourseProgressSync);
            window.addEventListener('courseStructureLoaded', this.handleCourseStructureLoaded);
            window.addEventListener('courseCompleted', this.handleCourseCompleted);
        },

        cleanupListeners() {
            window.removeEventListener('courseProgressUpdated', this.handleCourseProgressUpdate);
            window.removeEventListener('courseProgressSync', this.handleCourseProgressSync);
            window.removeEventListener('courseStructureLoaded', this.handleCourseStructureLoaded);
            window.removeEventListener('courseCompleted', this.handleCourseCompleted);

            if (this.redirectTimer) {
                clearInterval(this.redirectTimer);
            }
        },

        // User-friendly helper methods
        getUserName() {
            try {
                const userData = localStorage.getItem('user');
                if (userData) {
                    const user = JSON.parse(userData);
                    return user.name || 'Sobat Coding';
                }
            } catch (e) {
                console.error('Error getting user name:', e);
            }
            return 'Sobat Coding';
        },

        formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
        },

        formatRelativeTime(dateString) {
            if (!dateString) return '';
            
            const now = new Date();
            const date = new Date(dateString);
            const diffInSeconds = Math.floor((now - date) / 1000);
            
            if (diffInSeconds < 60) return 'Baru saja';
            if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} menit lalu`;
            if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} jam lalu`;
            if (diffInSeconds < 604800) return `${Math.floor(diffInSeconds / 86400)} hari lalu`;
            
            return this.formatDate(dateString);
        },

        getActionButtonText(course) {
            if (this.isCourseCompleted(course.id)) {
                return 'Lihat Sertifikat';
            } else if (this.activeProgram && this.activeProgram.id === course.id) {
                return 'Lanjutkan Belajar';
            } else {
                return 'Mulai Belajar';
            }
        },

        getActionButtonIcon(course) {
            if (this.isCourseCompleted(course.id)) {
                return '🎓';
            } else if (this.activeProgram && this.activeProgram.id === course.id) {
                return '▶️';
            } else {
                return '🚀';
            }
        },

        isCourseCompleted(courseId) {
            const progress = this.courseProgresses[courseId];
            return progress && progress.total > 0 && progress.completed === progress.total;
        },

        // Progress event handlers
        handleCourseProgressUpdate(event) {
            const { courseId, progress } = event.detail;
            console.log('📊 Progress update:', courseId, progress);

            if (progress && typeof progress === 'object') {
                this.$set(this.courseProgresses, courseId, {
                    completed: progress.completed_count || progress.completed || 0,
                    total: progress.total_materials || progress.total || 0,
                    percentage: progress.progress_percentage || progress.percentage || 0,
                    completedMateris: progress.completedMateris || [],
                    lastAccessed: progress.lastUpdated || new Date().toISOString(),
                    materialsSource: progress.materials_source || 'unknown'
                });

                this.updateUserStats();
                this.checkProgressMilestones(courseId, progress);
            }
        },

        handleCourseProgressSync(event) {
            const { courseId, completed, total, percentage, completedMateris, courseTitle, lastAccessed } = event.detail;
            
            this.$set(this.courseProgresses, courseId, {
                completed: completed || 0,
                total: total || 0,
                percentage: percentage || 0,
                completedMateris: completedMateris || [],
                lastAccessed: lastAccessed || new Date().toISOString(),
                courseTitle: courseTitle
            });

            this.updateUserStats();
        },

        handleCourseStructureLoaded(event) {
            const { courseId, title, totalMaterials, hasContent, materialsSource } = event.detail;
            
            this.courseStructures[courseId] = {
                title,
                totalMaterials,
                hasContent,
                materialsSource
            };

            if (!this.courseProgresses[courseId]) {
                this.$set(this.courseProgresses, courseId, {
                    completed: 0,
                    total: totalMaterials,
                    percentage: 0,
                    completedMateris: [],
                    materialsSource: materialsSource
                });
            }
        },

        handleCourseCompleted(event) {
            const { courseId, courseTitle, completedAt } = event.detail;
            
            this.showFriendlyNotification({
                type: 'Selamat! 🎉',
                badgeClass: 'inline-block bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-xs font-medium',
                message: `Wow! Kamu berhasil menyelesaikan "${courseTitle}"! Time to celebrate! 🎊`,
                timestamp: completedAt
            });

            this.updateUserStats();
        },

        // Milestone checking
        checkProgressMilestones(courseId, progress) {
            const percentage = progress.progress_percentage || 0;

            if (percentage === 25) {
                this.showFriendlyNotification({
                    type: 'Milestone ⭐',
                    badgeClass: 'inline-block bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-medium',
                    message: 'Keren! Kamu sudah 25% menguasai course ini! Keep going! 💪'
                });
            } else if (percentage === 50) {
                this.showFriendlyNotification({
                    type: 'Half Way! 🔥',
                    badgeClass: 'inline-block bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-medium',
                    message: 'Amazing! Kamu sudah setengah jalan! Jangan berhenti sekarang! 🚀'
                });
            } else if (percentage === 75) {
                this.showFriendlyNotification({
                    type: 'Almost There! ⚡',
                    badgeClass: 'inline-block bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-xs font-medium',
                    message: 'Woww! Tinggal sedikit lagi! Kamu pasti bisa finish! 🎯'
                });
            }
        },

        showFriendlyNotification(notification) {
            this.notifications.unshift({
                id: Date.now(),
                ...notification,
                timestamp: notification.timestamp || new Date().toISOString()
            });

            // Keep only latest 5 notifications
            if (this.notifications.length > 5) {
                this.notifications = this.notifications.slice(0, 5);
            }
        },

        // Course loading methods
        async handlePostPurchaseFlow(courseTitle) {
            console.log('🎉 Post-purchase flow for:', courseTitle);

            try {
                await this.loadPurchasedCoursesWithRetry(true);

                if (this.purchasedCourses.length > 0) {
                    const purchasedCourse = this.findPurchasedCourse(courseTitle);

                    if (purchasedCourse) {
                        this.activeProgram = purchasedCourse;
                        this.saveActiveProgram();
                        this.showSuccessNotificationWithRedirect(courseTitle, purchasedCourse);
                        this.loadAllCourseProgresses();
                        this.updateUserStats();
                    } else {
                        this.showSuccessNotification(courseTitle);
                        this.cleanUrlParameters();
                    }
                }

            } catch (error) {
                console.error('❌ Post-purchase error:', error);
                this.loading = false;
                this.showFriendlyNotification({
                    type: 'Oops! 😅',
                    badgeClass: 'inline-block bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-medium',
                    message: 'Ada kendala teknis nih. Coba refresh halaman ya!'
                });
            }
        },

        async loadNormalDashboard() {
            try {
                await this.loadPurchasedCoursesWithRetry(false);
                this.loadActiveProgram();
                this.updateUserStats();
            } catch (error) {
                console.error('❌ Dashboard loading error:', error);
                this.showFriendlyNotification({
                    type: 'Info 💡',
                    badgeClass: 'inline-block bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-medium',
                    message: 'Sedang ada gangguan koneksi. Coba refresh ya!'
                });
            } finally {
                this.loading = false;
            }
        },

        async loadPurchasedCoursesWithRetry(isPostPurchase = false) {
            const maxRetries = isPostPurchase ? 5 : 1;
            const retryDelay = isPostPurchase ? 2000 : 1000;
            
            const friendlyMessages = [
                'Tunggu sebentar ya...',
                'Masih loading nih...',
                'Hampir selesai...',
                'Bentar lagi siap...',
                'Almost there...'
            ];

            for (let attempt = 1; attempt <= maxRetries; attempt++) {
                try {
                    if (isPostPurchase && attempt > 1) {
                        this.loadingMessage = friendlyMessages[attempt - 1] || 'Loading...';
                    }

                    await this.loadPurchasedCourses();

                    if (this.purchasedCourses.length > 0) {
                        return;
                    }

                    if (isPostPurchase && attempt < maxRetries) {
                        await this.delay(retryDelay);
                        continue;
                    }

                    break;

                } catch (error) {
                    console.error(`❌ Attempt ${attempt} failed:`, error);

                    if (attempt === maxRetries) {
                        throw error;
                    }

                    if (isPostPurchase) {
                        this.loadingMessage = `Mencoba lagi... (${attempt}/${maxRetries})`;
                        await this.delay(retryDelay);
                    }
                }
            }
        },

        async loadPurchasedCourses() {
            try {
                const token = localStorage.getItem('authToken');
                if (!token) {
                    this.updateEmptyStateNotifications();
                    return;
                }

                const response = await axios.get('/api/my-courses', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    timeout: 8000
                });

                if (response.data.success) {
                    this.purchasedCourses = response.data.courses || [];
                    
                    if (this.purchasedCourses.length > 0) {
                        this.updatePurchasedStateNotifications();
                        await this.loadAllCourseProgresses();
                        this.$nextTick(() => {
                            this.$forceUpdate();
                        });
                    } else {
                        this.activeProgram = null;
                        this.updateEmptyStateNotifications();
                    }
                } else {
                    this.purchasedCourses = [];
                    this.updateEmptyStateNotifications();
                }

            } catch (error) {
                console.error('💥 Error loading courses:', error);

                if (error.response?.status === 401) {
                    localStorage.removeItem('authToken');
                    localStorage.removeItem('user');
                    delete axios.defaults.headers.common['Authorization'];
                    this.purchasedCourses = [];
                    this.activeProgram = null;
                    this.updateEmptyStateNotifications();
                } else {
                    throw error;
                }
            }
        },

        async loadAllCourseProgresses() {
            const progressPromises = this.purchasedCourses.map(async (course) => {
                const progress = await this.loadCourseProgress(course.id);
                return { courseId: course.id, progress };
            });

            try {
                const results = await Promise.all(progressPromises);
                results.forEach(({ courseId, progress }) => {
                    this.$set(this.courseProgresses, courseId, progress);
                });
            } catch (error) {
                console.error('❌ Error loading progress:', error);
            }
        },

        async loadCourseProgress(courseId) {
            try {
                const response = await axios.get(`https://itqom-platform-aa0ffce6a276.herokuapp.com/api/course-content/course/${courseId}`);

                if (response.data.success) {
                    const courseData = response.data.data;
                    const materis = courseData.materis || [];
                    const courseStats = courseData.course_stats || {};

                    this.courseStructures[courseId] = {
                        title: courseData.courseDescription?.title,
                        totalMaterials: courseData.totalMateris,
                        hasContent: courseStats.has_content,
                        materialsSource: courseStats.materials_source
                    };

                    if (materis.length === 0) {
                        return {
                            completed: 0,
                            total: courseData.totalMateris || 0,
                            percentage: 0,
                            materialsSource: courseStats.materials_source || 'course_description'
                        };
                    }

                    const progressKey = `course_progress_${courseId}`;
                    let completedMateris = [];

                    try {
                        const savedProgress = localStorage.getItem(progressKey);
                        if (savedProgress) {
                            const progressData = JSON.parse(savedProgress);
                            completedMateris = progressData.completedMateris || [];
                        }
                    } catch (e) {
                        console.error('Error loading progress from localStorage:', e);
                    }

                    const completed = completedMateris.length;
                    const total = materis.length;
                    const percentage = total > 0 ? (completed / total) * 100 : 0;

                    return {
                        completed,
                        total,
                        percentage,
                        completedMateris,
                        materialsSource: courseStats.materials_source || 'course_content'
                    };
                } else {
                    return { completed: 0, total: 0, percentage: 0 };
                }

            } catch (error) {
                console.error(`💥 Error loading progress for course ${courseId}:`, error);
                return { completed: 0, total: 0, percentage: 0 };
            }
        },

        // Course actions
        async startLearning(course) {
            console.log('🚀 Starting learning:', course.title);

            this.activeProgram = course;
            this.activeProgram.last_accessed = new Date().toISOString();
            this.saveActiveProgram();

            this.showFriendlyNotification({
                type: 'Let\'s Go! 🚀',
                badgeClass: 'inline-block bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-medium',
                message: `Siap belajar "${course.title}"! Semangat ya! 💪`
            });

            this.updateUserStats();
            window.location.href = `/course/${course.id}`;
        },

        findPurchasedCourse(courseTitle) {
            return this.purchasedCourses.find(course =>
                course.title.toLowerCase().includes(courseTitle.toLowerCase()) ||
                courseTitle.toLowerCase().includes(course.title.toLowerCase())
            );
        },

        // Notification methods
        showSuccessNotificationWithRedirect(courseTitle, course) {
            this.showPurchaseNotification = true;
            this.latestPurchasedCourse = courseTitle;
            this.redirectCountdown = 3;

            this.showFriendlyNotification({
                type: 'Berhasil! 🎉',
                badgeClass: 'inline-block bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-medium',
                message: `Yeay! "${courseTitle}" sudah siap dipelajari! Otomatis ke course dalam beberapa detik...`
            });

            this.redirectTimer = setInterval(() => {
                this.redirectCountdown--;
                if (this.redirectCountdown <= 0) {
                    clearInterval(this.redirectTimer);
                    this.redirectToCourse(course);
                }
            }, 1000);

            this.cleanUrlParameters();
        },

        showSuccessNotification(courseTitle) {
            this.showPurchaseNotification = true;
            this.latestPurchasedCourse = courseTitle;

            this.showFriendlyNotification({
                type: 'Berhasil! 🎊',
                badgeClass: 'inline-block bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-medium',
                message: `Selamat! Kamu berhasil membeli "${courseTitle}"! Selamat belajar ya! 🚀`
            });

            this.updateUserStats();
        },

        dismissNotification() {
            this.showPurchaseNotification = false;

            if (this.redirectTimer) {
                clearInterval(this.redirectTimer);
                this.redirectTimer = null;
            }

            this.cleanUrlParameters();
        },

        redirectToCourse(course) {
            window.location.href = `/course/${course.id}`;
        },

        // State management
        saveActiveProgram() {
            if (this.activeProgram) {
                try {
                    localStorage.setItem('active_program', JSON.stringify(this.activeProgram));
                } catch (error) {
                    console.error('Error saving active program:', error);
                }
            }
        },

        loadActiveProgram() {
            try {
                const savedProgram = localStorage.getItem('active_program');
                if (savedProgram) {
                    const programData = JSON.parse(savedProgram);
                    const existingCourse = this.purchasedCourses.find(course => course.id === programData.id);
                    if (existingCourse) {
                        this.activeProgram = existingCourse;
                    } else {
                        localStorage.removeItem('active_program');
                    }
                }

                if (!this.activeProgram && this.purchasedCourses.length > 0) {
                    this.activeProgram = this.purchasedCourses[0];
                    this.saveActiveProgram();
                }

            } catch (error) {
                console.error('Error loading active program:', error);
                localStorage.removeItem('active_program');
            }
        },

        updateUserStats() {
            const baseXP = 100;
            const courseBonus = this.purchasedCourses.length * 200;
            const progressBonus = this.overallStats.totalCompleted * 50;
            const completionBonus = this.completedCoursesCount * 500;

            this.userXP = baseXP + courseBonus + progressBonus + completionBonus;
            this.userLevel = Math.floor(this.userXP / 500) + 1;
            const currentLevelXP = (this.userLevel - 1) * 500;
            const nextLevelXP = this.userLevel * 500;
            this.levelProgress = Math.floor(((this.userXP - currentLevelXP) / (nextLevelXP - currentLevelXP)) * 100);
        },

        updateEmptyStateNotifications() {
            this.notifications = [
                {
                    id: 1,
                    type: 'Selamat Datang! 👋',
                    badgeClass: 'inline-block bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-medium',
                    message: 'Hai! Selamat datang di Dunia Coding! Siap memulai adventure coding yang seru? 🎮',
                    timestamp: new Date().toISOString()
                },
                {
                    id: 2,
                    type: 'Course Baru! 📚',
                    badgeClass: 'inline-block bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-medium',
                    message: 'Ada course baru yang keren banget nih! Langsung cek dan dapatkan early bird discount! 🎁',
                    timestamp: new Date().toISOString()
                },
                {
                    id: 3,
                    type: 'Promo Spesial! 💰',
                    badgeClass: 'inline-block bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-medium',
                    message: 'Psst... Ada promo khusus 20% off untuk course pertama kamu! Don\'t miss it! ⏰',
                    timestamp: new Date().toISOString()
                }
            ];
        },

        updatePurchasedStateNotifications() {
            this.notifications = [
                {
                    id: 1,
                    type: 'Welcome Aboard! 🚀',
                    badgeClass: 'inline-block bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-xs font-medium',
                    message: 'Congrats! Kamu udah resmi jadi bagian dari Dunia Coding! Ready to level up? 💪',
                    timestamp: new Date().toISOString()
                },
                {
                    id: 2,
                    type: 'Tips Belajar 💡',
                    badgeClass: 'inline-block bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-medium',
                    message: 'Pro tip: Konsisten belajar 30 menit sehari lebih efektif daripada marathon 3 jam seminggu sekali! 📈',
                    timestamp: new Date().toISOString()
                },
                {
                    id: 3,
                    type: 'Mentoring 👨‍🏫',
                    badgeClass: 'inline-block bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-medium',
                    message: 'Jangan lupa ikut sesi mentoring ya! Banyak insight menarik dari mentor berpengalaman! 🎯',
                    timestamp: new Date().toISOString()
                }
            ];
        },

        // Utility methods
        cleanUrlParameters() {
            const url = new URL(window.location);
            url.searchParams.delete('payment_success');
            url.searchParams.delete('course_title');
            window.history.replaceState({}, document.title, url.pathname);
        },

        delay(ms) {
            return new Promise(resolve => setTimeout(resolve, ms));
        },

        // Debug methods
        async manualRefreshCourses() {
            this.loading = true;
            this.loadingMessage = 'Refresh data course...';
            await this.loadPurchasedCoursesWithRetry();
            this.loading = false;
        },

        async debugCourseData() {
            console.log('🔧 Debug started...');
            
            try {
                const token = localStorage.getItem('authToken');
                if (!token) {
                    console.log('❌ No token found');
                    return;
                }

                const [userResponse, coursesResponse] = await Promise.all([
                    axios.get('/api/user', { headers: { 'Authorization': `Bearer ${token}` } }),
                    axios.get('/api/my-courses', { headers: { 'Authorization': `Bearer ${token}` } })
                ]);

                console.log('👤 User:', userResponse.data);
                console.log('📚 Courses:', coursesResponse.data);

                return {
                    user: userResponse.data,
                    courses: coursesResponse.data
                };

            } catch (error) {
                console.error('💥 Debug error:', error);
                return { error: error.message };
            }
        }
    },

    watch: {
        purchasedCourses: {
            handler(newCourses) {
                if (newCourses.length > 0 && !this.activeProgram) {
                    this.activeProgram = newCourses[0];
                    this.saveActiveProgram();
                }
                this.loadAllCourseProgresses();
            },
            deep: true
        },

        activeProgram: {
            handler(newProgram) {
                if (newProgram) {
                    this.saveActiveProgram();
                }
            },
            deep: true
        },

        'overallStats.totalCompleted': {
            handler(newCompleted, oldCompleted) {
                if (newCompleted > oldCompleted) {
                    this.updateUserStats();
                }
            }
        }
    }
};
</script>

<style scoped>
/* Modern animations */
.animate-float {
    animation: float 6s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-12px); }
}

.animate-bounce {
    animation: bounce 1s infinite;
}

@keyframes bounce {
    0%, 20%, 53%, 80%, 100% { transform: translateY(0); }
    40%, 43% { transform: translateY(-30px); }
    70% { transform: translateY(-15px); }
    90% { transform: translateY(-4px); }
}

/* Smooth transitions */
.transition-all {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Progress bar animations */
.progress-bar {
    transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Hover effects */
.group:hover .group-hover\:animate-bounce {
    animation: bounce 1s infinite;
}

/* Custom backdrop blur */
.backdrop-blur-sm {
    backdrop-filter: blur(4px);
}

/* Responsive improvements */
@media (max-width: 640px) {
    .text-3xl { font-size: 1.75rem; }
    .text-2xl { font-size: 1.5rem; }
    .px-8 { padding-left: 1.5rem; padding-right: 1.5rem; }
    .py-4 { padding-top: 0.75rem; padding-bottom: 0.75rem; }
}

/* Loading spinner */
@keyframes spin {
    to { transform: rotate(360deg); }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

/* Pulse animation */
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: .5; }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Card hover effects */
.hover\:scale-105:hover {
    transform: scale(1.05);
}

.hover\:shadow-xl:hover {
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Focus states for accessibility */
button:focus,
a:focus {
    outline: 2px solid #8B5CF6;
    outline-offset: 2px;
}

/* Dark mode support (optional) */
@media (prefers-color-scheme: dark) {
    .bg-gray-50 { background-color: #1f2937; }
    .text-gray-800 { color: #f9fafb; }
    .bg-white { background-color: #374151; }
    .text-gray-600 { color: #d1d5db; }
    .text-gray-500 { color: #9ca3af; }
}
</style>