<template>
  <div class="homepage-container">
    <section class="homepage" id="home" data-aos="fade-up">
      <div class="homepage-text">
        <h1>Develop your skills in a new and unique way</h1>
        <p>Discover the best coding course for your kids. Learn Coding from basic</p>
      </div>
      <div class="homepage-img md:w-1/2 flex justify-center">
        <img class="homepage-img" src="/image/homepage-img.png" alt="HomePage">
      </div>
    </section>

    <div class="partners-section text-center mt-8">
      <p class="text-gray-700 text-lg font-medium mb-6">Dunia Coding telah bekerja sama dengan</p>
      <div class="flex flex-wrap justify-center items-center gap-6 mb-4">
        <img src="/image/maze.png" alt="Maze" class="h-8 md:h-10 max-w-[100px] object-contain">
        <img src="/image/dropbox.png" alt="Dropbox" class="h-8 md:h-10 max-w-[100px] object-contain">
        <img src="/image/webflow.png" alt="Webflow" class="h-8 md:h-10 max-w-[100px] object-contain">
      </div>
      <p class="text-gray-700 text-base">Dan 100+ Tech Company Lainnya</p>
    </div>

    <section class="masking bg-white min-h-screen flex items-center justify-center" id="masking" data-aos="fade-up">
      <div class="container mx-auto px-4 flex flex-col-reverse lg:flex-row items-center justify-between gap-12">
        <div class="w-full lg:w-1/2 text-center lg:text-left">
          <h1 class="text-3xl md:text-5xl font-bold mb-6">Exclusive Mentoring</h1>
          <p class="text-gray-700 text-base md:text-lg leading-relaxed">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et
            dolore magna aliqua.
            Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
            Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
            Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est
            laborum.
          </p>
        </div>
        <div class="w-full lg:w-1/2 flex justify-center">
          <img src="/image/masking.png" alt="Masking" class="max-w-full h-auto lg:max-w-[80%]">
        </div>
      </div>
    </section>

    <section class="event-section overflow-hidden" data-aos="fade-up">
      <div class="container mx-auto px-4 py-12">
        <div class="flex flex-col lg:flex-row items-center gap-12">

          <div class="w-full lg:w-1/2 left-content">
            <button class="badge bg-purple-100 text-purple-700 px-4 py-2 rounded-full text-sm font-semibold mb-4">
              Our Product
            </button>
            <h2 class="title text-4xl font-bold mb-6">
              Online <span class="highlight text-purple-600">Course</span>
            </h2>
            <p class="description text-gray-600 text-lg mb-8 leading-relaxed">
              Dapatkan fleksibilitas penuh dengan video tutorial dan e-book yang bisa diakses kapanpun, lengkap dan selalu
              relevan dengan industri.
            </p>

            <div class="features grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
              <ul class="space-y-3">
                <li class="flex items-center text-gray-700">
                  <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                  Flexible Access
                </li>
                <li class="flex items-center text-gray-700">
                  <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                  E-Book
                </li>
                <li class="flex items-center text-gray-700">
                  <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                  File Project
                </li>
              </ul>
              <ul class="space-y-3">
                <li class="flex items-center text-gray-700">
                  <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                  E-Certificate
                </li>
                <li class="flex items-center text-gray-700">
                  <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                  Exclusive Community
                </li>
                <li class="flex items-center text-gray-700">
                  <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                  Free Consultation
                </li>
              </ul>
            </div>

            <button
              class="explore-btn bg-purple-600 hover:bg-purple-700 text-white px-8 py-4 rounded-lg font-semibold transition-colors duration-300"
              @click="navigateToCourse"
            >
              Explore All Course
            </button>
          </div>

          <div class="lg:w-1/2 flex justify-center w-full">
            <div class="swiper-container relative w-full max-w-sm sm:max-w-md lg:max-w-lg">
              <div class="swiper mySwiper w-full">
                <div class="swiper-wrapper">
                  <div class="swiper-slide">
                    <div class="relative group">
                      <img src="/image/buildwebsite.png" alt="Build Website" class="slide-image rounded-2xl sm:rounded-3xl w-full h-[250px] sm:h-[300px] object-cover shadow-2xl transform group-hover:scale-105 transition-transform duration-500">
                      <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent rounded-2xl sm:rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                  </div>
                  <div class="swiper-slide">
                    <div class="relative group">
                      <img src="/image/creative.png" alt="Creative Design" class="slide-image rounded-2xl sm:rounded-3xl w-full h-[250px] sm:h-[300px] object-cover shadow-2xl transform group-hover:scale-105 transition-transform duration-500">
                      <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent rounded-2xl sm:rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                  </div>
                  <div class="swiper-slide">
                    <div class="relative group">
                      <img src="/image/learning-illustration.png" alt="Learning" class="slide-image rounded-2xl sm:rounded-3xl w-full h-[250px] sm:h-[300px] object-cover shadow-2xl transform group-hover:scale-105 transition-transform duration-500">
                      <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent rounded-2xl sm:rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                  </div>
                </div>

                <div class="swiper-button-next custom-nav-button"></div>
                <div class="swiper-button-prev custom-nav-button"></div>

                <div class="swiper-pagination"></div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </section>

    <section class="best-course-section p-8 " data-aos="fade-up">
      <div class="container mx-auto">
        <div class="flex justify-between items-center mb-8">
          <h2 class="text-3xl font-bold text-gray-800">Best Course Choices</h2>
          <a href="/course" class="text-purple-600 font-semibold hover:underline text-lg">Lihat Selengkapnya</a>
        </div>

        <div v-if="loading" class="text-center py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600 mx-auto"></div>
          <p class="text-gray-500 mt-4">Loading courses...</p>
        </div>

        <div v-else-if="error" class="text-center py-12">
          <p class="text-red-500 text-lg mb-4">{{ error }}</p>
          <button @click="fetchCourses" class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
            Try Again
          </button>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <router-link
            v-for="course in bestCourses"
            :key="course.id"
            :to="`/Course_Description/${course.id}`"
            class="block course-card-link"
          >
            <div class="course-card bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100">
              <div class="relative course-image-container overflow-hidden h-48">
                <img
                  :src="course.image"
                  :alt="course.title"
                  class="course-image w-full h-full object-cover transition-transform duration-300"
                >
                <div class="absolute top-3 left-3">
                  <span class="category-badge bg-purple-600 text-white px-3 py-1 rounded-full text-xs font-semibold">
                    {{ course.category || 'Programming' }}
                  </span>
                </div>
                <div v-if="course.daysLeft" class="absolute top-3 right-3">
                  <span class="days-badge bg-red-500 text-white px-3 py-1 rounded-full text-xs font-semibold">
                    {{ course.daysLeft }}
                  </span>
                </div>
              </div>

              <div class="course-content p-5">
                <h3 class="course-title text-xl font-bold text-gray-900 mb-4 leading-tight line-clamp-2">
                  {{ course.title }}
                </h3>

                <div class="flex items-center space-x-6 mb-4 text-sm text-gray-600">
                  <div class="flex items-center space-x-1">
                    <svg class="w-4 h-4 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                    </svg>
                    <span class="font-medium">{{ course.video_count || '80' }} Materi </span>
                  </div>
                  <div class="flex items-center space-x-1">
                    <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-medium">{{ course.duration || '8 minggu' }}</span>
                  </div>
                </div>

                <div class="flex items-center mb-4 pb-4 border-b border-gray-100">
                  <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center mr-3">
                    <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                    </svg>
                  </div>
                  <div>
                    <p class="text-sm font-semibold text-gray-700">{{ course.instructor || 'Expert Instructor' }}</p>
                    <p class="text-xs text-gray-500">Instructor</p>
                  </div>
                </div>

                <div class="flex items-center justify-between">
                  <div>
                    <p v-if="course.original" class="text-sm text-gray-400 line-through mb-1">
                      Rp {{ formatPrice(course.original) }}
                    </p>
                    <p class="course-price text-2xl font-bold"
                       :class="course.price === 'Gratis' ? 'text-green-600' : 'text-purple-600'">
                      {{ course.price === 'Gratis' ? 'Gratis' : `Rp ${formatPrice(course.price)}` }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </router-link>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import axios from 'axios';
import { Swiper } from 'swiper';
import { Navigation, Pagination, Autoplay } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

export default {
  name: 'Homepage',
  data() {
    return {
      // Swiper instance
      swiper: null,

      // Courses data
      loading: false,
      error: null,
      courses: [],

      // Fallback courses
      fallbackCourses: [
        {
          id: 1,
          title: 'Full Stack Mobile Development : Membangun Aplikasi Manajemen Karyawan dengan Laravel & Kotlin',
          image: '/image/devfest-stockholm.png',
          price: '250000',
          original: '500000',
          instructor: 'John Doe',
          duration: '8 minggu',
          category: 'Mobile Development',
          videoCount: '154',
        },
        {
          id: 2,
          title: 'Complete Flutter Development : Build Event Apps',
          subtitle: 'Build Event Apps',
          image: '/image/devfest-stockholm.png',
          price: '450000',
          original: '900000',
          instructor: 'Jane Smith',
          duration: '6 minggu',
          category: 'Mobile Development',
          videoCount: '80',
        },
        {
          id: 3,
          title: 'Fundamental ReactJS untuk pemula',
          image: '/image/devfest-stockholm.png',
          price: 'Gratis',
          instructor: 'Mike Johnson',
          duration: '4 minggu',
          category: 'Web Programming',
          videoCount: '45',
        },
        {
          id: 4,
          title: 'Membangun Aplikasi Dating Real-Time dengan Flutter & Firebase',
          image: '/image/devfest-stockholm.png',
          daysLeft: '4 Hari Lagi',
          price: 'Gratis',
          instructor: 'Sarah Wilson',
          duration: '10 minggu',
          category: 'Mobile Development',
          videoCount: '120',
        }
      ]
    }
  },

  computed: {
    bestCourses() {
      const coursesToShow = this.courses.length > 0 ? this.courses : this.fallbackCourses;
      return coursesToShow.slice(0, 4);
    }
  },

  methods: {
    // Fixed Swiper initialization
    initializeSwiper() {
      this.$nextTick(() => {
        // Destroy existing swiper instance if exists
        if (this.swiper) {
          this.swiper.destroy(true, true);
          this.swiper = null;
        }

        const swiperElement = document.querySelector('.mySwiper');
        if (swiperElement) {
          this.swiper = new Swiper('.mySwiper', {
            modules: [Navigation, Pagination, Autoplay],

            // Core settings
            slidesPerView: 1,
            spaceBetween: 0,
            centeredSlides: true,
            loop: true,

            // Autoplay settings
            autoplay: {
              delay: 4000,
              disableOnInteraction: false,
              pauseOnMouseEnter: true,
            },

            // Pagination
            pagination: {
              el: '.swiper-pagination',
              clickable: true,
              dynamicBullets: true,
            },

            // Navigation
            navigation: {
              nextEl: '.swiper-button-next',
              prevEl: '.swiper-button-prev',
            },

            // Effects and transitions
            effect: 'slide',
            speed: 600,

            // Prevent clicks during transition
            preventClicks: true,
            preventClicksPropagation: true,

            // Touch settings
            touchRatio: 1,
            touchAngle: 45,
            grabCursor: true,

            // Breakpoints for responsive
            breakpoints: {
              320: {
                slidesPerView: 1,
                spaceBetween: 0,
              },
              640: {
                slidesPerView: 1,
                spaceBetween: 0,
              },
              1024: {
                slidesPerView: 1,
                spaceBetween: 0,
              }
            },

            // Event handlers
            on: {
              init: function() {
                console.log('Swiper initialized successfully');
              },
              slideChange: function() {
                console.log('Slide changed to:', this.activeIndex);
              },
              transitionStart: function() {
                // Add transition class
                this.el.classList.add('swiper-transitioning');
              },
              transitionEnd: function() {
                // Remove transition class
                this.el.classList.remove('swiper-transitioning');
              }
            }
          });
        }
      });
    },

    // Course methods
    async fetchCourses() {
      this.loading = true;
      this.error = null;

      try {
        const response = await axios.get('https://itqom-platform-aa0ffce6a276.herokuapp.com/api/courses');
        this.courses = response.data;
      } catch (error) {
        console.error('Gagal mengambil data kursus:', error);
        this.error = 'Gagal memuat data kursus. Silakan coba lagi.';
        this.courses = this.fallbackCourses;
      } finally {
        this.loading = false;
      }
    },

    navigateToCourse() {
      this.$router.push('/course');
    },

    formatPrice(price) {
      if (!price) return '0';
      return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
  },

  async mounted() {
    await this.fetchCourses();

    // Initialize Swiper after component is mounted
    setTimeout(() => {
      this.initializeSwiper();
    }, 100);

    // Initialize AOS if available
    this.$nextTick(() => {
      if (typeof AOS !== 'undefined') {
        AOS.init({
          duration: 1000,
          once: true
        });
      }
    });
  },

  beforeDestroy() {
    // Destroy Swiper instance to prevent memory leaks
    if (this.swiper) {
      this.swiper.destroy(true, true);
      this.swiper = null;
    }
  }
}
</script>

<style  lang="scss" src="../../../sass/homepage.scss">
/* Event Section Styling */
.event-section {
  min-height: 80vh;
  display: flex;
  align-items: center;
  overflow: hidden; /* Prevent overflow issues */
}

/* Badge Styling */
.badge {
  display: inline-block;
  transition: all 0.3s ease;
}

.badge:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
}

/* Title Highlight */
.highlight {
  position: relative;
}

.highlight::after {
  content: '';
  position: absolute;
  bottom: 2px;
  left: 0;
  right: 0;
  height: 3px;
  background: linear-gradient(90deg, #8b5cf6, #a78bfa);
  border-radius: 2px;
}

/* Fixed Swiper Styles */
.swiper-container {
  position: relative;
  overflow: hidden;
  border-radius: 1.5rem;
  max-width: 100%;
}

.mySwiper {
  width: 100%;
  height: 100%;
  overflow: hidden;
  border-radius: 1.5rem;
}

.mySwiper .swiper-wrapper {
  display: flex;
  align-items: center;
  height: auto;
}

.mySwiper .swiper-slide {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;
  height: auto;
  flex-shrink: 0;
  opacity: 1;
}

/* Slide Images */
.slide-image {
  width: 100%;
  height: 250px;
  object-fit: cover;
  border-radius: 1.5rem;
  display: block;
  max-width: 100%;
}

@media (min-width: 640px) {
  .slide-image {
    height: 300px;
  }
}

/* Custom Navigation Buttons */
.custom-nav-button {
  width: 44px !important;
  height: 44px !important;
  margin-top: -22px !important;
  color: white !important;
  background: rgba(139, 92, 246, 0.9) !important;
  border-radius: 50% !important;
  backdrop-filter: blur(10px) !important;
  border: 2px solid rgba(255, 255, 255, 0.2) !important;
  transition: all 0.3s ease !important;
  z-index: 10 !important;
  box-shadow: 0 2px 10px rgba(139, 92, 246, 0.3) !important;
}

.custom-nav-button:hover {
  background: rgba(139, 92, 246, 1) !important;
  transform: scale(1.1) !important;
  box-shadow: 0 4px 20px rgba(139, 92, 246, 0.5) !important;
}

.custom-nav-button::after {
  font-size: 16px !important;
  font-weight: bold !important;
}

.swiper-button-prev {
  left: 15px !important;
}

.swiper-button-next {
  right: 15px !important;
}

/* Custom Pagination */
.mySwiper .swiper-pagination {
  bottom: 15px !important;
  z-index: 10 !important;
}

.mySwiper .swiper-pagination-bullet {
  width: 10px !important;
  height: 10px !important;
  background: rgba(255, 255, 255, 0.7) !important;
  opacity: 1 !important;
  transition: all 0.3s ease !important;
  margin: 0 4px !important;
}

.mySwiper .swiper-pagination-bullet-active {
  background: #8b5cf6 !important;
  width: 24px !important;
  border-radius: 12px !important;
  box-shadow: 0 2px 8px rgba(139, 92, 246, 0.4) !important;
}

/* Transition States */
.swiper-transitioning {
  pointer-events: none;
}

/* Features List */
.features ul {
  list-style: none;
  padding: 0;
}

.features li {
  transition: all 0.2s ease;
}

.features li:hover {
  transform: translateX(4px);
  color: #4f46e5;
}

/* Explore Button */
.explore-btn {
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
}

.explore-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(139, 92, 246, 0.4);
}

/* Course Cards */
.best-course-section {
  background: #f9fafb;
}

.course-card {
  transition: all 0.3s ease;
}

.course-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 28px rgba(0, 0, 0, 0.12);
}

.course-card:hover .course-image {
  transform: scale(1.05);
}

.course-card:hover .course-title {
  color: #7c3aed;
}

.course-card-link {
  text-decoration: none;
  color: inherit;
}

.course-card-link:hover {
  text-decoration: none;
  color: inherit;
}

/* Line clamp utility */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Loading spinner animation */
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}

/* Responsive Design */
@media (max-width: 1024px) {
  .event-section {
    min-height: auto;
    padding: 4rem 0;
  }
}

@media (max-width: 768px) {
  .title {
    font-size: 2.5rem;
  }

  .features {
    grid-template-columns: 1fr;
  }

  .course-card {
    margin-bottom: 1rem;
  }

  .course-card:hover {
    transform: translateY(-2px);
  }

  .course-title {
    font-size: 1.125rem;
  }
}
</style>



