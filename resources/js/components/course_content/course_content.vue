<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Loading State -->
    <div v-if="loading" class="flex items-center justify-center min-h-screen">
      <div class="animate-spin rounded-full h-32 w-32 border-b-2 border-purple-500"></div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="flex items-center justify-center min-h-screen">
      <div class="text-center">
        <div class="text-6xl mb-4">⚠️</div>
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Terjadi Kesalahan</h2>
        <p class="text-gray-600 mb-4">{{ error }}</p>
        <button
          @click="retryLoad"
          class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition"
        >
          Coba Lagi
        </button>
        <router-link
          to="/Dashboard"
          class="ml-4 bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition"
        >
          Kembali ke Dashboard
        </router-link>
      </div>
    </div>

    <!-- Main Content -->
    <div v-else class="flex">
      <!-- Sidebar Navigation -->
      <div class="w-80 bg-white shadow-lg fixed h-full overflow-y-auto">
        <div class="p-6 border-b">
          <router-link to="/Dashboard" class="flex items-center text-purple-600 hover:text-purple-800 mb-4">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke Dashboard
          </router-link>
          <h2 class="text-xl font-bold text-gray-800">{{ courseData.courseDescription?.title || 'Course' }}</h2>
          <p class="text-sm text-gray-600">{{ courseData.totalMateris }} Materi</p>

          <!-- Progress Bar -->
          <div class="mt-4">
            <div class="flex justify-between text-sm text-gray-600 mb-1">
              <span>Progress</span>
              <span>{{ Math.round(progress) }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div
                class="bg-gradient-to-r from-purple-500 to-indigo-500 h-2 rounded-full transition-all duration-300"
                :style="{ width: progress + '%' }"
              ></div>
            </div>
            <p class="text-xs text-gray-500 mt-1">
              {{ completedMateris.length }} dari {{ courseData.totalMateris }} selesai
            </p>
          </div>
        </div>

        <!-- Material List -->
        <div class="p-4">
          <h3 class="font-semibold text-gray-800 mb-3">Daftar Materi</h3>
          <div
            v-for="(materi, index) in courseData.materis"
            :key="materi.slug"
            class="mb-2"
          >
            <button
              @click="selectMateri(materi)"
              :class="[
                'w-full text-left p-3 rounded-lg transition-all',
                currentMateri?.slug === materi.slug
                  ? 'bg-purple-100 border-l-4 border-purple-500 text-purple-800'
                  : 'hover:bg-gray-100 text-gray-700',
                completedMateris.includes(materi.slug) ? 'bg-green-50' : ''
              ]"
            >
              <div class="flex items-center">
                <span class="flex-shrink-0 w-6 h-6 rounded-full text-xs flex items-center justify-center mr-3"
                      :class="completedMateris.includes(materi.slug) ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-600'">
                  {{ completedMateris.includes(materi.slug) ? '✓' : index + 1 }}
                </span>
                <span class="font-medium text-sm">{{ materi.judul }}</span>
              </div>
            </button>
          </div>
        </div>
      </div>

      <!-- Main Content Area -->
      <div class="ml-80 flex-1 p-8">
        <div v-if="currentMateri" class="max-w-4xl mx-auto">
          <!-- Header -->
          <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <div class="flex justify-between items-start mb-4">
              <div>
                <span class="inline-block bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full mb-2">
                  Materi {{ currentMateri.urutan }}
                </span>
                <h1 class="text-3xl font-bold text-gray-800">{{ currentMateri.judul }}</h1>
              </div>
              <button
                @click="toggleComplete"
                :class="[
                  'px-4 py-2 rounded-lg font-semibold transition-all',
                  isCurrentMateriCompleted
                    ? 'bg-green-100 text-green-800 border border-green-300'
                    : 'bg-purple-600 text-white hover:bg-purple-700'
                ]"
              >
                {{ isCurrentMateriCompleted ? '✓ Selesai' : 'Tandai Selesai' }}
              </button>
            </div>
          </div>

          <!-- Content -->
          <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
            <div class="prose max-w-none" v-html="currentMateri.konten"></div>
          </div>

          <!-- Navigation -->
          <div class="flex justify-between items-center bg-white rounded-lg shadow-lg p-6">
            <button
              v-if="prevMateri"
              @click="selectMateri(prevMateri)"
              class="flex items-center px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
              </svg>
              {{ prevMateri.judul }}
            </button>
            <div v-else></div>

            <div class="text-center">
              <span class="text-sm text-gray-500">
                {{ currentMateriIndex + 1 }} dari {{ courseData.materis?.length || 0 }}
              </span>
            </div>

            <button
              v-if="nextMateri"
              @click="selectMateri(nextMateri)"
              class="flex items-center px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition"
            >
              {{ nextMateri.judul }}
              <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
              </svg>
            </button>
            <div v-else class="flex items-center">
              <span class="text-green-600 font-semibold">🎉 Course Selesai!</span>
            </div>
          </div>

          <!-- Course Completion Celebration -->
          <div v-if="isCourseCompleted" class="mt-6 bg-gradient-to-r from-green-100 to-blue-100 rounded-lg p-6 text-center">
            <div class="text-6xl mb-4">🎊</div>
            <h2 class="text-2xl font-bold text-green-800 mb-2">Selamat! Kamu Telah Menyelesaikan Course!</h2>
            <p class="text-gray-700 mb-4">
              Kamu telah menyelesaikan <strong>{{ courseData.courseDescription?.title }}</strong>
            </p>
            <div class="flex gap-3 justify-center">
              <router-link
                to="/Dashboard"
                class="bg-purple-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-purple-700 transition"
              >
                Kembali ke Dashboard
              </router-link>
              <a
                href="/certificate"
                class="bg-green-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-green-700 transition"
              >
                Lihat Sertifikat
              </a>
            </div>
          </div>
        </div>

        <!-- No Material Selected -->
        <div v-else class="text-center py-20">
          <div class="text-6xl mb-4">📚</div>
          <h2 class="text-2xl font-bold text-gray-800 mb-2">Pilih Materi untuk Memulai</h2>
          <p class="text-gray-600">Klik pada salah satu materi di sidebar untuk memulai belajar</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'CourseContent',
  computed: {
    courseDescriptionId() {
      return this.$route.params.courseDescriptionId || this.$route.params.id;
    },
    currentMateriIndex() {
      if (!this.currentMateri || !this.courseData.materis) return -1;
      return this.courseData.materis.findIndex(m => m.slug === this.currentMateri.slug);
    },
    prevMateri() {
      const index = this.currentMateriIndex;
      if (index <= 0) return null;
      return this.courseData.materis[index - 1];
    },
    nextMateri() {
      const index = this.currentMateriIndex;
      if (index >= this.courseData.materis.length - 1) return null;
      return this.courseData.materis[index + 1];
    },
    isCurrentMateriCompleted() {
      return this.currentMateri && this.completedMateris.includes(this.currentMateri.slug);
    },
    progress() {
      if (!this.courseData.materis || this.courseData.materis.length === 0) return 0;
      return (this.completedMateris.length / this.courseData.materis.length) * 100;
    },
    isCourseCompleted() {
      return this.courseData.materis &&
             this.courseData.materis.length > 0 &&
             this.completedMateris.length === this.courseData.materis.length;
    }
  },
  data() {
    return {
      loading: true,
      error: null,
      courseData: {
        courseDescription: null,
        materis: [],
        totalMateris: 0,
        course_stats: null
      },
      currentMateri: null,
      completedMateris: [], // Array of completed material slugs
    };
  },
  async mounted() {
    console.log('🎯 CourseContent mounted - Course Description ID:', this.courseDescriptionId);
    await this.loadCourseContent();
    this.loadProgress();
    this.setupProgressListener();
  },
  beforeUnmount() {
    // Clean up event listeners
    window.removeEventListener('courseProgressUpdated', this.handleProgressUpdate);
  },
  methods: {
    // ===== SETUP PROGRESS LISTENER =====
    setupProgressListener() {
      // Listen for progress updates from dashboard or other components
      window.addEventListener('courseProgressUpdated', this.handleProgressUpdate);
    },

    handleProgressUpdate(event) {
      const { courseId, progress } = event.detail;
      if (courseId == this.courseDescriptionId) {
        console.log('📡 Received progress update from external source:', progress);
        if (progress.completedMateris) {
          this.completedMateris = progress.completedMateris;
        }
      }
    },

    // ===== ENHANCED COURSE LOADING =====
    async loadCourseContent() {
      try {
        this.loading = true;
        this.error = null;

        // Set up authentication if token exists
        const token = localStorage.getItem('auth_token') || localStorage.getItem('authToken');
        if (token) {
          axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        }

        console.log('🔍 Loading course content for ID:', this.courseDescriptionId);

        // Use the enhanced API endpoint
        const response = await axios.get(`http://localhost:8000/api/course-content/course/${this.courseDescriptionId}`);

        console.log('📡 Course content response:', response.data);

        if (response.data.success) {
          this.courseData = response.data.data;

          // Ensure materis is an array
          if (!Array.isArray(this.courseData.materis)) {
            this.courseData.materis = [];
          }

          // Auto-select first material if available
          if (this.courseData.materis && this.courseData.materis.length > 0) {
            this.currentMateri = this.courseData.materis[0];
          }

          // Enhanced logging
          console.log('✅ Course data loaded successfully:', {
            title: this.courseData.courseDescription?.title,
            totalMateris: this.courseData.totalMateris,
            materialsSource: this.courseData.course_stats?.materials_source,
            hasContent: this.courseData.course_stats?.has_content
          });

          // Notify dashboard about course structure
          this.notifyDashboardCourseStructure();

        } else {
          throw new Error(response.data.message || 'Gagal memuat data course');
        }

      } catch (error) {
        console.error('❌ Error loading course content:', error);

        if (error.response?.status === 404) {
          this.error = 'Course tidak ditemukan atau Anda tidak memiliki akses ke course ini.';
        } else if (error.response?.status === 401) {
          this.error = 'Sesi Anda telah berakhir. Silakan login kembali.';
          // Redirect to login after 2 seconds
          setTimeout(() => {
            localStorage.removeItem('auth_token');
            localStorage.removeItem('authToken');
            localStorage.removeItem('user_data');
            this.$router.push('/login');
          }, 2000);
        } else {
          this.error = error.response?.data?.message || 'Terjadi kesalahan saat memuat course. Silakan coba lagi.';
        }
      } finally {
        this.loading = false;
      }
    },

    // ===== NOTIFY DASHBOARD ABOUT COURSE STRUCTURE =====
    notifyDashboardCourseStructure() {
      const courseStructure = {
        courseId: this.courseDescriptionId,
        title: this.courseData.courseDescription?.title,
        totalMaterials: this.courseData.totalMateris,
        hasContent: this.courseData.course_stats?.has_content,
        materialsSource: this.courseData.course_stats?.materials_source
      };

      // Broadcast course structure to dashboard
      window.dispatchEvent(new CustomEvent('courseStructureLoaded', {
        detail: courseStructure
      }));

      console.log('📡 Course structure broadcasted to dashboard:', courseStructure);
    },

    // ===== ENHANCED MATERIAL SELECTION =====
    selectMateri(materi) {
      if (!materi) return;

      this.currentMateri = materi;
      this.saveProgress();

      // Log material selection
      console.log('📖 Selected material:', {
        title: materi.judul,
        order: materi.urutan,
        slug: materi.slug
      });

      // Scroll to top
      window.scrollTo({ top: 0, behavior: 'smooth' });
    },

    // ===== ENHANCED PROGRESS SAVING =====
    saveProgress() {
      const courseId = this.courseDescriptionId;
      const progressKey = `course_progress_${courseId}`;

      const progressData = {
        completedMateris: this.completedMateris,
        currentMateri: this.currentMateri?.slug || null,
        lastUpdated: new Date().toISOString(),
        // Enhanced data for dashboard integration
        course_id: courseId,
        course_title: this.courseData.courseDescription?.title,
        total_materials: this.courseData.totalMateris || 0,
        completed_count: this.completedMateris.length,
        progress_percentage: this.progress,
        materials_source: this.courseData.course_stats?.materials_source || 'unknown'
      };

      try {
        localStorage.setItem(progressKey, JSON.stringify(progressData));

        // Enhanced dashboard notification
        window.dispatchEvent(new CustomEvent('courseProgressUpdated', {
          detail: {
            courseId: courseId,
            progress: progressData,
            timestamp: new Date().toISOString()
          }
        }));

        // Also send to dashboard sync event
        window.dispatchEvent(new CustomEvent('courseProgressSync', {
          detail: {
            courseId: courseId,
            completed: this.completedMateris.length,
            total: this.courseData.totalMateris || 0,
            percentage: this.progress,
            completedMateris: this.completedMateris,
            courseTitle: this.courseData.courseDescription?.title,
            lastAccessed: new Date().toISOString()
          }
        }));

        console.log('💾 Enhanced progress saved and broadcasted:', progressData);
      } catch (error) {
        console.error('❌ Error saving progress:', error);
      }
    },

    // ===== ENHANCED PROGRESS LOADING =====
    loadProgress() {
      const courseId = this.courseDescriptionId;
      const progressKey = `course_progress_${courseId}`;

      try {
        const savedProgress = localStorage.getItem(progressKey);
        if (savedProgress) {
          const progressData = JSON.parse(savedProgress);
          this.completedMateris = progressData.completedMateris || [];

          // Restore current material if saved and materials are loaded
          if (progressData.currentMateri && this.courseData.materis && this.courseData.materis.length > 0) {
            const savedMateri = this.courseData.materis.find(m => m.slug === progressData.currentMateri);
            if (savedMateri) {
              this.currentMateri = savedMateri;
            }
          }

          console.log('📚 Progress loaded:', {
            completed: this.completedMateris.length,
            total: this.courseData.totalMateris,
            percentage: Math.round(this.progress),
            lastUpdated: progressData.lastUpdated
          });

          // Notify dashboard immediately about loaded progress
          this.saveProgress();
        }
      } catch (error) {
        console.error('❌ Error loading progress:', error);
      }
    },

    // ===== ENHANCED COMPLETION TOGGLE =====
    toggleComplete() {
      if (!this.currentMateri) return;

      const slug = this.currentMateri.slug;
      const index = this.completedMateris.indexOf(slug);

      if (index > -1) {
        // Remove from completed
        this.completedMateris.splice(index, 1);
        console.log('❌ Material unmarked as completed:', this.currentMateri.judul);
      } else {
        // Add to completed
        this.completedMateris.push(slug);
        console.log('✅ Material marked as completed:', this.currentMateri.judul);

        // Visual feedback
        this.$nextTick(() => {
          const button = event.target;
          if (button) {
            button.style.transform = 'scale(1.05)';
            setTimeout(() => {
              button.style.transform = 'scale(1)';
            }, 200);
          }
        });

        // Auto-navigate to next material if available and not at the end
        if (this.nextMateri && !this.isCourseCompleted) {
          setTimeout(() => {
            this.selectMateri(this.nextMateri);
          }, 800);
        }

        // Check if course is completed
        if (this.isCourseCompleted) {
          this.celebrateCompletion();
        }
      }

      // Save progress after update
      this.saveProgress();

      // Enhanced logging
      console.log('📊 Progress updated:', {
        completed: this.completedMateris.length,
        total: this.courseData.totalMateris,
        percentage: Math.round(this.progress),
        courseCompleted: this.isCourseCompleted
      });
    },

    // ===== COURSE COMPLETION CELEBRATION =====
    celebrateCompletion() {
      console.log('🎉 Course completed! Triggering celebration...');

      // Trigger celebration animation
      setTimeout(() => {
        // Scroll to completion message
        const completionElement = document.querySelector('.bg-gradient-to-r.from-green-100');
        if (completionElement) {
          completionElement.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
          });
        }
      }, 1000);

      // Notify dashboard about course completion
      window.dispatchEvent(new CustomEvent('courseCompleted', {
        detail: {
          courseId: this.courseDescriptionId,
          courseTitle: this.courseData.courseDescription?.title,
          completedAt: new Date().toISOString(),
          totalMaterials: this.courseData.totalMateris
        }
      }));

      // Update user achievements
      this.updateAchievements();
    },

    // ===== UPDATE ACHIEVEMENTS =====
    updateAchievements() {
      try {
        let achievements = JSON.parse(localStorage.getItem('user_achievements') || '[]');

        const completionAchievement = {
          id: `course_${this.courseDescriptionId}_completed`,
          type: 'course_completion',
          courseId: this.courseDescriptionId,
          courseTitle: this.courseData.courseDescription?.title,
          completedAt: new Date().toISOString(),
          badge: '🎓',
          title: 'Course Completed',
          description: `Menyelesaikan course "${this.courseData.courseDescription?.title}"`
        };

        achievements.push(completionAchievement);
        localStorage.setItem('user_achievements', JSON.stringify(achievements));

        console.log('🏆 Achievement unlocked:', completionAchievement);
      } catch (error) {
        console.error('❌ Error updating achievements:', error);
      }
    },

    // ===== RETRY LOAD =====
    async retryLoad() {
      await this.loadCourseContent();
      this.loadProgress();
    },

    // ===== FORMAT DATE =====
    formatDate(dateString) {
      if (!dateString) return '';
      const date = new Date(dateString);
      return date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
      });
    }
  },

  // ===== WATCHERS =====
  watch: {
    // Watch for course data changes
    'courseData.materis': {
      handler(newMateris) {
        if (newMateris && newMateris.length > 0 && !this.currentMateri) {
          this.currentMateri = newMateris[0];
          console.log('🎯 Auto-selected first material:', this.currentMateri.judul);
        }
        // Notify dashboard about materials structure change
        this.notifyDashboardCourseStructure();
      },
      deep: true
    },

    // Watch for progress changes
    completedMateris: {
      handler(newCompleted, oldCompleted) {
        if (newCompleted.length !== oldCompleted.length) {
          console.log('📈 Progress changed:', {
            from: oldCompleted.length,
            to: newCompleted.length,
            total: this.courseData.totalMateris
          });
        }
      },
      deep: true
    }
  }
};
</script>

<style scoped>
/* Custom styles for content */
.prose {
  line-height: 1.7;
}

.prose h1, .prose h2, .prose h3, .prose h4 {
  color: #374151;
  font-weight: 600;
  margin-top: 2rem;
  margin-bottom: 1rem;
}

.prose h1 {
  font-size: 2rem;
}

.prose h2 {
  font-size: 1.5rem;
}

.prose h3 {
  font-size: 1.25rem;
}

.prose p {
  margin-bottom: 1.5rem;
  color: #4B5563;
}

.prose ul, .prose ol {
  margin-bottom: 1.5rem;
  padding-left: 1.5rem;
}

.prose li {
  margin-bottom: 0.5rem;
}

.prose code {
  background-color: #F3F4F6;
  padding: 0.25rem 0.5rem;
  border-radius: 0.25rem;
  font-family: 'Courier New', monospace;
  font-size: 0.875rem;
}

.prose pre {
  background-color: #1F2937;
  color: #F9FAFB;
  padding: 1rem;
  border-radius: 0.5rem;
  overflow-x: auto;
  margin: 1.5rem 0;
}

.prose pre code {
  background-color: transparent;
  padding: 0;
  color: inherit;
}

.prose blockquote {
  border-left: 4px solid #8B5CF6;
  padding-left: 1rem;
  margin: 1.5rem 0;
  font-style: italic;
  color: #6B7280;
}

.prose img {
  max-width: 100%;
  height: auto;
  border-radius: 0.5rem;
  margin: 1.5rem 0;
}

.prose table {
  width: 100%;
  border-collapse: collapse;
  margin: 1.5rem 0;
}

.prose th, .prose td {
  border: 1px solid #E5E7EB;
  padding: 0.75rem;
  text-align: left;
}

.prose th {
  background-color: #F9FAFB;
  font-weight: 600;
}

/* Loading animation */
@keyframes spin {
  to { transform: rotate(360deg); }
}

.animate-spin {
  animation: spin 1s linear infinite;
}

/* Smooth transitions */
.transition-all {
  transition: all 0.3s ease;
}

/* Custom scrollbar for sidebar */
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: #f1f1f1;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}

/* Celebration animation */
@keyframes celebrate {
  0%, 100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.1);
  }
}

.celebration {
  animation: celebrate 0.6s ease-in-out;
}
</style>
