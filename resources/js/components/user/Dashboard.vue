<template>
    <section class="bg-gray-50 min-h-screen text-gray-800 font-sans">
        <div class="max-w-6xl mx-auto px-6 pt-24">

            <div class="mb-10 flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-extrabold text-purple-800 flex items-center gap-2">
                        🚀 Dashboard Kamu <span class="text-lg bg-yellow-200 text-yellow-800 px-2 py-0.5 rounded-full">Gen Z</span>
                    </h1>
                    <p class="text-gray-600 mt-2">
                        Halo, <span class="font-semibold text-purple-700">{{ userName }}</span>!
                        <span class="ml-1">👋</span>
                        {{ purchasedCourses.length > 0 ? 'Siap upgrade skill hari ini? Yuk cek progres dan tantangan barumu!' : 'Yuk mulai perjalanan coding kamu dengan membeli course pertama!' }}
                    </p>
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="loading" class="text-center py-10">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
                <p class="text-gray-600 mt-2">{{ loadingMessage }}</p>
            </div>

            <!-- Post-Purchase Loading State -->
            <div v-else-if="isPostPurchaseScenario && purchasedCourses.length === 0" class="text-center py-10">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div>
                <p class="text-green-600 mt-2 font-semibold">🎉 Pembelian berhasil! Memuat course kamu...</p>
                <p class="text-gray-500 text-sm mt-1">Tunggu sebentar, kamu akan diarahkan ke course...</p>
            </div>

            <!-- Section Course yang Sudah Dibeli - PRIORITAS TERTINGGI -->
            <div v-else-if="!loading && purchasedCourses && purchasedCourses.length > 0" class="mb-10">
                <h2 class="text-2xl font-bold text-purple-800 mb-6 flex items-center gap-2">
                    📚 Course yang Sudah Kamu Beli
                </h2>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="course in purchasedCourses"
                        :key="course.id"
                        class="bg-white shadow-lg rounded-lg p-6 border-l-4 border-purple-500 hover:shadow-xl transition-shadow"
                        :class="{ 'ring-2 ring-purple-400 bg-purple-50': activeProgram && activeProgram.id === course.id }"
                    >
                        <div class="flex items-start gap-4">
                            <div class="flex-1">
                                <h3 class="font-bold text-lg text-purple-800 mb-2">{{ course.title }}</h3>
                                <p class="text-sm text-gray-600 mb-2">
                                    Dibeli: {{ formatDate(course.purchased_at) }}
                                </p>

                                <!-- Enhanced Progress untuk course ini -->
                                <div v-if="courseProgresses[course.id]" class="mb-3">
                                    <div class="w-full bg-gray-200 h-2 rounded-full">
                                        <div
                                            class="bg-gradient-to-r from-green-500 to-blue-500 h-2 rounded-full transition-all duration-300"
                                            :style="{ width: courseProgresses[course.id].percentage + '%' }"
                                        ></div>
                                    </div>
                                    <p class="text-xs text-gray-600 mt-1">
                                        {{ courseProgresses[course.id].completed }} dari {{ courseProgresses[course.id].total }} materi selesai
                                        ({{ Math.round(courseProgresses[course.id].percentage) }}%)
                                    </p>
                                    <p v-if="courseProgresses[course.id].lastAccessed" class="text-xs text-gray-500 mt-1">
                                        Terakhir diakses: {{ formatDate(courseProgresses[course.id].lastAccessed) }}
                                    </p>
                                </div>

                                <!-- Course completion status -->
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="inline-block bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full font-semibold">
                                        ✓ Sudah Dibeli
                                    </span>
                                    <span v-if="activeProgram && activeProgram.id === course.id"
                                          class="inline-block bg-orange-100 text-orange-700 text-xs px-2 py-0.5 rounded-full font-semibold">
                                        🔥 Aktif
                                    </span>
                                    <span v-if="isCourseCompleted(course.id)"
                                          class="inline-block bg-purple-100 text-purple-700 text-xs px-2 py-0.5 rounded-full font-semibold">
                                        🎓 Selesai
                                    </span>
                                </div>

                                <button
                                    @click="startLearning(course)"
                                    class="inline-block bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-bold px-4 py-2 rounded-lg hover:scale-105 transition text-sm"
                                >
                                    {{ getActionButtonText(course) }} 🚀
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section jika belum ada course yang dibeli -->
            <div v-else-if="!loading && (!purchasedCourses || purchasedCourses.length === 0) && !isPostPurchaseScenario" class="mb-10">
                <div class="bg-gradient-to-r from-purple-100 to-indigo-100 rounded-lg p-8 text-center">
                    <span class="text-6xl mb-4 block">📚</span>
                    <h2 class="text-2xl font-bold text-purple-800 mb-4">Belum Ada Course yang Dibeli</h2>
                    <p class="text-gray-600 mb-6">Yuk mulai perjalanan coding kamu! Pilih course yang sesuai dengan minat dan tujuan kariermu.</p>
                    <a href="/course" class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-bold px-6 py-3 rounded-lg hover:scale-105 transition">
                        Lihat Course Available 🚀
                    </a>
                </div>
            </div>

            <!-- Enhanced Notifications -->
            <div v-if="showPurchaseNotification" class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 rounded-lg">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">🎉</span>
                    <div>
                        <p class="font-bold text-green-800">Selamat! Pembelian Anda berhasil!</p>
                        <p class="text-green-700">{{ latestPurchasedCourse }} sudah siap untuk dipelajari.</p>
                        <p class="text-green-600 text-sm mt-1">{{ redirectCountdown > 0 ? `Redirect ke course dalam ${redirectCountdown} detik...` : 'Redirecting...' }}</p>
                    </div>
                    <button
                        @click="dismissNotification"
                        class="ml-auto text-green-600 hover:text-green-800 font-bold"
                    >
                        ✕
                    </button>
                </div>
            </div>

            <!-- Enhanced Dashboard Cards -->
            <div v-if="!loading && purchasedCourses.length > 0" class="grid md:grid-cols-3 gap-6 mb-10">
                <!-- Enhanced Program Aktif -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-purple-700 flex items-center gap-2">🔥 Program Aktif</h3>
                    <div v-if="activeProgram">
                        <button @click="startLearning(activeProgram)" class="block w-full text-left">
                            <h4 class="text-xl font-bold text-purple-800 mt-2 hover:text-purple-600 transition">
                                {{ activeProgram.title }}
                            </h4>
                        </button>
                        <p class="text-sm text-gray-500 mt-1">
                            Terakhir dipelajari: {{ formatDate(activeProgram.last_accessed || activeProgram.purchased_at) }}
                        </p>

                        <!-- Enhanced Progress untuk program aktif -->
                        <div v-if="currentProgress.total > 0" class="mt-3">
                            <div class="w-full bg-gray-200 h-3 rounded-full">
                                <div
                                    class="bg-gradient-to-r from-purple-500 to-indigo-500 h-3 rounded-full transition-all duration-500"
                                    :style="{ width: currentProgress.percentage + '%' }"
                                ></div>
                            </div>
                            <p class="text-xs text-gray-600 mt-1">
                                {{ currentProgress.completed }}/{{ currentProgress.total }} materi ({{ Math.round(currentProgress.percentage) }}%)
                            </p>
                            <p v-if="currentProgress.materialsSource" class="text-xs text-gray-400 mt-1">
                                Source: {{ currentProgress.materialsSource }}
                            </p>
                        </div>

                        <div class="flex gap-2 mt-2">
                            <span class="inline-block bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full">On Fire!</span>
                            <span v-if="isCourseCompleted(activeProgram.id)"
                                  class="inline-block bg-purple-100 text-purple-700 text-xs px-2 py-0.5 rounded-full">🎓 Completed</span>
                        </div>
                    </div>
                    <div v-else>
                        <p class="text-lg text-gray-600 mt-2">Belum ada program aktif</p>
                        <a href="/course" class="text-sm text-purple-600 hover:underline">Pilih Course</a>
                    </div>
                </div>

                <!-- Enhanced Progres Belajar -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-purple-700 flex items-center gap-2">📈 Progres Belajar</h3>
                    <div class="w-full bg-gray-200 h-4 rounded-full mt-2 relative overflow-hidden">
                        <div
                            class="bg-gradient-to-r from-purple-500 to-indigo-500 h-4 rounded-full transition-all duration-700"
                            :style="{ width: currentProgress.percentage + '%' }"
                        ></div>
                        <span
                            class="absolute right-3 top-0 text-xs font-bold text-purple-700 h-4 flex items-center"
                        >{{ Math.round(currentProgress.percentage) }}%</span>
                    </div>
                    <p class="text-sm text-gray-600 mt-1">
                        <span>{{ currentProgress.completed }} dari {{ currentProgress.total }} materi selesai</span>
                        <span class="ml-1">🎯</span>
                    </p>
                    <div v-if="overallStats.totalCourses > 1" class="mt-2 text-xs text-gray-500">
                        Total: {{ overallStats.totalCompleted }}/{{ overallStats.totalMaterials }} materi dari {{ overallStats.totalCourses }} course
                    </div>
                    <button
                        v-if="activeProgram"
                        @click="startLearning(activeProgram)"
                        class="mt-3 px-4 py-2 bg-purple-600 text-white rounded-lg font-bold hover:bg-purple-700 transition"
                    >
                        {{ getActionButtonText(activeProgram) }} 🚀
                    </button>
                </div>

                <!-- Enhanced Sesi Selanjutnya -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-purple-700 flex items-center gap-2">⏰ Sesi Selanjutnya</h3>
                    <div v-if="activeProgram">
                        <p class="text-base mt-2 font-bold">{{ nextSessionDate }}</p>
                        <p class="text-sm text-gray-500">
                            Course: <span class="font-semibold text-indigo-700">{{ activeProgram.title }}</span>
                        </p>
                        <p class="text-xs text-gray-400 mt-1">Bareng: <span class="font-semibold text-indigo-700">Mentor Tim</span></p>
                        <div class="flex gap-2 mt-2">
                            <span class="inline-block bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded-full">Jangan Telat!</span>
                            <span v-if="getNextMaterial(activeProgram.id)"
                                  class="inline-block bg-yellow-100 text-yellow-700 text-xs px-2 py-0.5 rounded-full">
                                Next: {{ getNextMaterial(activeProgram.id).judul }}
                            </span>
                        </div>
                    </div>
                    <div v-else>
                        <p class="text-sm text-gray-600 mt-2">Belum ada sesi terjadwal</p>
                        <p class="text-xs text-gray-400">Beli course untuk memulai sesi belajar</p>
                    </div>
                </div>
            </div>

            <!-- Enhanced Stats banner -->
            <div v-if="!loading && purchasedCourses.length > 0" class="bg-gradient-to-r from-indigo-100 to-purple-100 rounded-lg p-4 text-center mb-10">
                <span class="text-purple-800 font-bold">👨‍💻 12.000+</span> Gen Z sudah belajar bareng Dunia Coding!
                <span class="ml-2 text-green-600 font-semibold">#NoMoreSkip</span>
                <div class="mt-2 text-sm text-gray-600">
                    Kamu sudah menyelesaikan <strong>{{ overallStats.totalCompleted }}</strong> materi dari <strong>{{ overallStats.totalMaterials }}</strong> total materi
                </div>
            </div>

            <!-- Enhanced Learning Progress Cards -->
            <div v-if="!loading && purchasedCourses.length > 0" class="grid md:grid-cols-2 gap-6 mb-10">
                <div class="bg-white shadow rounded-lg p-6 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-purple-800">{{ activeProgram ? activeProgram.title : 'Course Aktif' }}</h3>
                        <p class="text-sm text-gray-600">{{ activeProgram ? 'Lanjutkan pembelajaran' : 'Belum ada course aktif' }}</p>
                        <div v-if="activeProgram && currentProgress.total > 0" class="mt-2">
                            <p class="text-xs text-gray-500">Progress: {{ Math.round(currentProgress.percentage) }}%</p>
                            <p v-if="currentProgress.materialsSource" class="text-xs text-gray-400">
                                Materi dari: {{ currentProgress.materialsSource === 'course_content' ? 'Course Content' : 'Course Description' }}
                            </p>
                        </div>
                    </div>
                    <button
                        v-if="activeProgram"
                        @click="startLearning(activeProgram)"
                        class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-bold px-4 py-2 rounded-lg hover:scale-105 transition"
                    >
                        {{ getActionButtonText(activeProgram) }}
                    </button>
                    <a
                        v-else
                        href="/course"
                        class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-bold px-4 py-2 rounded-lg hover:scale-105 transition"
                    >
                        Pilih Course
                    </a>
                </div>

                <div class="bg-white shadow rounded-lg p-6 flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-bold text-purple-800 flex items-center gap-2">🎓 Lihat Sertifikat</h3>
                        <p class="text-sm text-gray-600">
                            {{ completedCoursesCount > 0 ? `${completedCoursesCount} sertifikat siap diunduh` : 'Selesaikan course untuk mendapat sertifikat' }}
                        </p>
                        <div v-if="completedCoursesCount > 0" class="mt-1">
                            <span class="text-xs text-green-600 font-semibold">{{ completedCoursesCount }} course selesai!</span>
                        </div>
                    </div>
                    <a :href="routes.certificate"
                        class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-bold px-4 py-2 rounded-lg hover:scale-105 transition">Lihat</a>
                </div>
            </div>

            <!-- Quick Actions (unchanged) -->
            <div class="grid md:grid-cols-3 gap-6 mb-10">
                <a :href="routes.course"
                    class="bg-white shadow rounded-lg p-6 flex flex-col items-center hover:scale-105 transition group">
                    <span class="text-3xl mb-2 group-hover:animate-bounce">📚</span>
                    <span class="font-bold text-purple-700">Kursus</span>
                    <span class="text-xs text-gray-500 mt-1">Lihat semua kelas & promo terbaru</span>
                </a>
                <a :href="routes.tanyaMentor"
                    class="bg-white shadow rounded-lg p-6 flex flex-col items-center hover:scale-105 transition group">
                    <span class="text-3xl mb-2 group-hover:animate-bounce">💬</span>
                    <span class="font-bold text-purple-700">Tanya Mentor</span>
                    <span class="text-xs text-gray-500 mt-1">Diskusi & Tanya jawab</span>
                </a>
                <a :href="routes.reward"
                    class="bg-white shadow rounded-lg p-6 flex flex-col items-center hover:scale-105 transition group">
                    <span class="text-3xl mb-2 group-hover:animate-bounce">🎁</span>
                    <span class="font-bold text-purple-700">Ambil Reward</span>
                    <span class="text-xs text-gray-500 mt-1">Cek hadiah & voucher</span>
                </a>
            </div>

            <!-- Enhanced Level Progress -->
            <div v-if="!loading && purchasedCourses.length > 0" class="bg-white shadow rounded-lg p-6 flex items-center gap-4 mb-10">
                <div>
                    <div class="text-xs text-gray-500 mb-1">Level Kamu</div>
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-purple-700 text-lg">Level {{ userLevel }}</span>
                        <div class="w-40 bg-gray-200 h-3 rounded-full">
                            <div class="bg-gradient-to-r from-purple-500 to-indigo-500 h-3 rounded-full" :style="{ width: levelProgress + '%' }">
                            </div>
                        </div>
                        <span class="text-xs text-gray-500">{{ levelProgress }}% ke Level {{ userLevel + 1 }}</span>
                    </div>
                    <div class="mt-1 text-xs text-gray-400">
                        Berdasarkan {{ overallStats.totalCompleted }} materi selesai dari {{ overallStats.totalCourses }} course
                    </div>
                </div>
                <span class="ml-auto bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full text-xs font-bold">XP: {{ userXP }}</span>
            </div>

            <!-- Enhanced Notifications -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-2xl font-bold text-purple-800 mb-4 flex items-center gap-2">🔔 Notifikasi Terbaru</h2>
                <ul class="list-disc pl-6 text-sm text-gray-700 space-y-2">
                    <li v-for="notification in notifications" :key="notification.id">
                        <span :class="notification.badgeClass">{{ notification.type }}</span>
                        {{ notification.message }}
                        <span v-if="notification.timestamp" class="text-xs text-gray-400 ml-2">
                            {{ formatDate(notification.timestamp) }}
                        </span>
                    </li>
                </ul>
                <a href="#" class="block text-right text-purple-700 hover:underline mt-2 text-sm font-bold">Lihat Semua Notifikasi</a>
            </div>

            <!-- Enhanced Leaderboard -->
            <div v-if="!loading && purchasedCourses.length > 0" class="bg-white shadow rounded-lg p-6 mt-8">
                <h3 class="text-lg font-bold text-purple-800 mb-2 flex items-center gap-2">🏆 Leaderboard Mingguan</h3>
                <ol class="list-decimal pl-6 text-gray-700 text-sm space-y-1">
                    <li><span class="font-bold text-purple-700">Kamu</span> - {{ userXP }} XP
                        <span class="ml-2 bg-green-200 text-green-800 px-2 py-0.5 rounded-full text-xs">TOP 1</span>
                        <span class="ml-2 text-xs text-gray-500">({{ overallStats.totalCompleted }} materi selesai)</span>
                    </li>
                    <li>Bregas - 1100 XP</li>
                    <li>Yafa - 950 XP</li>
                </ol>
            </div>

            <!-- Debug Buttons (development only) -->
            <div v-if="!loading && purchasedCourses.length === 0 && !isPostPurchaseScenario" class="mt-6 text-center">
                <button
                    @click="manualRefreshCourses"
                    class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition mr-4"
                >
                    🔄 Refresh Manual
                </button>
                <button
                    @click="debugCourseData"
                    class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition"
                >
                    🔧 Debug Data
                </button>
            </div>
        </div>
    </section>
</template>

<script>
import axios from 'axios';

export default {
    name: 'UserDashboard',
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
            open: false,
            isScrolled: false,
            loading: true,
            loadingMessage: 'Memuat data courses...',

            // Course Data
            purchasedCourses: [],
            activeProgram: null,
            courseProgresses: {},
            courseStructures: {}, // NEW: Store course structures

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
                    type: 'Welcome',
                    badgeClass: 'inline-block bg-blue-100 text-blue-700 px-2 py-0.5 rounded mr-2',
                    message: '🎉 Selamat datang di Dunia Coding! Mulai perjalanan coding kamu sekarang.',
                    timestamp: new Date().toISOString()
                }
            ]
        };
    },

    computed: {
        userName() {
            return this.getUserName();
        },
        avatarUrl() {
            return this.assetBaseUrl + 'image/hajisodikin.jpg';
        },
        routes() {
            return this.laravelRoutes;
        },
        nextSessionDate() {
            if (!this.activeProgram) return '';

            const today = new Date();
            const nextMonday = new Date(today);
            const daysUntilMonday = (8 - today.getDay()) % 7 || 7;
            nextMonday.setDate(today.getDate() + daysUntilMonday);

            return nextMonday.toLocaleDateString('id-ID', {
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            }) + ' - 19:00 WIB';
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
        // NEW: Overall statistics
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
        // NEW: Count completed courses
        completedCoursesCount() {
            return Object.values(this.courseProgresses).filter(progress =>
                progress.total > 0 && progress.completed === progress.total
            ).length;
        }
    },

    async mounted() {
        console.log('🎯 Dashboard mounted - starting initialization...');
        this.setupAxios();
        this.setupProgressListeners(); // NEW: Setup listeners

        const urlParams = new URLSearchParams(window.location.search);
        const paymentSuccess = urlParams.get('payment_success');
        const courseTitle = urlParams.get('course_title');

        if (paymentSuccess === 'true' && courseTitle) {
            console.log('💰 Post-purchase scenario detected');
            this.loadingMessage = '🎉 Pembelian berhasil! Memuat course baru...';
            await this.handlePostPurchaseFlow(decodeURIComponent(courseTitle));
        } else {
            console.log('📊 Normal dashboard load');
            await this.loadNormalDashboard();
        }

        window.addEventListener('scroll', this.handleScroll);
    },

    beforeUnmount() {
        window.removeEventListener('scroll', this.handleScroll);
        // Clean up progress listeners
        window.removeEventListener('courseProgressUpdated', this.handleCourseProgressUpdate);
        window.removeEventListener('courseProgressSync', this.handleCourseProgressSync);
        window.removeEventListener('courseStructureLoaded', this.handleCourseStructureLoaded);
        window.removeEventListener('courseCompleted', this.handleCourseCompleted);

        if (this.redirectTimer) {
            clearInterval(this.redirectTimer);
        }
    },

    methods: {
        // ===== NEW: SETUP PROGRESS LISTENERS =====
        setupProgressListeners() {
            // Listen for progress updates from CourseContent component
            window.addEventListener('courseProgressUpdated', this.handleCourseProgressUpdate);
            window.addEventListener('courseProgressSync', this.handleCourseProgressSync);
            window.addEventListener('courseStructureLoaded', this.handleCourseStructureLoaded);
            window.addEventListener('courseCompleted', this.handleCourseCompleted);

            console.log('📡 Progress listeners setup complete');
        },

        // ===== NEW: HANDLE PROGRESS UPDATES =====
        handleCourseProgressUpdate(event) {
            const { courseId, progress } = event.detail;
            console.log('📊 Received progress update for course:', courseId, progress);

            if (progress && typeof progress === 'object') {
                this.$set(this.courseProgresses, courseId, {
                    completed: progress.completed_count || progress.completed || 0,
                    total: progress.total_materials || progress.total || 0,
                    percentage: progress.progress_percentage || progress.percentage || 0,
                    completedMateris: progress.completedMateris || [],
                    lastAccessed: progress.lastUpdated || new Date().toISOString(),
                    materialsSource: progress.materials_source || 'unknown'
                });

                // Update user stats based on new progress
                this.updateUserStats();

                // Add notification for progress milestone
                if (progress.completed_count && progress.total_materials) {
                    this.checkProgressMilestones(courseId, progress);
                }
            }
        },

        handleCourseProgressSync(event) {
            const { courseId, completed, total, percentage, completedMateris, courseTitle, lastAccessed } = event.detail;
            console.log('🔄 Syncing progress for course:', courseId);

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
            console.log('🏗️ Course structure loaded:', event.detail);

            this.courseStructures[courseId] = {
                title,
                totalMaterials,
                hasContent,
                materialsSource
            };

            // Update progress if we don't have it yet
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
            console.log('🎉 Course completed!', event.detail);

            // Add completion notification
            this.notifications.unshift({
                id: Date.now(),
                type: 'Achievement',
                badgeClass: 'inline-block bg-purple-100 text-purple-700 px-2 py-0.5 rounded mr-2',
                message: `🎓 Selamat! Kamu telah menyelesaikan course "${courseTitle}"!`,
                timestamp: completedAt
            });

            // Update stats
            this.updateUserStats();
        },

        // ===== NEW: CHECK PROGRESS MILESTONES =====
        checkProgressMilestones(courseId, progress) {
            const percentage = progress.progress_percentage || 0;
            const completed = progress.completed_count || 0;
            const total = progress.total_materials || 0;

            // Check for milestone achievements
            if (percentage === 25) {
                this.notifications.unshift({
                    id: Date.now(),
                    type: 'Milestone',
                    badgeClass: 'inline-block bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded mr-2',
                    message: `🌟 Milestone! Kamu sudah menyelesaikan 25% dari course ini!`,
                    timestamp: new Date().toISOString()
                });
            } else if (percentage === 50) {
                this.notifications.unshift({
                    id: Date.now(),
                    type: 'Milestone',
                    badgeClass: 'inline-block bg-blue-100 text-blue-700 px-2 py-0.5 rounded mr-2',
                    message: `🔥 Hebat! Kamu sudah di tengah perjalanan (50% selesai)!`,
                    timestamp: new Date().toISOString()
                });
            } else if (percentage === 75) {
                this.notifications.unshift({
                    id: Date.now(),
                    type: 'Milestone',
                    badgeClass: 'inline-block bg-orange-100 text-orange-700 px-2 py-0.5 rounded mr-2',
                    message: `🚀 Hampir selesai! Tinggal 25% lagi untuk menyelesaikan course!`,
                    timestamp: new Date().toISOString()
                });
            } else if (percentage === 100) {
                this.notifications.unshift({
                    id: Date.now(),
                    type: 'Completion',
                    badgeClass: 'inline-block bg-purple-100 text-purple-700 px-2 py-0.5 rounded mr-2',
                    message: `🎊 WOW! Kamu telah menyelesaikan course ini 100%! Time to celebrate!`,
                    timestamp: new Date().toISOString()
                });
            }
        },

        // ===== ENHANCED COURSE PROGRESS LOADING =====
        async loadAllCourseProgresses() {
            console.log('📊 Loading progress for all courses in parallel...');

            const progressPromises = this.purchasedCourses.map(async (course) => {
                const progress = await this.loadCourseProgress(course.id);
                return { courseId: course.id, progress };
            });

            try {
                const results = await Promise.all(progressPromises);

                results.forEach(({ courseId, progress }) => {
                    this.$set(this.courseProgresses, courseId, progress);
                });

                console.log('✅ All course progresses loaded:', this.courseProgresses);
            } catch (error) {
                console.error('❌ Error loading some progress data:', error);
            }
        },

        // ===== ENHANCED COURSE PROGRESS LOADING =====
        async loadCourseProgress(courseId) {
            try {
                console.log(`📊 Loading progress for course ${courseId}...`);

                const response = await axios.get(`https://itqom-platform-aa0ffce6a276.herokuapp.com/api/course-content/course/${courseId}`);

                if (response.data.success) {
                    const courseData = response.data.data;
                    const materis = courseData.materis || [];
                    const courseStats = courseData.course_stats || {};

                    // Store course structure
                    this.courseStructures[courseId] = {
                        title: courseData.courseDescription?.title,
                        totalMaterials: courseData.totalMateris,
                        hasContent: courseStats.has_content,
                        materialsSource: courseStats.materials_source
                    };

                    if (materis.length === 0) {
                        console.log(`⚠️ No materials found for course ${courseId}`);
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

                    console.log(`✅ Progress loaded for course ${courseId}:`, {
                        completed: `${completed}/${total}`,
                        percentage: `${Math.round(percentage)}%`,
                        materialsSource: courseStats.materials_source
                    });

                    return {
                        completed,
                        total,
                        percentage,
                        completedMateris,
                        materialsSource: courseStats.materials_source || 'course_content'
                    };
                } else {
                    console.error(`❌ Failed to load course content for ${courseId}:`, response.data);
                    return { completed: 0, total: 0, percentage: 0 };
                }

            } catch (error) {
                console.error(`💥 Error loading progress for course ${courseId}:`, error);
                return { completed: 0, total: 0, percentage: 0 };
            }
        },

        // ===== NEW: HELPER METHODS =====
        isCourseCompleted(courseId) {
            const progress = this.courseProgresses[courseId];
            return progress && progress.total > 0 && progress.completed === progress.total;
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

        getNextMaterial(courseId) {
            const progress = this.courseProgresses[courseId];
            if (!progress || !progress.completedMateris) return null;

            // This would need to be enhanced with actual material data
            // For now, return a placeholder
            if (progress.completed < progress.total) {
                return { judul: `Materi ${progress.completed + 1}` };
            }
            return null;
        },

        // ===== SETUP AXIOS =====
        setupAxios() {
            if (!axios.defaults.baseURL) {
                axios.defaults.baseURL = 'https://itqom-platform-aa0ffce6a276.herokuapp.com';
            }

            axios.defaults.headers.common['Accept'] = 'application/json';
            axios.defaults.headers.common['Content-Type'] = 'application/json';

            const token = localStorage.getItem('authToken');
            if (token) {
                axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
                console.log('✅ Auth token set globally');
            } else {
                console.warn('⚠️ No auth token found, API calls may fail');
            }

            console.log('🔧 Axios configured with base URL:', axios.defaults.baseURL);
        },

        getUserName() {
            try {
                const userData = localStorage.getItem('user');
                if (userData) {
                    const user = JSON.parse(userData);
                    return user.name || 'Pengguna';
                }
            } catch (e) {
                console.error('Error getting user name:', e);
            }
            return 'Pengguna';
        },

        // ===== POST PURCHASE FLOW =====
        async handlePostPurchaseFlow(courseTitle) {
            console.log('🚀 Handling post-purchase flow for:', courseTitle);

            try {
                await this.loadPurchasedCoursesWithRetry(true);

                if (this.purchasedCourses.length > 0) {
                    const purchasedCourse = this.findPurchasedCourse(courseTitle);

                    if (purchasedCourse) {
                        console.log('✅ Found purchased course:', purchasedCourse.title);
                        this.activeProgram = purchasedCourse;
                        this.saveActiveProgram();
                        this.showSuccessNotificationWithRedirect(courseTitle, purchasedCourse);
                        this.loadAllCourseProgresses();
                        this.updateUserStats();
                    } else {
                        console.log('⚠️ Could not find matching course');
                        this.showSuccessNotification(courseTitle);
                        this.cleanUrlParameters();
                    }
                } else {
                    console.log('❌ No courses found after purchase - showing retry message');
                    this.loadingMessage = 'Hmm, course belum muncul. Mencoba lagi...';
                }

            } catch (error) {
                console.error('❌ Error in post-purchase flow:', error);
                this.loading = false;
                this.showErrorNotification('Terjadi kesalahan saat memuat course. Silakan refresh halaman.');
            }
        },

        async loadNormalDashboard() {
            try {
                await this.loadPurchasedCoursesWithRetry(false);
                this.loadActiveProgram();
                this.updateUserStats();
            } catch (error) {
                console.error('❌ Error loading normal dashboard:', error);
            } finally {
                this.loading = false;
            }
        },

        findPurchasedCourse(courseTitle) {
            return this.purchasedCourses.find(course =>
                course.title.toLowerCase().includes(courseTitle.toLowerCase()) ||
                courseTitle.toLowerCase().includes(course.title.toLowerCase())
            );
        },

        showSuccessNotificationWithRedirect(courseTitle, course) {
            console.log('🎉 Showing success notification with redirect');

            this.showPurchaseNotification = true;
            this.latestPurchasedCourse = courseTitle;
            this.redirectCountdown = 3;

            this.notifications.unshift({
                id: Date.now(),
                type: 'Success',
                badgeClass: 'inline-block bg-green-100 text-green-700 px-2 py-0.5 rounded mr-2',
                message: `🎉 Pembelian berhasil! "${courseTitle}" siap dipelajari. Redirecting ke course...`,
                timestamp: new Date().toISOString()
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

        redirectToCourse(course) {
            console.log('🚀 Redirecting to course:', course.title);
            window.location.href = `/course/${course.id}`;
        },

        async loadPurchasedCoursesWithRetry(isPostPurchase = false) {
            console.log('🔄 Starting course loading with retry...');

            const maxRetries = isPostPurchase ? 5 : 1;
            const retryDelay = isPostPurchase ? 2000 : 1000;

            for (let attempt = 1; attempt <= maxRetries; attempt++) {
                console.log(`🔄 Attempt ${attempt}/${maxRetries} to load courses...`);

                try {
                    await this.loadPurchasedCourses();

                    if (this.purchasedCourses.length > 0) {
                        console.log('✅ Courses loaded successfully!');
                        return;
                    }

                    if (isPostPurchase && attempt < maxRetries) {
                        console.log(`⏳ No courses found, waiting before retry ${attempt + 1}...`);
                        this.loadingMessage = `Mencoba memuat course... (${attempt}/${maxRetries})`;
                        await this.delay(retryDelay);
                        continue;
                    }

                    console.log('📝 No courses found - final result');
                    break;

                } catch (error) {
                    console.error(`❌ Attempt ${attempt} failed:`, error);

                    if (attempt === maxRetries) {
                        throw error;
                    }

                    if (isPostPurchase) {
                        this.loadingMessage = `Error loading, retrying... (${attempt}/${maxRetries})`;
                        await this.delay(retryDelay);
                    }
                }
            }
        },

        async loadPurchasedCourses() {
            try {
                console.log('🔍 Loading purchased courses...');

                const token = localStorage.getItem('authToken');
                if (!token) {
                    console.log('❌ No token found');
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

                console.log('📡 API Response received:', response.status);

                if (response.data.success) {
                    this.purchasedCourses = response.data.courses || [];
                    console.log('✅ Courses loaded:', this.purchasedCourses.length);

                    if (this.purchasedCourses.length > 0) {
                        this.updatePurchasedStateNotifications();
                        // Load progress for all courses
                        await this.loadAllCourseProgresses();
                        this.$nextTick(() => {
                            this.$forceUpdate();
                        });
                    } else {
                        this.activeProgram = null;
                        this.updateEmptyStateNotifications();
                    }

                } else {
                    console.error('❌ API returned success: false', response.data);
                    this.purchasedCourses = [];
                    this.updateEmptyStateNotifications();
                }

            } catch (error) {
                console.error('💥 Error loading purchased courses:', error);

                if (error.response?.status === 401) {
                    console.log('🔑 Token expired, clearing auth data');
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

        async startLearning(course) {
            console.log('🚀 Starting learning for course:', course.title);

            this.activeProgram = course;
            this.activeProgram.last_accessed = new Date().toISOString();
            this.saveActiveProgram();

            this.notifications.unshift({
                id: Date.now(),
                type: 'Learning',
                badgeClass: 'inline-block bg-blue-100 text-blue-700 px-2 py-0.5 rounded mr-2',
                message: `🚀 Mulai belajar "${course.title}". Semangat belajarnya!`,
                timestamp: new Date().toISOString()
            });

            this.updateUserStats();
            window.location.href = `/course/${course.id}`;
        },

        saveActiveProgram() {
            if (this.activeProgram) {
                try {
                    localStorage.setItem('active_program', JSON.stringify(this.activeProgram));
                    console.log('💾 Active program saved to localStorage');
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
                        console.log('✅ Active program loaded from localStorage:', this.activeProgram.title);
                    } else {
                        console.log('⚠️ Saved active program not found in purchased courses');
                        localStorage.removeItem('active_program');
                    }
                }

                if (!this.activeProgram && this.purchasedCourses.length > 0) {
                    this.activeProgram = this.purchasedCourses[0];
                    this.saveActiveProgram();
                    console.log('🎯 Set most recent course as active program:', this.activeProgram.title);
                }

            } catch (error) {
                console.error('Error loading active program:', error);
                localStorage.removeItem('active_program');
            }
        },

        showSuccessNotification(courseTitle) {
            console.log('🎉 Showing success notification for:', courseTitle);

            this.showPurchaseNotification = true;
            this.latestPurchasedCourse = courseTitle;

            this.notifications.unshift({
                id: Date.now(),
                type: 'Success',
                badgeClass: 'inline-block bg-green-100 text-green-700 px-2 py-0.5 rounded mr-2',
                message: `🎉 Selamat! Kamu berhasil membeli "${courseTitle}". Selamat belajar!`,
                timestamp: new Date().toISOString()
            });

            this.updateUserStats();
        },

        showErrorNotification(message) {
            this.notifications.unshift({
                id: Date.now(),
                type: 'Error',
                badgeClass: 'inline-block bg-red-100 text-red-700 px-2 py-0.5 rounded mr-2',
                message: `❌ ${message}`,
                timestamp: new Date().toISOString()
            });
        },

        dismissNotification() {
            this.showPurchaseNotification = false;

            if (this.redirectTimer) {
                clearInterval(this.redirectTimer);
                this.redirectTimer = null;
            }

            this.cleanUrlParameters();
        },

        cleanUrlParameters() {
            const url = new URL(window.location);
            url.searchParams.delete('payment_success');
            url.searchParams.delete('course_title');
            window.history.replaceState({}, document.title, url.pathname);
            console.log('🧹 URL parameters cleaned');
        },

        updateEmptyStateNotifications() {
            this.notifications = [
                {
                    id: 1,
                    type: 'Welcome',
                    badgeClass: 'inline-block bg-blue-100 text-blue-700 px-2 py-0.5 rounded mr-2',
                    message: '🎉 Selamat datang di Dunia Coding! Mulai perjalanan coding kamu sekarang.',
                    timestamp: new Date().toISOString()
                },
                {
                    id: 2,
                    type: 'Info',
                    badgeClass: 'inline-block bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded mr-2',
                    message: '📚 Ada course baru tersedia! Cek sekarang dan dapatkan early bird discount.',
                    timestamp: new Date().toISOString()
                },
                {
                    id: 3,
                    type: 'Promo',
                    badgeClass: 'inline-block bg-green-100 text-green-700 px-2 py-0.5 rounded mr-2',
                    message: '🤑 Promo spesial: diskon 20% untuk course pertama kamu!',
                    timestamp: new Date().toISOString()
                }
            ];
        },

        updatePurchasedStateNotifications() {
            this.notifications = [
                {
                    id: 1,
                    type: 'Baru',
                    badgeClass: 'inline-block bg-green-100 text-green-700 px-2 py-0.5 rounded mr-2',
                    message: '🎉 Congrats! Kamu sudah memulai perjalanan coding. #NoMoreSkip',
                    timestamp: new Date().toISOString()
                },
                {
                    id: 2,
                    type: 'Info',
                    badgeClass: 'inline-block bg-blue-100 text-blue-700 px-2 py-0.5 rounded mr-2',
                    message: '📅 Jangan lupa ikuti sesi mentoring untuk progress yang lebih optimal!',
                    timestamp: new Date().toISOString()
                },
                {
                    id: 3,
                    type: 'Tips',
                    badgeClass: 'inline-block bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded mr-2',
                    message: '💡 Konsisten belajar 30 menit setiap hari lebih efektif daripada belajar 3 jam seminggu sekali.',
                    timestamp: new Date().toISOString()
                }
            ];
        },

        // ===== ENHANCED USER STATS =====
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

            console.log('📊 Enhanced stats updated:', {
                courses: this.purchasedCourses.length,
                completedCourses: this.completedCoursesCount,
                totalCompleted: this.overallStats.totalCompleted,
                totalMaterials: this.overallStats.totalMaterials,
                userXP: this.userXP,
                userLevel: this.userLevel,
                levelProgress: this.levelProgress
            });
        },

        delay(ms) {
            return new Promise(resolve => setTimeout(resolve, ms));
        },

        getCourseImageUrl(imagePath) {
            if (!imagePath) return '/images/default-course.jpg';
            if (imagePath.startsWith('/storage/') || imagePath.startsWith('http')) {
                return imagePath;
            }
            return `/storage/${imagePath}`;
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

        handleScroll() {
            this.isScrolled = window.pageYOffset > 100;
            if (this.isScrolled) {
                this.open = false;
            }
        },

        async manualRefreshCourses() {
            console.log('🔄 Manual refresh triggered');
            this.loading = true;
            this.loadingMessage = 'Memuat ulang data courses...';
            await this.loadPurchasedCoursesWithRetry();
            this.loading = false;
        },

        logCurrentState() {
            console.log('🔍 Current Dashboard State:');
            console.log('- loading:', this.loading);
            console.log('- purchasedCourses:', this.purchasedCourses);
            console.log('- purchasedCourses.length:', this.purchasedCourses.length);
            console.log('- activeProgram:', this.activeProgram);
            console.log('- courseProgresses:', this.courseProgresses);
            console.log('- overallStats:', this.overallStats);
            console.log('- isPostPurchaseScenario:', this.isPostPurchaseScenario);
        },

        async debugCourseData() {
            console.log('🔧 Manual Debug Started...');
            this.logCurrentState();

            try {
                const token = localStorage.getItem('authToken');
                if (!token) {
                    console.log('❌ No token found');
                    return;
                }

                console.log('1️⃣ Checking user info...');
                const userResponse = await axios.get('/api/user', {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                console.log('👤 User data:', userResponse.data);

                console.log('2️⃣ Checking raw payments...');
                const paymentsResponse = await axios.get('/api/debug/raw-payments', {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                console.log('💳 Payments data:', paymentsResponse.data);

                console.log('3️⃣ Checking my courses...');
                const coursesResponse = await axios.get('/api/my-courses', {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                console.log('📚 Courses result:', coursesResponse.data);

                return {
                    user: userResponse.data,
                    payments: paymentsResponse.data,
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

        // NEW: Watch for overall progress changes
        'overallStats.totalCompleted': {
            handler(newCompleted, oldCompleted) {
                if (newCompleted > oldCompleted) {
                    console.log('📈 Overall progress increased:', {
                        from: oldCompleted,
                        to: newCompleted,
                        total: this.overallStats.totalMaterials
                    });

                    // Update user stats when progress changes
                    this.updateUserStats();
                }
            }
        }
    }
};
</script>

<style scoped>
.animate-float {
    animation: float 6s ease-in-out infinite;
}

@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-12px);
    }
}

.animate-bounce {
    animation: bounce 1s infinite;
}

@keyframes bounce {
    0%, 20%, 53%, 80%, 100% {
        transform: translateY(0);
    }
    40%, 43% {
        transform: translateY(-30px);
    }
    70% {
        transform: translateY(-15px);
    }
    90% {
        transform: translateY(-4px);
    }
}

.transition {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}

.opacity-0 {
    opacity: 0;
}

.opacity-100 {
    opacity: 1;
}

.pointer-events-none {
    pointer-events: none;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

.ring-2 {
    box-shadow: 0 0 0 2px rgba(147, 51, 234, 0.4);
}

.transition-all {
    transition: all 0.5s ease-in-out;
}

/* Enhanced progress bar animations */
.progress-bar {
    transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Milestone celebration effect */
@keyframes milestone-glow {
    0% { box-shadow: 0 0 5px rgba(147, 51, 234, 0.3); }
    50% { box-shadow: 0 0 20px rgba(147, 51, 234, 0.6); }
    100% { box-shadow: 0 0 5px rgba(147, 51, 234, 0.3); }
}

.milestone-achieved {
    animation: milestone-glow 2s ease-in-out;
}
</style>
