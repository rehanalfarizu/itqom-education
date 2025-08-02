<template>
  <div>
    <!-- Hero Section -->
    <section id="home" class="relative bg-gradient-to-br from-[#564AB1] via-[#6B5CE7] to-[#8B7CF6] text-white pt-26 pb-20 px-4 sm:px-6 overflow-hidden">
      <!-- Background Pattern -->
      <div class="absolute inset-0 opacity-10">
        <div class="absolute top-10 left-10 w-32 h-32 bg-white rounded-full blur-xl"></div>
        <div class="absolute bottom-20 right-20 w-24 h-24 bg-white rounded-full blur-xl"></div>
        <div class="absolute top-1/2 left-1/3 w-16 h-16 bg-white rounded-full blur-xl"></div>
      </div>

      <div class="container mx-auto flex flex-col lg:flex-row items-center justify-between relative z-10">
        <!-- Hero Text -->
        <div class="lg:w-1/2 mb-8 lg:mb-0 text-center lg:text-left">
          <div class="inline-block bg-white/20 backdrop-blur-sm px-3 py-2 sm:px-4 sm:py-2 rounded-full text-xs sm:text-sm font-medium mb-4 sm:mb-6">
            🚀 Platform Pembelajaran Terdepan
          </div>
          <h1 class="text-3xl sm:text-4xl lg:text-5xl xl:text-6xl font-bold mb-4 sm:mb-6 leading-tight">
            Buka
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 to-orange-400">
              Potensimu!
            </span>
          </h1>
          <p class="mb-6 sm:mb-8 text-base sm:text-lg lg:text-xl text-gray-200 leading-relaxed max-w-lg mx-auto lg:mx-0">
            Telusuri kursus menarik dan interaktif bersama kami. Bergabunglah dengan ribuan siswa yang telah mengembangkan skill mereka.
          </p>
          <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center lg:justify-start w-full max-w-md mx-auto lg:mx-0" v-if="!isLoggedIn">
            <router-link to="/register" class="bg-white text-[#564AB1] px-6 py-3 sm:px-8 sm:py-4 rounded-xl sm:rounded-2xl font-semibold hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 shadow-lg text-center text-sm sm:text-base">
              Get Started
            </router-link>
            <router-link to="/learnmore" class="border-2 border-white px-6 py-3 sm:px-8 sm:py-4 rounded-xl sm:rounded-2xl font-semibold hover:bg-white hover:text-[#564AB1] transition-all duration-300 transform hover:scale-105 backdrop-blur-sm text-center text-sm sm:text-base">
              Learn More
            </router-link>
          </div>
          <div v-else class="flex justify-center lg:justify-start">
            <button @click="scrollToCourses" class="bg-white text-[#564AB1] px-6 py-3 sm:px-8 sm:py-4 rounded-xl sm:rounded-2xl font-semibold hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 shadow-lg text-sm sm:text-base">
              Jelajahi Kursus
            </button>
          </div>
        </div>

        <!-- Hero Image Slider -->
        <div class="lg:w-1/2 flex justify-center w-full">
          <div class="swiper mySwiper w-full max-w-sm sm:max-w-md lg:max-w-lg relative">
            <div class="swiper-wrapper">
              <!-- Slide 1 -->
              <div class="swiper-slide">
                <div class="relative group">
                  <img src="/image/buildwebsite.png" alt="Build Website" class="rounded-2xl sm:rounded-3xl w-full h-[250px] sm:h-[300px] object-cover shadow-2xl transform group-hover:scale-105 transition-transform duration-500">
                  <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent rounded-2xl sm:rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </div>
              </div>
              <!-- Slide 2 -->
              <div class="swiper-slide">
                <div class="relative group">
                  <img src="/image/creative.png" alt="Creative Design" class="rounded-2xl sm:rounded-3xl w-full h-[250px] sm:h-[300px] object-cover shadow-2xl transform group-hover:scale-105 transition-transform duration-500">
                  <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent rounded-2xl sm:rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </div>
              </div>
              <!-- Slide 3 -->
              <div class="swiper-slide">
                <div class="relative group">
                  <img src="/image/learning-illustration.png" alt="Learning" class="rounded-2xl sm:rounded-3xl w-full h-[250px] sm:h-[300px] object-cover shadow-2xl transform group-hover:scale-105 transition-transform duration-500">
                  <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent rounded-2xl sm:rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </div>
              </div>
            </div>
            <!-- Custom Navigation -->
            <div class="swiper-pagination !bottom-2 sm:!bottom-4"></div>
            <div class="swiper-button-next !text-white !right-2 sm:!right-4 !top-1/2 !w-10 !h-10 sm:!w-12 sm:!h-12 bg-white/20 !rounded-full backdrop-blur-sm hover:bg-white/30 transition-all duration-300"></div>
            <div class="swiper-button-prev !text-white !left-2 sm:!left-4 !top-1/2 !w-10 !h-10 sm:!w-12 sm:!h-12 bg-white/20 !rounded-full backdrop-blur-sm hover:bg-white/30 transition-all duration-300"></div>
          </div>
        </div>
      </div>
    </section>

    <!-- Course Section -->
    <section id="courses" ref="coursesSection" class="py-16 sm:py-20 px-4 sm:px-6 bg-white">
      <div class="container mx-auto">
        <!-- Section Header -->
        <div class="text-center mb-12 sm:mb-16">
          <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
            Jelajahi
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#564AB1] to-[#8B7CF6]">
              Kursus Kami
            </span>
          </h2>
          <p class="text-gray-600 text-base sm:text-lg max-w-2xl mx-auto px-4">
            Pilih dari berbagai kategori kursus yang dirancang untuk mengembangkan skill profesional Anda
          </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 sm:gap-8">
          <!-- Enhanced Category Sidebar - Removed logo and reduced spacing -->
          <div class="lg:col-span-1 mb-6 lg:mb-0">
            <div class="bg-gradient-to-br from-white to-gray-50/50 rounded-3xl p-4 shadow-xl border border-gray-100/50 backdrop-blur-sm lg:sticky lg:top-6">
              <!-- Simplified Header -->
              <h3 class="text-xl font-bold text-gray-900 mb-4 text-center bg-gradient-to-r from-[#564AB1] to-[#8B7CF6] bg-clip-text text-transparent">
                Kategori
              </h3>

              <!-- Category Pills -->
              <div class="space-y-2">
                <button
                  v-for="category in categories"
                  :key="category.id"
                  @click="selectCategory(category.name)"
                  :class="[
                    'w-full p-3 rounded-2xl font-semibold text-sm transition-all duration-300 transform hover:scale-105 flex items-center group shadow-sm border',
                    selectedCategory === category.name
                      ? 'bg-gradient-to-r from-[#564AB1] to-[#8B7CF6] text-white shadow-lg shadow-purple-200/50 border-transparent'
                      : 'bg-white hover:bg-gray-50 text-gray-700 hover:text-[#564AB1] border-gray-200 hover:border-[#564AB1]/30 hover:shadow-md'
                  ]"
                >
                  <div class="flex items-center">
                    <div :class="[
                      'w-2 h-2 rounded-full mr-3 transition-all duration-300',
                      selectedCategory === category.name ? 'bg-white' : 'bg-[#564AB1]'
                    ]"></div>
                    <span>{{ category.name }}</span>
                  </div>
                </button>
              </div>
            </div>
          </div>

          <!-- Course Cards -->
          <div class="lg:col-span-3">
            <!-- Loading Animation -->
            <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6">
              <div v-for="i in 6" :key="i" class="bg-white rounded-xl shadow-lg overflow-hidden animate-pulse border-2 border-gray-200">
                <div class="h-40 bg-gray-200"></div>
                <div class="p-4 border-t border-gray-100">
                  <div class="h-4 bg-gray-200 rounded mb-3"></div>
                  <div class="h-3 bg-gray-200 rounded mb-2"></div>
                  <div class="h-3 bg-gray-200 rounded w-3/4"></div>
                </div>
              </div>
            </div>

            <!-- Course Grid -->
            <div v-else class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6">
              <router-link
                v-for="(course, index) in filteredCourses"
                :key="course.id"
                :to="`/Course_Description/${course.id}`"
                class="group block bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden border-2 border-gray-200 hover:border-[#564AB1]/40 transform hover:scale-[1.02]"
                :style="{ animationDelay: `${index * 50}ms` }"
              >
                <!-- Course Image -->
                <div class="relative h-40 overflow-hidden">
                  <OptimizedImage
                    :src="course.image"
                    :alt="course.title"
                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                  />

                  <!-- Category Badge -->
                  <div class="absolute top-3 left-3">
                    <span class="bg-white/90 backdrop-blur-sm px-3 py-1.5 rounded-full text-xs font-semibold text-gray-800 shadow-sm">
                      {{ course.category }}
                    </span>
                  </div>

                  <!-- Gradient overlay on hover -->
                  <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </div>

                <!-- Course Info -->
                <div class="p-4 bg-white border-t border-gray-100">
                  <!-- Course Title -->
                  <h3 class="font-bold text-gray-900 mb-3 text-base leading-tight line-clamp-2">
                    {{ course.title }}
                  </h3>

                  <!-- Instructor -->
                  <div class="flex items-center mb-3 text-gray-600">
                    <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-sm font-medium">{{ course.instructor }}</span>
                  </div>

                  <!-- Course Stats -->
                  <div class="flex items-center gap-4 mb-3 text-gray-600">
                    <!-- Video Count -->
                    <div class="flex items-center">
                      <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                      </svg>
                      <span class="text-sm">{{ course.video_count || '23' }} Materi</span>
                    </div>

                    <!-- Duration -->
                    <div class="flex items-center">
                      <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                      </svg>
                      <span class="text-sm">{{ course.duration }}</span>
                    </div>
                  </div>

                  <!-- Price Section -->
                  <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                      <!-- Discount Badge -->
                      <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">50% OFF</span>
                    </div>
                  </div>

                  <!-- Price -->
                  <div class="mt-2">
                    <div class="flex items-baseline space-x-2">
                      <span class="text-sm text-gray-500 line-through">Rp. {{ course.original }}</span>
                    </div>
                    <div class="text-xl font-bold text-blue-600">Rp. {{ course.price }}</div>
                  </div>
                </div>
              </router-link>
            </div>

            <!-- Enhanced Empty State -->
            <div v-if="!loading && filteredCourses.length === 0" class="text-center py-16">
              <div class="relative">
                <!-- Animated Background Elements -->
                <div class="absolute inset-0 flex items-center justify-center">
                  <div class="w-32 h-32 bg-gradient-to-r from-[#564AB1]/10 to-[#8B7CF6]/10 rounded-full animate-pulse"></div>
                </div>

                <!-- Main Icon -->
                <div class="relative bg-gradient-to-r from-[#564AB1] to-[#8B7CF6] w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                  <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                  </svg>
                </div>

                <h3 class="text-2xl font-bold text-gray-900 mb-3">Tidak Ada Kursus Ditemukan</h3>
                <p class="text-gray-600 mb-6 max-w-md mx-auto">
                  Maaf, tidak ada kursus yang cocok dengan filter yang Anda pilih. Coba pilih kategori lain atau hubungi kami untuk saran kursus.
                </p>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-3 justify-center max-w-sm mx-auto">
                  <button
                    @click="selectCategory('Semua Kategori')"
                    class="bg-gradient-to-r from-[#564AB1] to-[#8B7CF6] text-white px-6 py-3 rounded-xl font-semibold hover:shadow-lg transition-all duration-300 transform hover:scale-105"
                  >
                    Lihat Semua Kursus
                  </button>
                  <button class="border-2 border-[#564AB1] text-[#564AB1] px-6 py-3 rounded-xl font-semibold hover:bg-[#564AB1] hover:text-white transition-all duration-300">
                    Hubungi Kami
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import axios from 'axios';
import Swiper from 'swiper';
import 'swiper/swiper-bundle.css';
import OptimizedImage from '../OptimizedImage.vue';

export default {
  name: 'Course',
  components: {
    OptimizedImage
  },
  data() {
    return {
      selectedCategory: '', // Changed to single category
      courses: [],
      categories: [
        { id: 0, name: 'Semua Kategori' },
        { id: 1, name: 'Programming' },
        { id: 2, name: 'Design' },
        { id: 3, name: 'Marketing' },
        { id: 4, name: 'Business' },
        { id: 5, name: 'Photography' },
        { id: 6, name: 'Music' }
      ],
      isLoggedIn: false,
      userName: '',
      avatarUrl: '',
      swiper: null,
      loading: true
    };
  },
  computed: {
    filteredCourses() {
      if (!this.selectedCategory || this.selectedCategory === 'Semua Kategori') {
        return this.courses;
      }
      return this.courses.filter(course => course.category === this.selectedCategory);
    }
  },
  methods: {
    async fetchCourses() {
      try {
        this.loading = true;
        const response = await axios.get('https://itqom-platform-aa0ffce6a276.herokuapp.com/api/courses');

        // Check if response has success property (new API format)
        if (response.data.success) {
          this.courses = response.data.data;
        } else {
          // Fallback for old API format
          this.courses = response.data;
        }

        // Courses loaded successfully
        // console.log('Courses loaded:', this.courses.length);
      } catch (error) {
        console.error('Gagal mengambil data kursus:', error);
        this.courses = [];
      } finally {
        this.loading = false;
      }
    },

    async fetchCoursesByCategory(category) {
      if (!category || category === 'Semua Kategori') {
        return this.fetchCourses();
      }

      try {
        this.loading = true;
        const response = await axios.get(`https://itqom-platform-aa0ffce6a276.herokuapp.com/api/courses/category/${encodeURIComponent(category)}`);

        if (response.data.success) {
          this.courses = response.data.data;
        } else {
          this.courses = [];
        }
      } catch (error) {
        console.error('Gagal mengambil data kursus berdasarkan kategori:', error);
        this.courses = [];
      } finally {
        this.loading = false;
      }
    },

    async fetchPopularCourses() {
      try {
        const response = await axios.get('https://itqom-platform-aa0ffce6a276.herokuapp.com/api/courses/popular');

        if (response.data.success) {
          // Popular courses loaded successfully
          // console.log('Popular courses loaded:', response.data.data.length);
          return response.data.data;
        }
        return [];
      } catch (error) {
        console.error('Gagal mengambil kursus populer:', error);
        return [];
      }
    },
    selectCategory(categoryName) {
      this.selectedCategory = categoryName;
      // Fetch courses based on selected category
      this.fetchCoursesByCategory(categoryName);
    },
    checkLoginStatus() {
      const authToken = localStorage.getItem('authToken');
      const userData = localStorage.getItem('user');

      if (authToken && userData) {
        try {
          const user = JSON.parse(userData);
          this.isLoggedIn = true;
          this.userName = user.name;
          if (user.avatar) {
            this.avatarUrl = `/storage/${user.avatar}`;
          }
        } catch (e) {
          console.error("Gagal mengurai data pengguna:", e);
          this.isLoggedIn = false;
          this.userName = '';
        }
      } else {
        this.isLoggedIn = false;
        this.userName = '';
      }
    },
    scrollToCourses() {
      const element = this.$refs.coursesSection;
      if (element) {
        element.scrollIntoView({ behavior: 'smooth' });
      }
    },

    // Handle image loading error
    handleImageError(event) {
      // Fallback to default image if image fails to load
      event.target.src = '/images/default-course.jpg';
    },
    initSwiper() {
      this.swiper = new Swiper('.mySwiper', {
        loop: true,
        autoplay: {
          delay: 4000,
          disableOnInteraction: false,
        },
        pagination: {
          el: '.swiper-pagination',
          clickable: true,
          dynamicBullets: true,
        },
        navigation: {
          nextEl: '.swiper-button-next',
          prevEl: '.swiper-button-prev',
        },
        effect: 'fade',
        fadeEffect: {
          crossFade: true,
        },
        speed: 800,
      });
    }
  },
  mounted() {
    this.fetchCourses();
    this.checkLoginStatus();

    window.addEventListener('localStorageUpdated', this.checkLoginStatus);

    this.$nextTick(() => {
      setTimeout(() => {
        this.initSwiper();
      }, 100);
    });
  },
  beforeUnmount() {
    window.removeEventListener('localStorageUpdated', this.checkLoginStatus);

    if (this.swiper) {
      this.swiper.destroy();
      this.swiper = null;
    }
  }
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Custom Swiper Styles */
.swiper-pagination-bullet {
  background: rgba(255, 255, 255, 0.5) !important;
  opacity: 1 !important;
}

.swiper-pagination-bullet-active {
  background: white !important;
  transform: scale(1.2);
}

.swiper-button-next:after,
.swiper-button-prev:after {
  font-size: 14px !important;
  font-weight: bold;
}

@media (min-width: 640px) {
  .swiper-button-next:after,
  .swiper-button-prev:after {
    font-size: 16px !important;
  }
}

/* Smooth scroll behavior */
html {
  scroll-behavior: smooth;
}

/* Animation for course cards */
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

.grid > * {
  animation: fadeInUp 0.6s ease-out forwards;
}

/* Pulse animation for loading */
@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
